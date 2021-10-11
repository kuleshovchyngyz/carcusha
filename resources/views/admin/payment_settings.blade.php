<?php

$payment = $data['payment'];
?>

<div class="main__content">
    <h2 class="main__content-title">Оплаты</h2>
    <form  method="POST" action="{{ route('admin.store_payment_settings') }}" >
        @csrf
        <div class="row">
{{--            <div class="col-md-6">--}}
{{--                <label class="new-satings-input-wrap">--}}
{{--                    Стоимость лида в работе:--}}
{{--                    <input type="text" name="initial" value ={{ $payment->first()->type('initial') }} class="form-control @error('initial') is-invalid @enderror" >--}}
{{--                    @error('initial')--}}
{{--                    <span class="invalid-feedback" role="alert">--}}
{{--                        <strong>{{ $message }}</strong>--}}
{{--                    </span>--}}
{{--                    @enderror--}}
{{--                </label>--}}
{{--            </div>--}}
{{--            <div class="col-md-6">--}}
{{--                <label class="new-satings-input-wrap">--}}
{{--                    Стоимость завершённого лида:--}}
{{--                    <input type="text" name="success" value="{{ $payment->first()->type('success') }}" class="form-control">--}}
{{--                </label>--}}
{{--            </div>--}}


        </div>
        <div class="row">
            <div class="col-md-6">
                <label class="new-satings-input-wrap">
                    Стоимость реферальный программы:
                    <input type="text" name="refer" value="{{ $payment->first()->type('refer') }}" class="form-control">
                </label>
            </div>
            <div class="col-md-6">
                <label class="new-satings-input-wrap">
                    Стоимость завершённого лида реферальный программы:
                    <input type="text" name="percentage" value="{{ $payment->first()->type('percentage') }}%" class="form-control">
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label class="new-satings-input-wrap">
                    минимальная сумма для вывода:
                    <input type="text" name="MinAmountOfPayment" value="{{ $payment->first()->type('MinAmountOfPayment') }}" class="form-control">
                </label>
            </div>

        </div>
{{--        <hr class="bottom-line">--}}





        <div class="text-center">
            <button class="btn btn-red">Сохранить</button>
        </div>
    </form>
</div>

