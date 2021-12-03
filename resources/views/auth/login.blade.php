{{--@if($errors->any())--}}
{{--    {{ implode('', $errors->all(':message')) }}--}}
{{--@endif--}}

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
        <img src="{{ asset('img/logo-aut.png') }}" alt="" class="logoauth">
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
                        <div class="type-field d-flex">
                            <span class="phone @if(null !==old('number'))  active @endif">Телефон</span>
                            <label class="type-field-select @if(null ===old('number')) mail @else phone @endif">
                                <input type="checkbox" name="type-field" id="type_field">
                            </label>
                            <span class="mail @if(null ===old('number')) active @endif">E-Mail</span>
                        </div>
                        @if($login)
                            @error('email')
                            <span class="invalid-feedbackerror" role="alert">
                                      <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        @endif
                        @if($login)
                            @error('number')
                            <span class="invalid-feedbackerror" role="alert">
                                      <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        @endif
                        <div class="pos-r">
                            <input type="mail" placeholder="E-Mail" name="email" class="form-control type-mail @if(null !==old('number')) disabled @endif
                            @if($login)
                            @error('email') is-invalid @enderror
                            @endif " value="{{ old('email') }}" required autocomplete="email" @if(null !==old('number')) disabled="disabled" @endif>


                            <input type="tel" placeholder="Телефон" id="numberRegister" name="number" class="form-control type-phone @if(null ===old('number')) disabled @endif" @if(null ===old('number')) disabled="disabled" @endif
                            @if($login)
                            @error('number') is-invalid @enderror
                                   @endif  value="{{ old('number') }}" required autocomplete="email">
                            <span class="form-subbtitle disabled">В формате +7</span>
                        </div>
                        <div class="type-pass">

                            <input id="password" type="password" placeholder="Пароль"  class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" required autocomplete="current-password">
                            <label><input type="checkbox" class="password-checkbox"></label>
                        </div>
                        @if (Route::has('password.request'))
                            <a  class="red-link" href="{{ route('auth.forgot.password') }}">Забыли пароль?</a>
                        @endif
                        <button class="btn btn-red">Войти</button>
                    </form>
                </div>



                <div class="tab-pane fade {{ $register  ? 'active show' : ''}}" id="registrationtab" role="tabpanel" aria-labelledby="registration-tab">
                    <form id="registration" method="POST" action="{{ route('auth.verification-code') }}" class="text-center">
                        @csrf
                        <div class="type-field d-flex">
                            <span class="phone @if(null !==old('number'))  active @endif">Телефон</span>
                            <label class="type-field-select @if(null ===old('number')) mail @else phone @endif">
                                <input type="checkbox" name="type-field" id="type_field">
                            </label>
                            <span class="mail @if(null ===old('number')) active @endif">E-Mail</span>

                        </div>
                        @if($register)
                            @error('email')
                            <span class="invalid-feedbackerror" role="alert">
                                      <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        @endif
                        @if($register)
                            @error('number')
                            <span class="invalid-feedbackerror" role="alert">
                                      <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        @endif
                        <div class="pos-r">
                            <input type="mail" placeholder="E-Mail" name="email" class="form-control type-mail @if(null !==old('number')) disabled @endif
                            @if($register)
                            @error('email') is-invalid @enderror
                            @endif " value="{{ old('email') }}" required autocomplete="email" @if(null !==old('number')) disabled="disabled" @endif>


                            <input type="tel" placeholder="Телефон" id="numberRegister" name="number" class="form-control type-phone @if(null ===old('number')) disabled @endif" @if(null ===old('number')) disabled="disabled" @endif
                            @if($register)
                                   @error('number') is-invalid @enderror
                            @endif  value="{{ old('number') }}" required autocomplete="email">
                            <span class="form-subbtitle disabled">В формате +7</span>
                        </div>
                        <input type="text" name="invitation_code" class="form-control" value="{{ isset($_GET['ref']) ? $_GET['ref'] : ''}}" id="invitation-inpup" placeholder="Промокод (если есть)">
                        <button type="submit" class="btn btn-red">РЕГИСТРАЦИЯ</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    @endguest
@endsection
