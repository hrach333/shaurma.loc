@extends('layouts.app')

@section('title', 'Корзина')

@section('content')


    <!-- Start video-sec Area -->
    <section class="video-sec-area pb-100 pt-40" id="about">
        <div class="container">
            <div class="row justify-content-start align-items-center">
                <div class="col-lg-6 video-right justify-content-center align-items-center d-flex">
                    <div class="overlay"></div>
                    <video src="./video/food_video.mp4" controls class="video_preview"></video>
                </div>
                <div class="col-lg-6 video-left">
                    <h6>Жизненный процесс приготовления шаурмы.</h6>
                    <p>Мы делаем качественно из свежих ингредиентов, не жалея начинки.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- End video-sec Area -->
    <!-- Start menu Area -->
    <section class="menu-area section-gap" id="coffee">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="menu-content pb-60 col-lg-10">
                    <div class="title text-center">
                        <h1 class="mb-10">Корзина</h1>
                        <!--<p>Who are in extremely love with eco friendly system.</p>-->
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($cartItems as $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="cart-item">
                            <img class="img-fluid" src="img/{{ $item->product->product_img }}" alt="{{ $item->product->product_name }}">
                            <div class="product-details">
                                <h6>{{ $item->product->product_name}}</h6>
                                <div class="price">
                                    <h6>{{ $item->product->price }}₽</h6>

                                </div>
                                <div class="prd-bottom">
                                    <div class="quantity">
                                        <span class="quantity-label">Количество:</span>
                                        <button class="quantity-decrease" data-item-id="{{ $item->id }}">-</button>
                                        <input type="number" class="quantity-input" data-item-id="{{ $item->id }}" value="{{ $item->quantity }}" min="1">
                                        <button class="quantity-increase" data-item-id="{{ $item->id }}">+</button>
                                    </div>
                                </div>
                                <button class="remove-from-cart" data-item-id="{{ $item->id }}">Удалить</button>
                            </div>
                        </div>
                    </div>
                @endforeach
                    <div class="col-12 price-total">
                        <div class="total-amount">
                            <strong>Итого: </strong> &nbsp;<h6>{{ $priceTotal }}₽</h6>
                        </div>
                        <a class="button-brown"  href="{{route('order.form')}}">Оформить заказ</a>
                    </div>
                <div class="col-lg-12 centr">
                    {{ $cartItems->links('pagination::bootstrap-4') }}
                </div>
            </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Обработка удаления товара из корзины
            $(document).on('click', '.remove-from-cart', function(e) {
                e.preventDefault();
                var itemId = $(this).data('item-id');

                $.ajax({
                    url: '{{ route('cart.remove', '') }}/' + itemId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Удаляем элемент из DOM
                        $('.remove-from-cart[data-item-id="' + itemId + '"]').closest('.col-lg-4.col-md-6').remove();

                        // Обновляем количество товаров в корзине
                        updateCartCount();
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
            function updateCartCount() {
                $.ajax({
                    url: '{{ url('/cart/count') }}',
                    method: 'GET',
                    success: function(response) {
                        $('.cart-count').text(response.count);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            updateCartCount();
            // Обработка увеличения количества товара
            $(document).on('click', '.quantity-increase', function() {
                var itemId = $(this).data('item-id');
                var input = $('.quantity-input[data-item-id="' + itemId + '"]');
                var newQuantity = parseInt(input.val()) + 1;

                updateQuantity(itemId, newQuantity);
            });
            // Обработка уменьшения количества товара
            $(document).on('click', '.quantity-decrease', function() {
                var itemId = $(this).data('item-id');
                var input = $('.quantity-input[data-item-id="' + itemId + '"]');
                var newQuantity = parseInt(input.val()) - 1;

                if (newQuantity >= 1) {
                    updateQuantity(itemId, newQuantity);
                }
            });
            // Функция для обновления количества товара в корзине
            function updateQuantity(itemId, newQuantity) {
                $.ajax({
                    url: '{{ route('cart.updateQuantity') }}',
                    method: 'POST',
                    data: {
                        itemId: itemId,
                        quantity: newQuantity,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        $('.quantity-input[data-item-id="' + itemId + '"]').val(newQuantity);
                        updateCartCount();
                        updateTotalPrice(response.totalPrice);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            }
            // Функция для обновления общей суммы
            function updateTotalPrice(totalPrice) {
                $('.price-total h6').text(totalPrice + '₽');
            }
        });
    </script>
@endpush
