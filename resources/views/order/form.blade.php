@extends('layouts.app')

@section('title', 'Оформление заказа')

@section('content')
    <div class="container">
        <h2>Оформление заказа</h2>

        <form id="orderForm">
            @csrf
            @if (Auth::check())
                @php
                    $user = Auth::user();
                    $fullName = $user->full_name ?? '';
                    $address = $user->address ?? '';
                    $phoneNumber = $user->phone_number ?? '';
                @endphp
            @else
                @php
                    $fullName = '';
                    $address = '';
                    $phoneNumber = '';
                @endphp
            @endif

            <!-- Поля для информации о клиенте -->
            <div class="form-group">
                <label for="fullName">ФИО</label>
                <input type="text" class="form-control" id="fullName" name="full_name" value="{{ $fullName }}" required>
            </div>
            <div class="form-group">
                <label for="address">Адрес</label>
                <input type="text" class="form-control" id="address" name="address" value="{{ $address }}" required>
            </div>
            <div class="form-group">
                <label for="phoneNumber">Телефон</label>
                <input type="text" class="form-control" id="phoneNumber" name="phone_number" value="{{ $phoneNumber }}" required>
            </div>

            <!-- Выбор способа оплаты -->
            <div class="form-group">
                <label for="paymentType">Способ оплаты</label>
                <select class="form-control" id="paymentType" name="payment_type" required>
                    <option value="bank_card">Банковская карта</option>
                    <option value="sbp">Система быстрых платежей (СБП)</option>
                </select>
            </div>

            <!-- Поля для ввода данных банковской карты -->
            <div id="cardDetails" style="display: none;">
                <div class="form-group">
                    <label for="cardNumber">Номер карты</label>
                    <input type="text" class="form-control" id="cardNumber" name="card_number">
                </div>
                <div class="form-group">
                    <label for="cardExpiry">Срок действия карты (MM/YY)</label>
                    <input type="text" class="form-control" id="cardExpiry" name="card_expiry">
                </div>
                <div class="form-group">
                    <label for="cardCVC">CVC</label>
                    <input type="text" class="form-control" id="cardCVC" name="card_cvc">
                </div>
            </div>

            <!-- Поля для СБП -->
            <div id="sbpDetails" style="display: none;">
                <div class="form-group">
                    <label for="sbpParticipant">Участник СБП</label>
                    <select class="form-control" id="sbpParticipant" name="sbp_participant_id">
                        <!-- Здесь необходимо заполнить список участников СБП -->
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Подтвердить заказ</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Показать/скрыть поля в зависимости от выбранного способа оплаты
            $('#paymentType').change(function() {
                var paymentType = $(this).val();
                if (paymentType === 'bank_card') {
                    $('#cardDetails').show();
                    $('#sbpDetails').hide();
                } else if (paymentType === 'sbp') {
                    $('#cardDetails').hide();
                    $('#sbpDetails').show();
                } else {
                    $('#cardDetails').hide();
                    $('#sbpDetails').hide();
                }
            });

            // Получение списка участников СБП
            function loadSbpParticipants() {
                $.ajax({
                    url: '{{ route('payment.sbp.participants') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            var participants = response.participants;
                            var sbpParticipantSelect = $('#sbpParticipant');
                            sbpParticipantSelect.empty();
                            $.each(participants, function(index, participant) {
                                sbpParticipantSelect.append('<option value="' + participant.id + '">' + participant.name + '</option>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            // Загружаем участников СБП при загрузке страницы
            loadSbpParticipants();

            // Отправка формы
            $('#orderForm').submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                $.ajax({
                    url: '{{ route('order.create') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            var orderId = response.order_id;
                            var paymentType = $('#paymentType').val();

                            if (paymentType === 'bank_card') {
                                // Если выбрана оплата картой, создаем платеж
                                $.ajax({
                                    url: '{{ route('payment.create') }}',
                                    method: 'POST',
                                    data: {
                                        order_id: orderId,
                                        payment_type: paymentType,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        if (response.status === 'success') {
                                            // Перенаправляем на страницу оплаты
                                            window.location.href = response.redirect_url;
                                        } else {
                                            alert('Ошибка при создании платежа: ' + response.message);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error(error);
                                    }
                                });
                            } else if (paymentType === 'sbp') {
                                // Если выбрана оплата через СБП, создаем платеж через СБП
                                $.ajax({
                                    url: '{{ route('payment.create') }}',
                                    method: 'POST',
                                    data: {
                                        order_id: orderId,
                                        payment_type: paymentType,
                                        phone_number: $('#phoneNumber').val(),
                                        sbp_participant_id: $('#sbpParticipant').val(),
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        if (response.status === 'success') {
                                            alert('Оплата через СБП успешно создана');
                                        } else {
                                            alert('Ошибка при создании платежа: ' + response.message);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error(error);
                                    }
                                });
                            }
                        } else {
                            alert('Ошибка при создании заказа: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });
    </script>
@endsection
