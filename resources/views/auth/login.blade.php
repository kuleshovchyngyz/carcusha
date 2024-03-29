{{--@if($errors->any())--}}
{{--    @dump(old('email'))--}}
{{--    @dump(old('number'))--}}
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
        <img src="{{ asset('svg/logo_head_desk.svg') }}" alt="" class="logoauth">

        <div class="tab-wrap">
            <ul class="nav mb-3" id="pills-tab" role="tablist">
                <li class="nav__tab-item">
                    <a class="nav-link nav-link-aut {{$login ? 'active' : ''}}" id="authentication-tab" data-toggle="pill" href="#authenticationtab" role="tab"
                       aria-controls="authentication-tab" aria-selected="true">Вход</a>
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
                            <span class="phone @if(null !==old('number')||(null ===old('number')&&null ===old('email')))  active @endif">Телефон</span>
                            <label class="logintab type-field-select @if(null !==old('number')||(null ===old('number')&&null ===old('email')))  number @else mail @endif">
                                <input type="checkbox" name="type-field" id="type_field" checked>

                            </label>
                            <span class="mail @if(null ===old('number')&&null !==old('email')) active @endif">E-Mail</span>
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
                            <input type="mail" placeholder="E-Mail" name="email" class="form-control type-mail @if(null ===old('email')) disabled @endif
                            @if($login)
                            @error('email') is-invalid @enderror
                            @endif " value="{{ old('email') }}" required autocomplete="email" @if(null ===old('email')) disabled="disabled" @endif>


                            <input type="tel" placeholder="Телефон" id="numberLogin" name="number" class="form-control type-phone @if(null !==old('email')) disabled @endif" @if(null !==old('email')) disabled="disabled" @endif
                            @if($login)
                            @error('number') is-invalid @enderror
                                   @endif  value="{{ old('number') }}" required autocomplete="email">
                            <span class="form-subbtitle">
                            @if(null!==old('number')&&null===old('email')||null===old('number')&&null===old('email'))В формате +7
                            @endif
                            </span>
                        </div>
                        <div class="type-pass">

                            <input id="password" type="password" placeholder="Пароль"  class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" required autocomplete="current-password">
                            <label><input type="checkbox" class="password-checkbox"></label>
                        </div>
                        @if (Route::has('password.request'))
                            <a  class="red-link" href="{{ route('auth.forgot.password') }}">Забыли пароль?</a>
                        @endif
                        <button class="btn btn-blue">Войти</button>
                    </form>
                </div>



                <div class="tab-pane fade {{ $register  ? 'active show' : ''}}" id="registrationtab" role="tabpanel" aria-labelledby="registration-tab">
                    <form id="registration" method="POST" action="{{ route('auth.verification-code') }}" class="text-center">
                        @csrf
                        <div class="type-field d-flex">
                                <span class="phone @if(null !==old('number')||(null ===old('number')&&null ===old('email')))  active @endif">Телефон</span>
                            <label class="registertab type-field-select @if(null !==old('number')||(null ===old('number')&&null ===old('email')))  number @else mail @endif">
                                <input type="checkbox" name="type-field" id="type_field" checked>

                            </label>
                            <span class="mail @if(null ===old('number')) active @endif">E-Mail</span>

                        </div>

                        <div class="pos-r">
                            <input type="mail" placeholder="E-Mail" name="email" class="form-control type-mail @if(null ===old('email')) disabled @endif
                            @if($register)
                            @error('email') is-invalid @enderror
                            @endif " value="{{ old('email') }}" required autocomplete="email" @if(null ===old('email')) disabled="disabled" @endif>


                            <input type="tel" placeholder="Телефон" id="numberRegister" name="number" class="form-control type-phone @if(null !==old('email')) disabled @endif" @if(null !==old('email')) disabled="disabled" @endif
                            @if($register)
                                   @error('number') is-invalid @enderror
                            @endif  value="{{ old('number') }}" required autocomplete="email">
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
                            <span class="form-subbtitle">В формате +7</span>
                        </div>
                        <div class="main__setting-item">

                                <div>
                                    <select name="major" class="form-control">
                                        <option>Род деятельности</option>
                                        {!!    ViewService::init()->view('majors') !!}
                                    </select>
                                    @error('major')
                                        <span class="invalid-feedbackerror" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>


                        </div>
                        <div class="" id="invitation-code">
                             <input type="text" name="invitation_code" class="form-control" value="{{ isset($_GET['ref']) ? $_GET['ref'] :  old('invitation_code') }}" id="invitation-inpup" placeholder="Промокод (если есть)"  @error('email') is-invalid @enderror>
                        </div>

                            <p class="error text-danger d-none">Такого промокода не существует</p>

{{--                        @error('invitation_code')--}}
{{--                        <span class="invalid-feedbackerror" role="alert">--}}
{{--                                      <strong>{{ $message }}</strong>--}}
{{--                            </span>--}}
{{--                        @enderror--}}

{{--                        <input type="button" value="РЕГИСТРАЦИЯ" class="btn btn-blue submitRegisterForm" >--}}

                        <button type="button" id="registerButton" class="btn btn-blue">
                            РЕГИСТРАЦИЯ
                        </button>



                    </form>

                </div>
            </div>
        </div>
    </div>

        <div class="modal fade show" id="publicOfferWindow" tabindex="-1" aria-labelledby="exampleModalCenterTitle" style="padding-right: 17px;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 900px">
                <div class="modal-info">
                    <div class="container ">
                        <div class="d-flex justify-content-between">
                            <div class="publicOffer"><h1>{!! ViewService::init()->type(['title'])->view('publicOffer') !!}</h1></div>

                        </div>
                        <div class="publicOffer">{!! ViewService::init()->type(['text'])->view('publicOffer') !!}</div>
                        <div class="text-left">
                            <label class="checkbox-form">
                                <input type="checkbox"  class="checkbox-agree" @if($errors->any()) checked @endif required>
                                <span class="checkbox"></span>
                                Соглашаюсь с Публичной офертой
                            </label>
{{--                            <a href="{{ route('public_offer') }}" target="_blank" class="red-link">Публичной офертой</a>--}}
                        </div>
                        <div class="d-flex flex-wrap align-items-baseline">
                            <div class="col-md-6">
                                <input type="button" value="СОГЛАСИТЬСЯ" class="btn btn-disabled submitRegisterForm" >
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <a href="{{ route('login') }}" class="red-link back">Вернуться назад</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endguest
@endsection
