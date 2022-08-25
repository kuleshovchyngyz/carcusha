

@extends('layouts.app')

@section('content')
    <div class="authentication">
        <img src="{{ asset('svg/logo_head_desk.svg') }}" alt="" class="logoauth">
        <div class="tab-wrap">
            <div class="form-head">
                <h3 class="form-title">{{'Подтверждение телефона'}}</h3>
            </div>
            <div class="confirm-info">
                <div class="confirmation-text">
                    Сейчас вам поступит звонок. Введите
                    последние четыре цифры номера
                </div>

                {{-- <strong>{{$number}}.</strong> --}}

            </div>
            <div class="tab-content tab-content--custom" id="pills-tabContent">
                <div class="tab-pane fade show active" id="authenticationtab" role="tabpanel" aria-labelledby="authentication-tab">


                        <form action="{{ route('confirm.number') }}" method="POST" id="authentication" class="text-center">


                                    @csrf
                                    <input type="hidden" name="number" value="{{$number}}">
                                    <input type="number" name="code" id="confirmationcode" placeholder="Введите код" class="form-control  @error('code') is-invalid @enderror" value="{{ old('password') }}" >
                                    @error('code')
                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror

                                    <button type="submit"  class="btn btn-blue">Далее</button>
                                    {{-- <button type="submit" class="btn btn-blue">Подтвердить</button> --}}
                                    <button  data-tel={{ $number }} class="btn btn-blue btn-disabled-sms" id='timer'>02:01</button>
                                </form>
                </div>
            </div>
        </div>
    </div>

@endsection
