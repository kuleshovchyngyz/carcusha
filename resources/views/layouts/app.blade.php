<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="shortcut icon" href="{{ asset('img/speedometer.png') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>


    @include('layouts.message')
    <header class="header">
        <div class="container">
            <div class="row align-items-center justify-content-between" style="padding: 0 14px;">
                <div class="">
                    <a href="{{ route('home') }}">
                    <img src="{{ asset('img/logo.png') }}" alt="" class="logo">
                    <img src="{{ asset('img/logo-min.png') }}" alt="" class="logo-min">
                    </a>
                </div>
                <div class="ml-auto d-flex">
                    @auth()
                    <div class="notification-head">
                        <a href="#">


                            {!! ViewService::init()->view('numberOfNewNotifications') !!}
                            <img src="{{ asset('img/icon-alert.png') }}" alt="">
                        </a>
                        <div class="notification-head-dd">
                            <ul class="notifications">
                                {!! ViewService::init()->view('headerNotifications') !!}

                            </ul>
                        </div>
                    </div>

                        @if(Route::currentRouteName()!='lead.create' && Route::currentRouteName()!='qrform'&& Route::currentRouteName()!='car_application')
                                <a class="btn header__btn blue-btn" href="{{ route('lead.create') }}"> Добавить авто</a>
                        @endif
                    @endauth
                </div>

                <div class="main__dd-btn"></div>
            </div>
        </div>
    </header>


 <!-- note the end auth -->





            @yield('content')

    <footer class="footer">
        <div class="container d-flex">

            <img src="{{ asset('img//logo-white.png') }}" alt="" class="footer__logo">
            <a href="{{ route('public_offer') }}"  class="footer__link ml-auto">Публичная оферта</a>
        </div>
    </footer>
</body>
</html>
