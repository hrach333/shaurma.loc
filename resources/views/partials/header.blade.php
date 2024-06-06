<header id="header" id="home">
    <div class="header-top">
        <div class="container">
            <div class="row justify-content-end">
                <div class="col-lg-8 col-sm-4 col-8 header-top-right no-padding">
                    <ul>
                        <li>Пн-Пт: 8:00 до 18:00</li>
                        <li>Сб-Вс: <strong>Выходной</strong></li>
                        <li><a href="tel:(012) 6985 236 7512">8912985674</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row align-items-center justify-content-between d-flex">
            <div id="logo">
                <a href="index.html"><img src="img/logo.png" alt="" title="" /></a>
            </div>
            <nav id="nav-menu-container">
                <ul class="nav-menu">
                    <li class="menu-active"><a href="{{url('/')}}">Главная</a></li>
                    <li><a href="#about">Продукция</a></li>
                    <li><a href="#coffee">О нас </a></li>
                    <li>
                        <a href="{{ asset('cart') }}">
                            <img src="images/cart.png" width="32">
                            <div class="cart-count">{{ session('totalCartItems') }}</div>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>
