<?php
if(!isset($active)){
    $active = "leads";
}
?>
<div class="col-md-3">
    <div class="main__aside">

        <div class="main__info">
            <div class="d-flex justify-content-between">
                <div class="main_user-name">User # {{ Auth::user()->id }}
                </div>
                <div class="icons">
                    <span class="info-icon tb-icon dd-btn">
                                                    <div class="dd-btn__info">
                                                        <div class="dd-btn__info-content">
                                                            Сумма за добавление авто - 50 ₽.
                        Сумма за завершение сделки - 10000 ₽
                                                        </div>
                                                    </div>
                                                </span>
                    <img src="http://partnersonserver/svg/qrcode.svg" class="qrcodeSvg" alt="">

                </div>

            </div>
            <ul class="main__info-list">
                <li class="main__info-item">
                  <span class="z-index-3">
                                        <span class="info-icon tb-icon dd-btn">
                                            <div class="dd-btn__info">
                                                <div class="dd-btn__info-content">
                                                    {{ ViewService::init()->view('paymentAmountsDetail') }}
                                                </div>
                                            </div>
                                        </span>
                                        <strong>Балланс:</strong>
                                    </span>
                            <span>
                    <strong>{{ Auth::user()->balance->balance }}
                    </strong>
                    <i class="fa fa-rub" aria-hidden="true">
                    </i>
                  </span>
                </li>
                <li class="main__info-item">
                  <span>
                    Заморожено:
                  </span>
                  <span>
                    {{ Auth::user()->SumOfPendingAmount() }} <i class="fa fa-rub" aria-hidden="true">
                    </i>
                  </span>
                </li>
                <li class="main__info-item paid" style="display: none">
                    <span>Выведено: </span>
                    <span>{{ auth()->user()->sum_of_paids() }}  <i class="fa fa-rub" aria-hidden="true">
                    </i></span>
                </li>
            </ul>
            <div class="angle_toggle">
                <i class="fa fa-angle-up fa-angle-down">
                </i>


            </div>

        </div>
        <div class="aside-dd">
            <ul class="main__nav d-flex flex-column">
                <li class="main__nav-item @if($active=="leads") active @endif">
                    <a href="{{ route('lead.list') }}" class="main__nav-link">
                        <img src="{{ asset('svg/menu_avto.svg') }}" alt="">
                        Авто
                        <span class="main__nav-count">
                        {{ Auth::user()->leads->count() }}
                        </span>
                    </a>
                </li>
                <li class="main__nav-item @if($active=="notifications") active @endif">
                    <a href="{{ route('notification.list') }}" class="main__nav-link">
                        <img src="{{ asset('svg/menu_uved.svg') }}" alt="">
                        Уведомления
                        {!! \App\Models\MessageNotification::where('user_id',auth()->user()->id)->where('seen',0)->count()==0 ? "" : '
                        <span class="main__nav-count">' .\App\Models\MessageNotification::where('user_id',auth()->user()->id)->where('seen',0)->count().'
                        </span>'!!}
                    </a>
                </li>
                <li class="main__nav-item @if($active=="paymentqueries") active @endif">
                    <a href="{{ route('payment.paymentqueries') }}" class="main__nav-link">
                        <img src="{{ asset('svg/menu_viplat.svg') }}" alt="">
                        Выплаты
                    </a>
                </li>

                <li class="main__nav-item @if($active=="payments") active @endif">
                    <a href="{{ route('payment.list') }}" class="main__nav-link">
                        <img src="{{ asset('svg/menu_nachis.svg') }}" alt="">
                        Начислено
                    </a>
                </li>



                <li class="main__nav-item @if($active=="refer") active @endif">
                    <a href="{{ route('refer.list') }}" class="main__nav-link">
                        <img src="{{ asset('svg/menu_ref.svg')}}" alt="">
                        Реферальная программа
                    </a>
                </li>
                <li class="main__nav-item  @if($active=="promo") active @endif">
                    <a href="{{ route('promo') }}" class="main__nav-link">
                        <img src="{{ asset('svg/menu_promo.svg')}}" alt="">
                        Промоматериалы
                    </a>
                </li>
                <li class="main__nav-item  @if($active=="settings") active @endif">
                    <a href="{{ route('settings') }}" class="main__nav-link">
                        <img src="{{ asset('svg/menu_nastr.svg')}}" alt="">
                        Настройки
                    </a>
                </li>
                <li class="main__nav-item @if($active=="help") active @endif">
                    <a href="{{ route('help') }}" class="main__nav-link">
                        <img src="{{ asset('svg/menu_pomosh.svg') }} " alt="">
                        Помощь
                    </a>
                </li>
                <li class="main__nav-item @if($active=="updates") active @endif">
                    <a href="{{ route('updates') }}" class="main__nav-link">
                        <img src="{{ asset('svg/menu_obnov.svg') }} " alt="">
                        Обновления
                    </a>
                </li>
                <li  class="main__nav-item">
                    <a  href="{{ route('customlogout') }}" style="color:var(--color-blue);"> Выход из аккаунта
                    </a>
                </li>
            </ul>


            <div class="text-center angle_toggle_media">
                <i class="fa fa-angle-up fa-angle-down">
                </i>
            </div>
        </div>
    </div>
</div>

