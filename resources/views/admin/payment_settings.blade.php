<?php

$payment = $data['payment'];
?>

<div class="main__content">
    <h2 class="main__content-title">Оплаты</h2>
    <form  method="POST" action="{{ route('admin.store_payment_settings') }}" >
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label class="new-satings-input-wrap">
                    Стоимость реферальный программы:
                    <input type="text" name="refer" value=" {{ ViewService::init()->view('amount_of_referral_payment') }}" class="form-control">

                </label>
            </div>
            <div class="col-md-6">
                <label class="new-satings-input-wrap">
                    Стоимость завершённого лида реферальный программы:
                    <input type="text" name="percentage" value="{{  ViewService::init()->view('amount_of_percentage_payment') }}%" class="form-control">
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label class="new-satings-input-wrap">
                    минимальная сумма для вывода:
                    <input type="text" name="MinAmountOfPayment" value="{{ ViewService::init()->view('amount_of_min_payment') }}" class="form-control">
                </label>
            </div>

            <div class="col-md-6">
                <label class="new-satings-input-wrap">
                    <b>initial</b> (Стоимость лида в работе):
                    <input type="text" name="initial" value="{{ ViewService::init()->view('amount_of_initial_payment') }}" class="form-control">
                </label>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label class="new-satings-input-wrap">
                    <b>success</b> (Стоимость завершённого лида):
                    <input type="text" name="success" value="{{ ViewService::init()->view('amount_of_success_payment') }}" class="form-control">
                </label>
            </div>

            <div class="col-md-6">
                <label class="new-satings-input-wrap">
                    <b>nothing</b> (Нулевая вознаграждения):
                    <input type="text" name="nothing" value="{{ ViewService::init()->view('amount_of_nothing_payment') }}" class="form-control">
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label class="new-satings-input-wrap">
                    <b>rejected</b> (Отрицательный):
                    <input type="text" name="rejected" value="{{ ViewService::init()->view('amount_of_rejected_payment') }}" class="form-control">
                </label>
            </div>
        </div>
        {{--        <hr class="bottom-line">--}}

        <div class="text-center">
            <button class="btn btn-red">Сохранить</button>
        </div>
    </form>
</div>

