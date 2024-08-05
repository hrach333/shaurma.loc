@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Оформление заказа</h2>

        <form id="order-form">
            <div class="user-details-section">
                <h4>Контактная информация</h4>
                <div class="form-group">
                    <label for="full_name">ФИО</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="{{ auth()->check() ? auth()->user()->details->full_name : '' }}">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="{{ auth()->check() ? auth()->user()->email : '' }}">
                </div>
                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ auth()->check() ? auth()->user()->details->phone : '' }}">
                </div>
                <div class="form-group">
                    <label for="address">Адрес доставки</label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ auth()->check() ? auth()->user()->details->address : '' }}">
                </div>
                <button type="button" class="btn btn-primary" id="next-step">Далее</button>
            </div>

            <div class="payment-method-section" style="display:none;">
                <h4>Выберите способ оплаты</h4>
                <div class="form-group">
                    <button type="button" class="btn btn-outline-primary payment-method" data-method="bank_card">
                        <img src="{{ asset('images/card.png') }}" alt="Оплата картой">
                    </button>
                    <button type="button" class="btn btn-outline-primary payment-method" data-method="sbp">
                        <img src="{{ asset('images/sbp.jpg') }}" alt="Система быстрых платежей" style="border-radius: 15px;">
                    </button>
                </div>
            </div>
            <div id="payment-details" style="display:none;">
                <h3>Введите данные банковской карты</h3>
                <div class="credit-card-form">
                <div class="form-group">
                    <label for="card-number">Номер карты</label>
                    <input type="text" class="form-control" id="card-number" name="card_number" maxlength="19" placeholder="XXXX XXXX XXXX XXXX">
                </div>
                <div class="form-group">
                    <label for="card-expiry">Дата истечения срока действия</label>
                    <input type="text" class="form-control" id="card-expiry" name="card_expiry" placeholder="MM/YY">
                </div>
                <div class="form-group">
                    <label for="card-cvc">CVC</label>
                    <input type="password" class="form-control" id="card-cvc" name="cvc" maxlength="3" placeholder="CVC">
                </div>

                <button type="button" id="pay-button" class="btn btn-primary">Оплатить</button>
                </div>
            </div>
        </form>
        <div id="info"></div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            $('#next-step').click(function() {
                $('.user-details-section').hide();
                $('.payment-method-section').show();
            });

            $('.payment-method').click(function() {
                var method = $(this).data('method');
                if (method === 'bank_card') {
                    // Показать форму для ввода данных банковской карты
                    showBankCardForm();
                } else if (method === 'sbp') {
                    // Показать QR-код для оплаты через СБП
                    processSbpPayment();
                }
            });

            function showBankCardForm() {
                var payment_details = $('#payment-details');
                //console.log(payment_details.length);
                if (payment_details.is(":hidden")) {
                    $('#payment-details').show();
                } else {
                    $('#payment-details').hide();
                }

            }
            $('#pay-button').on('click', function (){
                var total = {{$total}};
                var formData = $('#order-form').serialize();
                var token = '{{ csrf_token() }}';
                //console.log(formData);

                $.ajax({
                    url: '{{ route('order.create') }}',
                    method: 'POST',
                    data: formData + '&total=' + total + '&_token=' + token,

                    success: function(response) {
                        console.log(response);
                        console.log(response.url);
                        //let data = JSON.parse(response);
                        //$('#info').html('<a href="' +data.url + '">Перейти для подтверждение</a>');
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });

            })

            function processSbpPayment() {
                // Реализация процесса оплаты через СБП
            }

            $('#card-number').on('input', function() {
                var value = $(this).val().replace(/\D/g, '').substring(0, 16); // Keep only digits and limit to 16 digits
                var formattedValue = value.match(/.{1,4}/g)?.join(' ') || ''; // Add spaces every 4 digits
                $(this).val(formattedValue);
            });

            // Format expiry date input
            $('#card-expiry').datepicker({
                dateFormat: "mm/yy",
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).val($.datepicker.formatDate('mm/yy', new Date(year, month, 1)));
                }
            });

            $("#card-expiry").focus(function() {
                $(".ui-datepicker-calendar").hide();
                $("#ui-datepicker-div").position({
                    my: "center top",
                    at: "center bottom",
                    of: $(this)
                });
            });


        });
    </script>
@endpush
