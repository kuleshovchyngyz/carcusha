@extends('layouts.app')

@section('content')
    <div class="authentication">
        <img src="{{ asset('img/logo-aut.png') }}" alt="" class="logoauth">
        <div class="tab-wrap">
            <div class="form-head">
                <h3 class="form-title">Подтверждение телефона</h3>
            </div>
            <div class="confirm-info">
                Мы отправили SMS с кодом
                подтверждения на Ваш номер
                <strong>{{$number}}.</strong>
            </div>
            <div class="tab-content tab-content--custom" id="pills-tabContent">
                <div class="tab-pane fade show active" id="authenticationtab" role="tabpanel" aria-labelledby="authentication-tab">
                    <form action="{{ route('auth.SmsVerification-code') }}" method="POST" id="authentication" class="text-center">
                        @csrf
                        <input type="hidden" name="number" value="{{$number}}">
                        @if(isset($invitation_code))<input type="hidden" name="invitation_code" value="{{$invitation_code}}">@endif

                        <input type="number" name="code" placeholder="Код из SMS" class="form-control  @error('code') is-invalid @enderror" value="{{ old('password') }}" >
                        @error('code')
                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                        @enderror
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
                        <button type="submit" class="btn btn-red">Подтвердить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
