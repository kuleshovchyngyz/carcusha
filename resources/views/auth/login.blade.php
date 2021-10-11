@extends('layouts.app')
@include('layouts.message')
@section('content')

    @guest
        @php($login = false)
        @php($register = false)

    @if(null !==Session::get('last_auth_attempt') )
        @if(Session::get('last_auth_attempt')=='login'&&isset($_GET['ref']))
                @php($login=true)
        @endif
        @if(Session::get('last_auth_attempt')=='login'&& !isset($_GET['ref']))
            @php($login=true)
        @endif
    @endif
    @if(null !==Session::get('last_auth_attempt') )
        @if(Session::get('last_auth_attempt')=='register'&&isset($_GET['ref']))
            @php($register=true)
        @endif
        @if(Session::get('last_auth_attempt')=='register'&&!isset($_GET['ref']))
            @php($register=true)
        @endif
    @endif
        @if((!$login &&!$register) && isset($_GET['ref']))
            @php($register=true)
            @php($login=false)
        @endif
        @if((!$login &&!$register) && !isset($_GET['ref']))
            @php($register=false)
            @php($login=true)
        @endif





    <div class="authentication">
        <img src="{{ asset('img/logo-aut.png') }}" alt="" class="logo">
        <div class="tab-wrap">
            <ul class="nav mb-3" id="pills-tab" role="tablist">
                <li class="nav__tab-item">
                    <a class="nav-link nav-link-aut {{$login ? 'active' : ''}}" id="authentication-tab" data-toggle="pill" href="#authenticationtab" role="tab"
                       aria-controls="authentication-tab" aria-selected="true">Авторизация</a>
                </li>
                <li class="nav__tab-item">
                    <a class="nav-link nav-link-aut {{ ($register) ? 'active' : ''}}" id="registration-tab" data-toggle="pill" href="#registrationtab" role="tab"
                       aria-controls="registration" aria-selected="false">Регистрация</a>
                </li>
            </ul>
            <div class="tab-content tab-content--custom" id="pills-tabContent">

                <div class="tab-pane fade  {{ $login ? 'active show' : ''}}" id="authenticationtab" role="tabpanel" aria-labelledby="authentication-tab">
                    <form method="POST" id="authentication" action="{{ route('login') }}" class="text-center">
                        @csrf

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                             <strong>{{ $message }}</strong>
                        </span>
                        @enderror


                        <input id="text" type="text"  placeholder="Телефон или E-Mail" class="form-control
                        @if($login)
                         @error('email') is-invalid @enderror
                        @endif " name="email" value="{{ old('email') }}" required autocomplete="email">

                        @if($login)
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror
                                <input id="text" type="hidden"   class="form-control
                                 @if($login)
                                        @error('number') is-invalid @enderror
                                @endif
                                    " name="number">
                                @error('number')
                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                @enderror

                        @endif
                        <button class="btn red-btn restorepass" style="display: none;">Восстановить пароль</button>

                        <input id="password" type="password" placeholder="Пароль" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">



                        @if (Route::has('password.request'))
                            <a  class="btn red-btn" href="{{ route('password.request') }}">
                                {{ __('Забыли пароль?') }}
                            </a>
                        @endif
                        <button type="submit" class="btn btn-red">Войти</button>
                    </form>
                </div>



                <div class="tab-pane fade {{ $register  ? 'active show' : ''}}" id="registrationtab" role="tabpanel" aria-labelledby="registration-tab">

                    <form id="registration" method="POST" action="{{ route('register') }}" class="text-center">
                        @csrf

                        <input id="text" type="text"  placeholder="Телефон или E-Mail" class="form-control
                        @if($register)
                            @error('email') is-invalid @enderror
                        @endif
                            "
                        name="email" value="{{ old('email') }}" required autocomplete="email">
                        @if($register)
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                            <input id="text" type="hidden"   class="form-control
                                 @if($register)
                            @error('number') is-invalid @enderror
                            @endif
                                " name="number">
                            @error('number')
                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                            @enderror
                        @endif
                        <input id="text" type="password" placeholder="Пароль" class="form-control
                        @if($register)
                        @error('password') is-invalid @enderror
                        @endif
                            " name="password" required autocomplete="new-password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                        @enderror

                        <a href="#" class="btn red-btn {{ isset($_GET['ref']) ? 'd-none' : ''}}" id="invitation">У меня есть код приглашения</a>
                        <input type="text" name="invitation_code" class="form-control {{ isset($_GET['ref']) ? 'd-block' : ''}}" value="{{ isset($_GET['ref']) ? $_GET['ref'] : ''}}" id="invitation-inpup" placeholder="Код приглашения" style="display: none;">
                        <button type="submit" class="btn btn-red">РЕГИСТРАЦИЯ</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    @endguest
@endsection
