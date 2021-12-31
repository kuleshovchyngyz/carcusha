{{--@if($errors->any())--}}
{{--    {{ implode('', $errors->all(':message')) }}--}}
{{--@endif--}}
@extends('layouts.app')
@include('layouts.message')
@section('content')

    @guest
        <div class="authentication">
            <img src="{{ asset('img/logo-aut.png') }}" alt="" class="logoauth">
            <div class="tab-wrap">
                <div class="form-head"><h3 class="form-title">Восстановить пароль</h3></div>
                <div class="tab-content tab-content--custom" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="authenticationtab" role="tabpanel" aria-labelledby="authentication-tab">
                        <form id="registration" method="POST" action="{{ route('auth.reset-password-code') }}" class="text-center">
                            @csrf
                            <div class="type-field d-flex">
                                <span class="phone">Телефон</span>
                                <label class="type-field-select @if(null ===old('number')){{'mail'}}@endif">
                                    <input type="checkbox" name="type-field" id="type_field">
                                </label>
                                <span class="mail active">E-Mail</span>
                            </div>
                            @error('email')
                            <span class="invalid-feedbackerror" role="alert">
                                      <strong>{{ $message }}</strong>
                            </span>
                            @enderror


                                @error('number')
                                <span class="invalid-feedbackerror" role="alert">
                                          <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                            <div class="pos-r">
                                <input type="mail" placeholder="E-Mail" name="email" class="form-control type-mail @if(null !==old('number')) disabled @endif

                                @error('email') is-invalid @enderror
                                    " value="{{ ViewService::init()->view('email') }}" required autocomplete="email" @if(null !==old('number')) disabled="disabled" @endif>


                                <input type="tel" placeholder="Телефон" id="number" name="number" class="form-control type-phone @if(null ===old('number')) disabled @endif" @if(null ===old('number')) disabled="disabled" @endif

                                @error('number') is-invalid @enderror
                                       value="{{ old('number') }}" required autocomplete="email">
                                @if(null !==old('number'))<span class="form-subbtitle">В формате +7</span>@endif
                            </div>
                            <button class="btn btn-red">Восстановить пароль</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>









    @endguest
@endsection

