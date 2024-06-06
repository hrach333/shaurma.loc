<div class="cart-container">
    <h3>Корзина</h3>
    <ul>
        @foreach($cartItems as $item)
            <li>{{ $item->product_name }} - {{ $item->quantity }} шт. - {{ $item->price * $item->quantity }}₽</li>
        @endforeach
    </ul>
    <p>Общая сумма: {{ $totalPrice }}₽</p>
    <button id="checkout-btn">Оформить заказ</button>
</div>
