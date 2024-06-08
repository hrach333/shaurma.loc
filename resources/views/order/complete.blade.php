@extends('layouts.app')

@section('title', 'Заказ завершен')

@section('content')
    <div class="container mt-5">
        @if(isset($order))
            <h2>Спасибо за ваш заказ!</h2>
            <p>Ваш заказ №{{ $order->id }} успешно оформлен.</p>
            <p>Мы отправили подтверждение на ваш email: {{ $order->email }}</p>
        @else
            <h2>Ошибка при оформлении заказа</h2>
            <p>{{ $error }}</p>
        @endif
    </div>
@endsection
