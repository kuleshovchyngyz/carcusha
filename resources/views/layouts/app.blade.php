<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link rel="shortcut icon" href="{{ asset('svg/favicon.ico') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">



</head>
<body>


    @include('layouts.message')
    <header class="header">
        <div class="container">
            <div class="row align-items-center justify-content-between" style="padding: 0 14px;">
                <div class="">
                    <a href="{{ route('home') }}">
                    <img src="{{ asset('svg/logo_head_desk.svg') }}" alt="" class="logo">
                    <img src="{{ asset('svg/logo_ad_head.svg') }}" alt="" class="logo-min">
                    </a>
                </div>
                <div class="ml-auto d-flex">
                @auth()
                        <div class="notification-head">
                            <a href="#">


                                {!! ViewService::init()->view('numberOfNewNotifications') !!}
                                <img src="{{ asset('svg/menu_uved.svg') }}" alt="">
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
@include('modals.qr')

{{--    <input type="text" id="number" name="number" class="form-control" placeholder="Не указан" value="{{ ViewService::init()->view('promo_number') }}">--}}
    <footer class="footer">
        <div class="container d-flex">

            <img src="{{ asset('svg/logo_foot.svg') }}" alt="" class="footer__logo">
            <a href="{{ route('public_offer') }}"  class="footer__link ml-auto">Публичная оферта</a>
        </div>
    </footer>
    @include('layouts.whatsapp')
</body>
</html>
