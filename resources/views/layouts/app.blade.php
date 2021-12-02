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
                    Test
                    <a href="{{ route('home') }}">
                    <img src="{{ asset('img/logo.png') }}" alt="" class="logo">
                    <img src="{{ asset('img/logo-min.png') }}" alt="" class="logo-min">
                    </a>
                </div>
                @auth()
                    @if(Route::currentRouteName()!='lead.create' && Route::currentRouteName()!='qrform'&& Route::currentRouteName()!='car_application')
                        <div class="">
                            <a class="btn header__btn red-btn" href="{{ route('lead.create') }}"> Добавить авто</a>

                        </div>
                    @endif
                @endauth
                <div class="main__dd-btn"></div>
            </div>
        </div>
    </header>


 <!-- note the end auth -->


            @yield('content')


</body>
</html>
