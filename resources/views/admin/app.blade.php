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
    <link rel="shortcut icon" href="{{ asset('svg/favicon.ico') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
@auth
    @include('layouts.message')
    <header class="header"> 

        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 col-6">
                    <a href="{{ route('admin.users') }}">
                        <img src="{{ asset('svg/logo_head_desk.svg') }}" alt="" class="logo">
                    </a>
                </div>

                <nav class="navbar navbar-expand-sm bg-light text-right">

                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="btn header__btn color-blue" href="{{ route('admin.users') }}"> Пользователи
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn header__btn color-blue" href="{{ route('admin.payments') }}"> Выплаты
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn header__btn color-blue" href="{{ route('admin.statuses') }}"> Статусы
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn header__btn color-blue" href="{{ route('admin.fantoms') }}"> Фантомы
                            </a>
                        </li>
                        <li class="nav-item">
                            <div class="dropdown">
                                <a class="btn header__btn color-blue dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Настройки
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="btn header__btn color-blue" href="{{ route('admin.payments_settings') }}"> Оплаты
                                    </a>
                                    <a class="btn header__btn color-blue" href="{{ route('admin.settings') }}"> Справки
                                    </a>
                                    <a class="btn header__btn color-blue" href="{{ route('admin.ads') }}">        Объявления
                                    </a>
                                    <a class="btn header__btn color-blue" href="{{ route('admin.offer') }}">Публичная оферта
                                    </a>
                                    <a class="btn header__btn color-blue" href="{{ route('admin.updates') }}">Обновления
                                    </a>
                                    <a class="btn header__btn color-blue" href="{{ route('admin.majors') }}">Род деятельности
                                    </a>
                                    <a data-toggle="modal" data-target="#AddTelegramBot" class="btn header__btn color-blue"> Добавить бот</a>
                                    <a data-toggle="modal" data-target="#AddWhatsapp" class="btn header__btn color-blue">Вотсап тел</a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="btn header__btn color-blue" href="{{ route('logout') }}"> Выйти
                            </a>
                        </li>
                    </ul>
                </nav>

            </div>
        </div>
    </header>

@endauth <!-- note the end auth -->


@yield('content')
@include('modals.modals')

</body>
</html>
