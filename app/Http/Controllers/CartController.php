<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    //


    public function addToCart(Request $request)
    {
           // Получаем id продукта из запроса
    $productId = $request->productId;
    $sessionId = session()->getId();
    // Проверяем, авторизован ли пользователь
    if (auth()->check()) {
        // Если пользователь авторизован, получаем его id
        $userId = auth()->id();

        // Обновляем записи корзины, привязанные к сессии, если они есть

        Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->update(['user_id' => $userId]);

        // Проверяем, есть ли уже такой продукт в корзине для данного пользователя
        $existingCartItem = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingCartItem) {
            // Если продукт уже есть в корзине, увеличиваем его количество
            $existingCartItem->quantity += 1;
            $existingCartItem->save();
        } else {
            // Если продукта еще нет в корзине, создаем новую запись
            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => 1,
                'session_id' => $sessionId
            ]);
        }
    } else {
        // Если пользователь не авторизован, сохраняем продукт с привязкой к его сессии
       // $sessionId = session()->getId();

        // Проверяем, есть ли уже такой продукт в корзине для данной сессии
        $existingCartItem = Cart::where('session_id', $sessionId)
            ->where('product_id', $productId)
            ->first();

        if ($existingCartItem) {
            // Если продукт уже есть в корзине, увеличиваем его количество
            $existingCartItem->quantity += 1;
            $existingCartItem->save();
        } else {
            // Если продукта еще нет в корзине, создаем новую запись
            Cart::create([
                'user_id' => null,
                'product_id' => $productId,
                'quantity' => 1,
                'session_id' => $sessionId
            ]);
        }
    }

    // Получаем общее количество товаров в корзине
    $totalCartItems = auth()->check()
        ? Cart::where('user_id', auth()->id())->sum('quantity')
        : Cart::where('session_id', session()->getId())->sum('quantity');

    // Сохраняем общее количество товаров в сессию
    session()->put('totalCartItems', $totalCartItems);

    // Возвращаем общее количество товаров в корзине
    return response()->json(['totalCartItems' => $totalCartItems]);
}
}
