<?php
    if(!isset($active)){
        $active = "leads";
    }
?>
<div class="col-md-3">
    <div class="main__aside">
        <div class="main__dd-btn"></div>
        <div class="main__info">
            <div class="main_user-name">User # {{ Auth::user()->id }}</div>
            <ul class="main__info-list">
                <li class="main__info-item">
                                    <span>
                                        <strong>Балланс:</strong>
                                    </span>
                    <span>
                                        <strong>{{ Auth::user()->total_amount_from_all() }}</strong> <i class="fa fa-rub" aria-hidden="true"></i>
                                    </span>
                </li>
                <li class="main__info-item">
                                    <span>
                                        Всего лидов:
                                    </span>
                    <span>
                                        {{ Auth::user()->leads->count() }}
                                    </span>
                </li>
            </ul>
        </div>
        <div class="aside-dd">
            <ul class="main__nav">

                <li class="main__nav-item @if($active=="users") active @endif">
                    <a href="{{ route('users.list') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-lid.png') }}" alt="">
                        Лиды
                    </a>
                </li>
                <li class="main__nav-item @if($active=="notifications") active @endif">
                    <a href="{{ route('notification.list') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-alert.png') }}" alt="">
                        Уведомления
                        {!! \App\Models\MessageNotification::where('user_id',auth()->user()->id)->where('seen',0)->count()==0 ? "" : '<span class="main__nav-count">' .\App\Models\MessageNotification::where('user_id',auth()->user()->id)->where('seen',0)->count().'</span>'!!}
                    </a>
                </li>
                <li class="main__nav-item @if($active=="payments") active @endif">
                    <a href="{{ route('payment.list') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-price.png') }}" alt="">
                        Выплаты
                    </a>
                </li>
                <li class="main__nav-item @if($active=="refer") active @endif">
                    <a href="{{ route('refer.list') }}" class="main__nav-link">
                        <img src="{{ asset('img/icon-ref.png')}}" alt="">
                        Реферальная программа
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

            </ul>
            <div class="text-center">
                <a  href="{{ route('customlogout') }}"> Выход из аккаунта</a>
            </div>

        </div>
    </div>
</div>
