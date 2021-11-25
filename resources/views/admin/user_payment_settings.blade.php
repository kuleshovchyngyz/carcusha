<?php

$payment = \App\Models\PaymentAmount::all();
?>

<div class="col-md-9">

    <div class="main__content">

            <h2 class="main__content-title">Оплаты</h2>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckUniquePayment" {{ ViewService::init($user)->view('isUniquePaymentChecked') }}>
                <label class="form-check-label" for="flexCheckDefault" >
                    Использовать уникальные цены
                </label>
            </div>
        <div class="uniquePayment {{ ViewService::init($user)->view('isUniquePayment') }}">
            <form  method="POST" action="{{ route('admin.store_user_payment_settings') }}" >
                <input  type="hidden" name="user_id" value="{{$user->id}}" >
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label class="new-satings-input-wrap">
                            Стоимость реферальный программы:
                            <input type="text" name="refer" value=" {{ ViewService::init($user,'uniqueAmount')->view('amount_of_referral_payment') }}" class="form-control">

                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="new-satings-input-wrap">
                            Стоимость завершённого лида реферальный программы:
                            <input type="text" name="percentage" value="{{  ViewService::init($user,'uniqueAmount')->view('amount_of_percentage_payment') }}%" class="form-control">
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="new-satings-input-wrap">
                            минимальная сумма для вывода:
                            <input type="text" name="MinAmountOfPayment" value="{{ ViewService::init($user,'uniqueAmount')->view('amount_of_min_payment') }}" class="form-control">
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="new-satings-input-wrap">
                            Стоимость лида в работе:
                            <input type="text" name="initial" value="{{ ViewService::init($user,'uniqueAmount')->view('amount_of_initial_payment') }}" class="form-control">
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="new-satings-input-wrap">
                            Стоимость завершённого лида:
                            <input type="text" name="success" value="{{ ViewService::init($user,'uniqueAmount')->view('amount_of_success_payment') }}" class="form-control">
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="new-satings-input-wrap">
                            Нулевая вознаграждения:
                            <input type="text" name="nothing" value="{{ ViewService::init($user,'uniqueAmount')->view('amount_of_nothing_payment') }}" class="form-control">
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="new-satings-input-wrap">
                            Отрицательный:
                            <input type="text" name="rejected" value="{{ ViewService::init($user,'uniqueAmount')->view('amount_of_rejected_payment') }}" class="form-control">
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="new-satings-input-wrap">
                            <b>firstPayment</b> (Первая выплата при регистрации использовался промокод):
                            <input type="text" name="firstPayment" value="{{ ViewService::init($user,'uniqueAmount')->view('amount_of_firstPayment') }}" class="form-control">
                        </label>
                    </div>
                </div>
                {{--        <hr class="bottom-line">--}}

                <div class="text-center">
                    <button class="btn btn-red">Сохранить</button>
                </div>
            </form>
        </div>

    </div>


</div>
