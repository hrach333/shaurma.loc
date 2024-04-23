@extends('admin.layouts.app')

@section('title', $title)

@section('content')
    <!-- Основное содержимое страницы -->
    <h1>{{ $title }}</h1>
    <!-- Другие элементы страницы -->
    <section class="menu-area section-gap" id="coffee">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="menu-content pb-60 col-lg-10">
                    <div class="title text-center">
                        <h1 class="mb-10">Наша продукция</h1>
                        <!--<p>Who are in extremely love with eco friendly system.</p>-->
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-lg-4" style="position: relative;" id="card{{$product->id}}">
                        <div class="single-menu" style="display: flex;">
                            <div style="flex-grow: 1;">
                                <div class="title-div justify-content-between d-flex">
                                    <div class="product-container">
                                        <img src="{{asset('img/' . $product->product_img) }}" id="image{{$product->id}}"style="width: 100%" class="product-image" alt="">
                                        <button class="button-add-image" onclick="openCKFinder('{{$product->id}}')" >
                                            <img src="{{ asset('images/plus.png') }}" alt="Добавить изображение">
                                        </button>
                                    </div>
                                    <h4 class="product-name" id="product-name{{$product->id}}" data-id-good="{{ $product->id }}" contenteditable="true">{{ $product->product_name }}</h4>
                                    <p class="price float-right" data-id-good="{{ $product->id }}" contenteditable="true">
                                        {{ $product->price }}₽
                                    </p>
                                    <button type="button" class="btn btn-danger" onclick="confirmDeleteProduct('{{ $product->id }}')">Удалить</button>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
                    <div class="col-lg-4" style="position: relative;">
                        <div style="width: 120px;margin: 0 auto;padding: 30px;">
                            <button type="button" class="button-brown add-button">
                                <img src="{{asset('images/plus_icon.png')}}" alt="Add" style="width: 30px; height: 30px;">
                            </button>
                        </div>
                    </div>

                    {{ $products->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @parent <!-- Добавляем содержимое родительской секции 'scripts' -->

@endsection
