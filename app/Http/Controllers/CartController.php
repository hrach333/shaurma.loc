<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    //
    public function viewCart(Request $request)
    {
        $sessionId = session('id_token');
        if (auth()->check()) {
            $userId = auth()->id();
            $cartItems = Cart::with('product')
                ->where(function ($query) use ($userId, $sessionId) {
                    $query->where('user_id', $userId)
                        ->orWhere('session_id', $sessionId);
                })
                ->paginate(5);

        } else {
            $userId = 0;
            $cartItems = Cart::with('product')
                ->where(function ($query) use ($userId, $sessionId) {
                    $query->where('user_id', $userId)
                        ->orWhere('session_id', $sessionId);
                })
                ->paginate(5);

        }
        $carts = Cart::with('product')
            ->where(function ($query) use ($userId, $sessionId) {
                $query->where('user_id', $userId)
                    ->orWhere('session_id', $sessionId);
            })
            ->get();
        $priceTotal = 0;
        foreach ($carts as $item) {
            $priceTotal += $item->product->price * $item->quantity;
        }
        return view('cart', compact('cartItems', 'priceTotal'));
    }

    public function addToCart(Request $request)
    {
        // Получаем id продукта из запроса
        $productId = $request->productId;
        $sessionId = session('id_token');
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
            : Cart::where('session_id', session('id_token'))->sum('quantity');

        // Сохраняем общее количество товаров в сессию
        session()->put('totalCartItems', $totalCartItems);

        // Возвращаем общее количество товаров в корзине
        return response()->json(['totalCartItems' => $totalCartItems]);
    }
    public function getItemCount()
    {
        $quantity = 0;

        if (auth()->check()) {
            // Получаем общее количество товаров в корзине для авторизованного пользователя
            $quantity = Cart::where('user_id', auth()->id())->sum('quantity');
        } else {
            // Получаем общее количество товаров в корзине для текущей сессии
            $quantity = Cart::where('session_id', session('id_token'))->sum('quantity');
        }
        return response()->json(['count' => $quantity]);
    }

    public function removeFromCart($id)
    {
        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $cartItem->delete();

        // Обновляем количество товаров в корзине после удаления
        $quantity = 0;
        if (auth()->check()) {
            $quantity = Cart::where('user_id', auth()->id())->sum('quantity');
        } else {
            $quantity = Cart::where('session_id', session('id_token'))->sum('quantity');
        }

        return response()->json(['totalCartItems' => $quantity]);
    }
    public function updateQuantity(Request $request)
    {
        $itemId = $request->itemId;
        $newQuantity = $request->quantity;

        $cartItem = Cart::find($itemId);

        if (!$cartItem) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        // Обновляем общую сумму
        $totalPrice = 0;
        if (auth()->check()) {
            $totalPrice = Cart::where('user_id', auth()->id())
                ->join('products', 'carts.product_id', '=', 'products.id')
                ->sum(DB::raw('carts.quantity * products.price'));
        } else {
            $totalPrice = Cart::where('session_id', session('id_token'))
                ->join('products', 'carts.product_id', '=', 'products.id')
                ->sum(DB::raw('carts.quantity * products.price'));
        }

        return response()->json(['totalPrice' => $totalPrice]);
    }

}
