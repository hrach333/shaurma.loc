@extends('admin.layouts.app')

@section('title', $title)

@section('content')
    <!-- Основное содержимое страницы -->
    <h1>{{ $title }}</h1>
    <!-- Другие элементы страницы -->
    <section class="menu-area section-gap" id="coffee">
        <div class="container">
            <div class="row d-flex justify-content-center">

                </div>
            </div>
            <div class="row">
                <form action="/admin/add-admin" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{$userId}}">
                    <input type="submit" value="Отправить">
                </form>
        </div>
    </section>
@endsection

@section('scripts')
    @parent <!-- Добавляем содержимое родительской секции 'scripts' -->

@endsection
