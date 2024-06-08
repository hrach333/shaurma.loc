@extends('layouts.app')

@section('title', 'Настройки пользователя')

@section('content')
    <div class="container mt-5">
        <h2>Настройки пользователя</h2>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('user.details.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="full_name">ФИО:</label>
                <input type="text" id="full_name" name="full_name" class="form-control" value="{{ old('full_name', $details->full_name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $details->phone ?? '') }}" required>
            </div>
            <div class="form-group">
                <label for="address">Адрес:</label>
                <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $details->address ?? '') }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
@endsection
