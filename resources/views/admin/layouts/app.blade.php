<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @include('ckfinder::setup')
    <script type="text/javascript" src="/js/ckfinder/ckfinder.js"></script>
    <script>
        CKFinder.config({
            connectorPath: '/ckfinder/connector'
        });
    </script>
    @yield('head')
    <!-- Дополнительные стили -->
    <style>
        .wrapper {
            display: flex;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .sidebar {
            width: 250px;
            background-color: #193756;
            /* измененный цвет фона */
            color: white;
            height: 100vh;
            transition: all 0.3s;
        }

        .menu {
            background-color: #193756;
            /* измененный цвет фона */
            color: #007bff;
            /* голубой цвет текста */
            padding: 10px;
            margin-bottom: 10px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .menu ul {
            list-style-type: none;
            padding: 0;
        }

        .menu-title {
            cursor: pointer;
        }

        .footer {
            background-color: #193756;
            /* измененный цвет фона */
            color: white;
            text-align: center;
            padding: 10px;
        }

        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 0;
                overflow: hidden;
            }

            .content {
                width: 100%;
            }

            .menu {
                height: 0;
                padding: 0;
            }
        }
    </style>
    <script src="{{ asset('ckfinder/ckfinder.js') }}"></script>
</head>

<body>

    <div class="wrapper">
        @include('admin.layouts.sidebar')

        <div class="content">
            @yield('content')
        </div>
    </div>

    @include('admin.layouts.footer')

    @yield('scripts')
    <script>
        $(document).ready(function() {
            $(".toggle-btn").click(function() {
                $(".sidebar").toggleClass("collapsed");
                $(".menu").css("height", "0");
            });

            $(".menu-title").click(function() {
                $(this).next("ul").slideToggle();
            });
            $('.product-name').on('blur', function() {
                var title = $(this).html();
                var ProductId = $(this).data('id-good');
                // Получаем токен CSRF из мета-тега
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '/admin/save_content',
                    method: 'POST',
                    data: {
                        content_id: ProductId,
                        product_name: title,

                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken // Передаем токен CSRF в заголовке запроса
                    },
                    success: function(response) {
                        console.log('Контент успешно сохранен!');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
            $('.price.float-right').on('blur', function() {
                var productPrice = $(this).html();
                var price = productPrice.replace(/₽/g, '');
                var ProductId = $(this).data('id-good');
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '/admin/save_content',
                    method: 'POST',
                    data: {
                        content_id: ProductId,
                        product_price: price,

                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken // Передаем токен CSRF в заголовке запроса
                    },
                    success: function(response) {
                        console.log('Контент успешно сохранен!');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });

            })
            $(document).on('click', '.add-button', function() {

                // Создаем новый блок
                var newBlock = $('<div class="col-lg-4"></div>');

                // Добавляем атрибут style с значением position: relative;
                newBlock.css('position', 'relative');

                // Добавляем элементы внутрь созданного блока с текстовыми заглушками
                newBlock.append('<div class="single-menu">' +
                    '<div class="title-div justify-content-between d-flex">' +
                    '<div><img src="./img/placeholder.jpg" style="width: 100%"></div>' +
                    // Текстовая заглушка для изображения
                    '<h4 id="product-name" data-id-good="1" contenteditable="true">Product Name</h4>' +
                    // Текстовая заглушка для названия продукта
                    '<p class="price float-right" data-id-good="1" contenteditable="true">100₽</p>' +
                    // Текстовая заглушка для цены
                    '</div>' +
                    '</div>');

                // Вставляем созданный блок в DOM
                $('.row').append(newBlock);

                // Отправляем AJAX запрос на сервер для создания новой записи в БД
                $.ajax({
                    url: '/admin/createProduct',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF токен Laravel
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log(data); // Выводим ответ от сервера в консоль
                        //alert('Product created successfully'); // Показываем сообщение об успешном создании продукта
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('There was an error!',
                            error); // Логируем ошибку в консоль
                    }
                });
            });
            /**
            $('.product-image').hover(function() {
                if ($(this).attr('src') !== '{{ asset('img/default.png') }}') {
                    $(this).next('.button-add-image').children('img').attr('src', '{{ asset('images/pencil.svg') }}');
                } else {
                    $(this).next('.button-add-image').children('img').attr('src', '{{ asset('images/plus.png') }}');
                }
                $(this).closest('.button-add-image').show();
            }, function() {
                if ($(this).attr('src') !== '{{ asset('img/default.png') }}') {
                    $(this).next('.button-add-image').children('img').attr('src', '{{ asset('images/pencil.svg') }}');
                } else {
                    $(this).next('.button-add-image').children('img').attr('src', '{{ asset('images/plus.png') }}');
                }
                $(this).closest('.button-add-image').show();
            });
                **/
            $('.product-container').hover(function() {
                console.log('this hover')
                $(this).children('.button-add-image').show();
            }, function() {
                $(this).children('.button-add-image').hide();
                console.log('this function')
            });

            if ($('.product-image').attr('src') !== '{{ asset('img/default.png') }}') {
                $('.button-add-image img').attr('src', '{{ asset('images/pencil.png') }}');
            } else {
                $('.button-add-image img').attr('src', '{{ asset('images/plus.png') }}');
            }

        });
        // Находим все элементы с атрибутом contenteditable="true"
        var editableElements = document.querySelectorAll('[contenteditable="true"]');

        // Добавляем обработчик события для каждого редактируемого элемента
        editableElements.forEach(function(element) {
            element.addEventListener('keydown', function(event) {
                // Проверяем, была ли нажата клавиша Enter
                if (event.key === 'Enter') {
                    // Завершаем редактирование элемента
                    element.blur(); // Выход из фокуса элемента
                    event.preventDefault(); // Предотвращаем стандартное поведение Enter
                }
            });
        });
        //Открываем файловый менеджер
        function openCKFinder(id) {
            CKFinder.modal({
                language: 'ru',
                chooseFiles: true,
                width: 800,
                height: 600,
                onInit: function(finder) {
                    finder.on('files:choose', function(evt) {
                        var file = evt.data.files.first();
                        var imageUrl = file.getUrl();

                        var image = file.attributes.name;
                        console.log('Имя файла ' + image);
                        // Здесь вы можете заменить изображение продукта на выбранное изображение.
                        //console.log(imageUrl);
                        $('#image' + id).attr('src', imageUrl);
                        saveImageNameToProduct(image, id);

                    });
                }
            });

            function extractImageNameFromURL(url) {
                // Регулярное выражение для поиска названия файла в URL
                var regex = /\/([^\/?#]+\.(?:jpg|jpeg|png|gif))$/i;
                // Сопоставление URL с регулярным выражением
                var match = url.match(regex);
                // Если найдено совпадение, возвращаем название файла с расширением, иначе возвращаем null
                return match ? match[1] : null;
            }

            function saveImageNameToProduct(imageName, productId) {
                var url = '/admin/save-image';

                var data = {
                    productId: productId,
                    imageName: imageName,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.post(url, data)
                    .done(function(response) {
                        console.log('Название изображения успешно сохранено:', response);
                    })
                    .fail(function(xhr, status, error) {
                        console.error('Ошибка при сохранении названия изображения:', error);
                    });
            }



        }
        // Функция для удаления продукта с подтверждением
        function confirmDeleteProduct(productId) {
            // Подтверждение удаления
            if (confirm('Вы уверены, что хотите удалить этот продукт?')) {
                deleteProduct(productId);
            } else {
                console.log('Удаление отменено');
            }
        }

        // AJAX-запрос на удаление продукта
        function deleteProduct(productId) {
            $.ajax({
                url: '/admin/products/' + productId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Обработка успешного удаления
                    console.log('Продукт успешно удален ');
                    $('#card' + productId).remove();
                },
                error: function(xhr, status, error) {
                    // Обработка ошибки
                    console.error('Ошибка при удалении продукта:', error);
                }
            });
        }
    </script>

</body>

</html>
