<!DOCTYPE html>
<html lang="zxx" class="no-js">
<head>
    <!-- Mobile Specific Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Favicon-->
    <link rel="shortcut icon" href="img/fav.png">
    <!-- Author Meta -->
    <meta name="author" content="codepixer">
    <!-- Meta Description -->
    <meta name="description" content="">
    <!-- Meta Keyword -->
    <meta name="keywords" content="">
    <!-- meta character set -->
    <meta charset="UTF-8">
    <!-- Site Title -->
    <title>Час Гурмана</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,400,300,500,600,700" rel="stylesheet">
    <!--
    CSS
    ============================================= -->
    <link rel="stylesheet" href="css/linearicons.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/nice-select.css">
    <link rel="stylesheet" href="css/animate.min.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

<header id="header" id="home">
    <div class="header-top">
        <div class="container">
            <div class="row justify-content-end">
                <div class="col-lg-8 col-sm-4 col-8 header-top-right no-padding">
                    <ul>
                        <li>
                            Пн-Пт: 8:00 до 18:00
                        </li>
                        <li>
                            Сб-Вс: <strong>Выходной</strong>
                        </li>
                        <li>
                            <a href="tel:(012) 6985 236 7512">8912985674</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row align-items-center justify-content-between d-flex">
            <div id="logo">
                <a href="index.html"><img src="img/logo.png" alt="" title=""/></a>
            </div>
            <nav id="nav-menu-container">
                <ul class="nav-menu">
                    <li class="menu-active"><a href="#home">Главная</a></li>
                    <li><a href="#about">Продукция</a></li>
                    <li><a href="#coffee">О нас </a></li>
                    <!--
                  <li class="menu-has-children"><a href="">Pages</a>
                    <ul>
                      <li><a href="generic.html">Generic</a></li>
                      <li><a href="elements.html">Elements</a></li>
                    </ul>

                  </li>
                  -->
                </ul>
            </nav><!-- #nav-menu-container -->
        </div>
    </div>
</header><!-- #header -->


<!-- start banner Area -->
<section class="banner-area" id="home">
    <div class="container">
        <div class="row fullscreen d-flex align-items-center justify-content-start">
            <div class="banner-content col-lg-7">

                <h2 style="color: #fff" class="text-home">
                    Час Гурмана: Гастрономическое волшебство в каждом укусе! Наслаждайтесь праздником вместе снами!
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
                <h6>Жизненный процесс приготовления шаурми.</h6>
                <p>Мы делаем качественно из свежих индигриентов не жалея начинки.</p>
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
                    <!--<p>Who are in extremely love with eco friendly system.</p>-->
                </div>
            </div>
        </div>
        <div class="row">
            @foreach($products as $product)
            <div class="col-lg-4">
                <div class="single-menu">
                    <div class="title-div justify-content-between d-flex">
                        <div><img src="{{asset('/img/'. $product->product_img)}}" width="210"
                                  style="width: 100%"></div>
                        <h4>{{$product->product_name}}</h4>

                        <p class="price float-right">
                            {{$product->price}}₽
                        </p>
                    </div>
                    <button type="button" class="button-brown">Добавить в карзину</button>
                </div>
            </div>
            @endforeach
            <div class="col-lg-12 centr">
            {{ $products->links('pagination::bootstrap-4') }}
            </div>
        </div>
</section>
<!-- End menu Area -->
<section>

</section>

<!-- start footer Area -->
<footer class="footer-area section-gap">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-6 col-sm-6">
                <div class="single-footer-widget">
                    <h6>Об комапнии</h6>
                    <p>
                        Краткое описание
                    </p>
                    <p class="footer-text">
                        <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                        Copyright &copy;<script>document.write(new Date().getFullYear());</script>
                        All rights reserved | This template is made with <i class="fa fa-heart-o"
                                                                            aria-hidden="true"></i> by <a
                            href="https://colorlib.com" target="_blank">Colorlib</a>
                        <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    </p>
                </div>
            </div>
            <div class="col-lg-5  col-md-6 col-sm-6">

            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 social-widget">
                <div class="single-footer-widget">
                    <h6>Follow Us</h6>
                    <p>Let us be social</p>
                    <div class="footer-social d-flex align-items-center">
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-twitter"></i></a>
                        <a href="#"><i class="fa fa-dribbble"></i></a>
                        <a href="#"><i class="fa fa-behance"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- End footer Area -->

<script src="js/vendor/jquery-2.2.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="js/vendor/bootstrap.min.js"></script>
<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhOdIF3Y9382fqJYt5I_sswSrEw5eihAA"></script>
<script src="js/easing.min.js"></script>
<script src="js/hoverIntent.js"></script>
<script src="js/superfish.min.js"></script>
<script src="js/jquery.ajaxchimp.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/jquery.sticky.js"></script>
<script src="js/jquery.nice-select.min.js"></script>
<script src="js/parallax.min.js"></script>
<script src="js/waypoints.min.js"></script>
<script src="js/jquery.counterup.min.js"></script>
<script src="js/mail-script.js"></script>
<script src="js/main.js"></script>
</body>
</html>



