@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Оформление заказа</h2>
        <form id="orderForm" action="{{ route('order.create') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Имя</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="address">Адрес</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="total">Сумма заказа</label>
                <input type="text" class="form-control" id="total" name="total" value="{{ $total }}" readonly>
            </div>
            <div class="form-group">
                <label for="payment_method">Способ оплаты</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="bank_card">Банковская карта</option>
                    <option value="sbp">Система быстрых платежей (СБП)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Оформить заказ</button>
        </form>

        <div id="paymentSection" style="display: none;">
            <h2>Оплата заказа</h2>
            <button id="payWithSbp" class="btn btn-success" style="display: none;">Оплатить через СБП</button>
            <div id="cardPaymentForm" style="display: none;">
                <!-- Форма для ввода данных карты -->
                <div class="form-group">
                    <label for="card_number">Номер карты</label>
                    <input type="text" class="form-control" id="card_number" name="card_number" required>
                </div>
                <div class="form-group">
                    <label for="card_expiry">Срок действия карты</label>
                    <input type="text" class="form-control" id="card_expiry" name="card_expiry" required>
                </div>
                <div class="form-group">
                    <label for="card_cvc">CVC</label>
                    <input type="text" class="form-control" id="card_cvc" name="card_cvc" required>
                </div>
                <button id="payWithCard" class="btn btn-success">Оплатить картой</button>
            </div>
        </div>

        <div id="qrCodeSection" style="display: none;">
            <h2>Сканируйте QR-код для оплаты</h2>
            <img id="qrCodeImage" src="" alt="QR-код для оплаты">
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#orderForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var paymentMethod = $('#payment_method').val();

                $.ajax({
                    url: '{{ route('order.create') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#orderForm').hide();
                        $('#paymentSection').show();

                        if (paymentMethod === 'sbp') {
                            $('#payWithSbp').show();
                        } else {
                            $('#cardPaymentForm').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ошибка при оформлении заказа:', error);
                    }
                });
            });

            $('#payWithSbp').on('click', function() {
                var total = $('#total').val();

                $.ajax({
                    url: '{{ route('payment.create') }}',
                    method: 'POST',
                    data: {
                        total: total,
                        payment_method: 'sbp',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#paymentSection').hide();
                        $('#qrCodeSection').show();
                        $('#qrCodeImage').attr('src', response.confirmation.confirmation_data);

                        // Периодически проверяем статус платежа
                        var paymentId = response.id;
                        var intervalId = setInterval(function() {
                            $.ajax({
                                url: '{{ route('payment.status') }}',
                                method: 'GET',
                                data: {
                                    paymentId: paymentId
                                },
                                success: function(response) {
                                    if (response.status === 'succeeded') {
                                        clearInterval(intervalId);
                                        alert('Платеж успешно завершен!');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Ошибка при проверке статуса платежа:', error);
                                }
                            });
                        }, 5000); // Проверяем каждые 5 секунд
                    },
                    error: function(xhr, status, error) {
                        console.error('Ошибка при создании платежа:', error);
                    }
                });
            });

            $('#payWithCard').on('click', function() {
                var total = $('#total').val();
                var cardNumber = $('#card_number').val();
                var cardExpiry = $('#card_expiry').val();
                var cardCvc = $('#card_cvc').val();

                $.ajax({
                    url: '{{ route('payment.create') }}',
                    method: 'POST',
                    data: {
                        total: total,
                        payment_method: 'bank_card',
                        card_number: cardNumber,
                        card_expiry: cardExpiry,
                        card_cvc: cardCvc,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Платеж успешно завершен!');
                    },
                    error: function(xhr, status, error) {
                        console.error('Ошибка при создании платежа:', error);
                    }
                });
            });
        });
    </script>
@endsection
