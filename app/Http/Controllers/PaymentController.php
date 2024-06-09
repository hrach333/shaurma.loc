<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $shopId = env('YOOKASSA_SHOP_ID');
        $secretKey = env('YOOKASSA_SECRET_KEY');
        $description = 'Заказ №' . $request->order_id;

        $paymentData = [
            'amount' => [
                'value' => number_format($request->total, 2, '.', ''),
                'currency' => 'RUB'
            ],
            'capture' => true,
            'description' => $description
        ];

        if ($request->payment_method == 'sbp') {
            $paymentData['payment_method_data'] = [
                'type' => 'sbp'
            ];
            $paymentData['confirmation'] = [
                'type' => 'qr'
            ];
        } elseif ($request->payment_method == 'bank_card') {
            $paymentData['payment_method_data'] = [
                'type' => 'bank_card',
                'card' => [
                    'number' => $request->card_number,
                    'expiry_month' => substr($request->card_expiry, 0, 2),
                    'expiry_year' => substr($request->card_expiry, -2),
                    'cvc' => $request->card_cvc
                ]
            ];
        }

        $response = Http::withBasicAuth($shopId, $secretKey)
            ->withHeaders([
                'Idempotence-Key' => uniqid('', true),
                'Content-Type' => 'application/json'
            ])
            ->post('https://api.yookassa.ru/v3/payments', $paymentData);

        // Логируем ответ для диагностики
        Log::debug($response->body());

        if ($response->failed()) {
            // Логируем ошибку
            Log::error('Ошибка при создании платежа: ' . $response->body());

            // Возвращаем ошибку клиенту
            return response()->json(['error' => 'Не удалось создать платеж. Пожалуйста, свяжитесь с поддержкой.'], 500);
        }

        $data = $response->json();

        return response()->json($data);
    }

    public function checkPaymentStatus(Request $request)
    {
        $paymentId = $request->input('paymentId');
        $shopId = env('YOOKASSA_SHOP_ID');
        $secretKey = env('YOOKASSA_SECRET_KEY');

        $response = Http::withBasicAuth($shopId, $secretKey)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->get("https://api.yookassa.ru/v3/payments/{$paymentId}");

        // Логируем ответ для диагностики
        Log::debug($response->body());

        if ($response->failed()) {
            // Логируем ошибку
            Log::error('Ошибка при проверке статуса платежа: ' . $response->body());

            // Возвращаем ошибку клиенту
            return response()->json(['error' => 'Не удалось проверить статус платежа. Пожалуйста, свяжитесь с поддержкой.'], 500);
        }

        $data = $response->json();

        return response()->json($data);
    }
}
