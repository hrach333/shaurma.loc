<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
class OrderController extends Controller
{
    public function create(Request $request)
    {
        // Валидация данных
        $validated = $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

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

        $idempotenceKey = uniqid('', true); // Генерация уникального идемпотентного ключа

        $response = Http::withBasicAuth($shopId, $secretKey)->withHeaders([
            'Idempotence-Key' => $idempotenceKey, // Добавление идемпотентного ключа в заголовок
        ])->post('https://api.yookassa.ru/v3/payments', [
            'amount' => [
                'value' => number_format($order->total, 2, '.', ''),
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => url('/order/complete'), // URL для перенаправления после завершения оплаты
            ],
            'capture' => true,
            'description' => 'Order #' . $order->id,
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        //$responseBody = $response->json();
        $responseBody = $response->json();

        Log::info('YooKassa Response: ', $responseBody); // Логирование ответа для отладки

        if (isset($responseBody['confirmation'])) {
            $order->payment_id = $responseBody['id'];
            $order->save();

            return response()->json(['message' => 'Order created successfully', 'order' => $order, 'payment_url' => $responseBody['confirmation']['confirmation_url']], 201);
        } else {
            // Логирование ошибки, если 'confirmation' отсутствует в ответе
            Log::error('YooKassa Error: ', $responseBody);

            return response()->json(['message' => 'Error creating payment', 'error' => $responseBody], 500);
        }
        $order->payment_id = $responseBody['id'];
        $order->save();

        return response()->json(['message' => 'Order created successfully', 'order' => $order, 'payment_url' => $responseBody['confirmation']['confirmation_url']], 201);
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
