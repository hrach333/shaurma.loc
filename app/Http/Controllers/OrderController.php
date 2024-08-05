<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationCanceled;
use YooKassa\Model\Notification\NotificationWaitingForCapture;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        //dd('выполнен create');
        // Валидация данных
        $validated = $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);
        //Log::info('проверка дынных нв приход: ', [$request->all()]);
        // Создание заказа
        $order = new Order();
        $order->user_id = auth()->check() ? auth()->id() : null;
        $order->session_id = session('id_token');
        $order->status = 'pending';
        $order->total = $request->total;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->save();

        // Интеграция с ЮKassa для создания платежа
        $shopId = env('YOOKASSA_SHOP_ID');
        $secretKey = env('YOOKASSA_SECRET_KEY');
        $date = explode('/', $request->card_expiry);
        $month = $date[0];
        $year = $date[1];
        $client = new Client();
        /** @var object $client */
        $client->setAuth($shopId, $secretKey);
        $result = $client->createPayment(
            array(
                'amount' => array(
                    'value' => number_format($order->total, 2, '.', ''),
                    'currency' => 'RUB',
                ),
                'payment_method_data' => array(
                    'type' => 'bank_card',
                    'card' => array(
                        'cardholder' => 'MR CARDHOLDER',
                        'csc' => $request->cvc,
                        'expiry_month' => $month,
                        'expiry_year' => $year,
                        'number' => str_replace(" ", "", $request->card_number),
                    ),
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => route('order.complete'), // URL для перенаправления после завершения оплаты
                ),
                'description' => 'Order #' . $order->id,
            ),
            uniqid('', true)
        );

        //print_r($result);
        //Log::info('YooKassa Response: ', $result); // Логирование ответа для отладки
        $status = $result->getStatus();
        /*
         * pending — ожидает обработки;
           waiting_for_capture — платеж успешно прошел, ожидает подтверждения операции;
           succeeded — платеж успешно завершен;
           canceled — платеж отменен.
         */
        $paymentId = $result->getId();
        $amountInfo = $result->getAmount();
        $amount = $amountInfo->getValue();
        $currency = $amountInfo->getCurrency();
        $description = $result->getDescription();
        $order->payment_id = $paymentId;
        $order->save();
        Log::info("Статус " . $status);
        if ($status === NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE) {
            try {
                $capturedPayment = $client->capturePayment([
                    'amount' => [
                        'value' => $amount,
                        'currency' => $currency,
                    ],
                ], $paymentId, uniqid('', true));
                return response()->json(['message' => "Платеж подтвержден: " . $capturedPayment->getId()], 201);
            } catch (\Exception $e) {
                return response()->json(['error' => "Ошибка при подтверждении платежа: " . $e->getMessage()]);
            }
        } elseif ($status === NotificationEventType::PAYMENT_SUCCEEDED) {
            $order->status = $status;
            $order->save();
            return response()->json(['message' => "Платеж выполнен: " . $result->getId(), 'ammount' => $amount], 201);
        } elseif ($status === 'pending'){
            $confirmation = $result->getConfirmation();
            $url = $confirmation->getConfirmationUrl();
            return response()->json(['message' => 'Переходите для подтверждение ', 'url' => $url]);
        } elseif ($status == 'succeeded') {
            $order->status = $status;
            $order->save();
            return response()->json(['message' => 'Платеж выполнен  удачно ' . $paymentId]);
        }
        else {
            // Логирование ошибки, если 'confirmation' отсутствует в ответе
            Log::info('YooKassa Error: ', [$result->getStatus()]);

            return response()->json(['message' => 'Error creating payment', 'error' => $result->getStatus()], 500);
        }


    }

    // Метод для отображения формы заказа
    public function showForm()
    {
        return view('order.form', ['total' => $this->getTotal()]);
    }

    private function getTotal()
    {
        // Рассчитываем общую сумму заказа
        $total = 0;
        $cartItems = auth()->check()
            ? Cart::where('user_id', auth()->id())->with('product')->get()
            : Cart::where('session_id', session('id_token'))->with('product')->get();

        foreach ($cartItems as $item) {
            $total += $item->quantity * $item->product->price;
        }

        return $total;
    }

    public function complete(Request $request)
    {
        $paymentId = $request->input('paymentId');
        $order = Order::where('payment_id', $paymentId)->first();

        if ($order) {
            $order->status = 'completed';
            $order->save();
            return view('order.complete', ['order' => $order]);
        }

        return view('order.complete', ['error' => 'Order not found or already completed']);
    }
}
