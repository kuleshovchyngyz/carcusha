<?php
if(!isset($active)){
    $active = "leads";
}
?>
<div class="col-md-3">
    <div class="main__aside">

        <div class="main__info">
            <div class="main_user-name">User # {{ Auth::user()->id }}
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
            <ul class="main__nav">
                <li class="main__nav-item @if($active=="leads") active @endif">
                    <a href="{{ route('lead.list') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-lid.png') }}" alt="">
                        Авто
                        <span class="main__nav-count">
                        {{ Auth::user()->leads->count() }}
                        </span>
                    </a>
                </li>
                <li class="main__nav-item @if($active=="notifications") active @endif">
                    <a href="{{ route('notification.list') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-alert.png') }}" alt="">
                        Уведомления
                        {!! \App\Models\MessageNotification::where('user_id',auth()->user()->id)->where('seen',0)->count()==0 ? "" : '
                        <span class="main__nav-count">' .\App\Models\MessageNotification::where('user_id',auth()->user()->id)->where('seen',0)->count().'
                        </span>'!!}
                    </a>
                </li>
                <li class="main__nav-item @if($active=="paymentqueries") active @endif">
                    <a href="{{ route('payment.paymentqueries') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-price.png') }}" alt="">
                        Выплаты
                    </a>
                </li>

                <li class="main__nav-item @if($active=="payments") active @endif">
                    <a href="{{ route('payment.list') }}" class="main__nav-link">
                        <img src="{{ asset('img/payments.png') }}" alt="">
                        Начислено
                    </a>
                </li>



                <li class="main__nav-item @if($active=="refer") active @endif">
                    <a href="{{ route('refer.list') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-ref.png')}}" alt="">
                        Реферальная программа
                    </a>
                </li>
                <li class="main__nav-item  @if($active=="promo") active @endif">
                    <a href="{{ route('promo') }}" class="main__nav-link">
                        <img src="{{ asset('img/promo.png')}}" alt="">
                        Промоматериалы
                    </a>
                </li>
                <li class="main__nav-item  @if($active=="settings") active @endif">
                    <a href="{{ route('settings') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-settings.png')}}" alt="">
                        Настройки аккаунта
                    </a>
                </li>
                <li class="main__nav-item @if($active=="help") active @endif">
                    <a href="{{ route('help') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-help.png') }} " alt="">
                        Помощь
                    </a>
                </li>
                <li class="main__nav-item @if($active=="help") active @endif">
                    <a href="{{ route('updates') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-update.png') }} " alt="">
                        Обновления
                    </a>
                </li>
            </ul>
            <div class="text-center">
                <a  href="{{ route('customlogout') }}" style="color:#EE1D24;"> Выход из аккаунта
                </a>
            </div>
        </div>
    </div>
</div>

