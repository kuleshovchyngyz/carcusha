@extends('layouts.app')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="authentication">
        <img src="{{ asset('svg/logo_cvet.svg') }}" alt="" class="logoauth">
        <div class="tab-wrap">
            <div class="form-head">
                <h3 class="form-title">@if(!isset($reset)){{'Подтверждение почты'}}@else{{'Восстановление пароля'}}@endif</h3>
            </div>
            <div class="confirm-info">
                Мы отправили письмо с кодом
                подтверждения на Вашу почту
                <strong> {{ $email }}.</strong>
                Не забудьте проверить папку
                        <strong>"СПАМ"</strong>
            </div>
            <div class="tab-content tab-content--custom" id="pills-tabContent">
                <div class="tab-pane fade show active" id="authenticationtab" role="tabpanel" aria-labelledby="authentication-tab">
                    @if(!isset($confirmEmail))
                    <form action="{{ route('auth.EmailVerification-code') }}" method="POST" id="authentication" class="text-center">
                        @else
                            <form action="{{ route('confirm.email') }}" method="POST" id="authentication" class="text-center">
                        @endif
                        @csrf

                        <input type="hidden" name="email" value="{{$email}}">

                        @if(isset($invitation_code))<input type="hidden" name="invitation_code" value="{{$invitation_code}}">@endif
                        @if(isset($reset))<input type="hidden" name="reset" value="{{$reset}}">@endif
                        <input type="number" name="code" placeholder="Код из письма" class="form-control  @error('code') is-invalid @enderror" value="{{ old('password') }}" >
                        @error('code')
                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                        @enderror
                        @if(!isset($confirmEmail))
                            <div class="type-pass">

                                <input id="password" type="password" placeholder="Придумайте пароль" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" name="password" required autocomplete="new-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                @if(!$errors->has('password'))
                                    <label><input type="checkbox" class="password-checkbox"></label>
                                @endif

                            </div>
                            <div class="type-pass">
                                <input id="password-confirm" type="password" class="form-control"  placeholder="Подтвердите пароль" name="password_confirmation" required autocomplete="new-password">
                                <label><input type="checkbox" class="password-checkbox"></label>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-blue">Подтвердить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
