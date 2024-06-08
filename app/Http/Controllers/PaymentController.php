<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function getSbpParticipants()
    {
        $shopId = env('YOOKASSA_SHOP_ID');
        $secretKey = env('YOOKASSA_SECRET_KEY');

        $response = Http::withBasicAuth($shopId, $secretKey)->get('https://api.yookassa.ru/v3/sbp-participants');

        return $response->json();
    }

    public function createPayment(Request $request)
    {
        $method = $request->input('method');
        $shopId = env('YOOKASSA_SHOP_ID');
        $secretKey = env('YOOKASSA_SECRET_KEY');
        $idempotenceKey = uniqid('', true); // Генерация уникального идемпотентного ключа

        $paymentData = [
            'amount' => [
                'value' => '100.00', // Укажите сумму платежа
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => url('/order/complete'), // URL для завершения платежа
            ],
            'capture' => true,
            'description' => 'Оплата заказа',
        ];

        if ($method === 'card') {
            $token = $request->input('token');
            $paymentData['payment_method_data'] = [
                'type' => 'bank_card',
                'card' => [
                    'token' => $token,
                ],
            ];
        } elseif ($method === 'sbp') {
            $participantId = $request->input('participantId');
            $phone = $request->input('phone');
            $paymentData['payment_method_data'] = [
                'type' => 'sbp',
                'phone' => $phone,
                'participant_id' => $participantId
            ];
        }

        $response = Http::withBasicAuth($shopId, $secretKey)->withHeaders([
            'Idempotence-Key' => $idempotenceKey, // Добавление идемпотентного ключа в заголовок
        ])->post('https://api.yookassa.ru/v3/payments', $paymentData);

        $responseBody = $response->json();

        if (isset($responseBody['confirmation'])) {
            return response()->json([
                'status' => 'success',
                'redirect_url' => $responseBody['confirmation']['confirmation_url']
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $responseBody['description']
            ]);
        }
    }
}
