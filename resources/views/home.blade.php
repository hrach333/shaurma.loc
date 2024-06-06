@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <!-- start banner Area -->
    <section class="banner-area" id="home">
        <div class="container">
            <div class="row fullscreen d-flex align-items-center justify-content-start">
                <div class="banner-content col-lg-7">
                    <h2 style="color: #fff" class="text-home">
                        Час Гурмана: Гастрономическое волшебство в каждом укусе! Наслаждайтесь праздником вместе с нами!
                    </h2>
                    <a href="#" class="primary-btn text-uppercase"></a>
                </div>
            </div>
        </div>
    </section>
    <!-- End banner Area -->

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
                        <h1 class="mb-10">Наша продукция</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-lg-4">
                        <div class="single-menu">
                            <div class="title-div justify-content-between d-flex">
                                <div><img src="{{ asset('/img/' . $product->product_img) }}" width="210" style="width: 100%"></div>
                                <h4>{{ $product->product_name }}</h4>
                                <p class="price float-right">{{ $product->price }}₽</p>
                            </div>
                            <div class="text-description">{{ $product->description }}</div>
                            <button type="button" class="button-brown" data-product-id="{{ $product->id }}">Добавить в корзину</button>
                        </div>
                    </div>
                @endforeach
                <div class="col-lg-12 centr">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
            </div>
    </section>
    <!-- End menu Area -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.button-brown').on('click', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                $.ajax({
                    url: '{{ route('cart.add') }}',
                    method: 'POST',
                    data: {
                        productId: productId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('.cart-count').text(response.totalCartItems);
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
        });
    </script>
@endpush
