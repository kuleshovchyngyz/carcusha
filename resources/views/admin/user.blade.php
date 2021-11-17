<?php

$user = $data;
?>
<div class="row">
    <div class="col-md-3">
        <div class="main__aside" >
            <div class="main__dd-btn"></div>
            <div class="main__info">
                <div class="main_user-name" data-user="{{ $user->id }}">User #{{ $user->id }}</div>
                <ul class="main__info-list">
                    <li class="main__info-item">
                  <span>
                  <strong>Балланс:</strong>
                  </span>
                        <span>
                  <strong>{{ $user->balance->balance }}</strong> <i class="fa fa-rub" aria-hidden="true"></i>
                  </span>
                    </li>
                </ul>
                <div class="aside-dd">
                    <ul class="main__info-list">
                        <li class="main__info-item">
                     <span>
                     Регистрация:
                     </span>
                            <span>
                     {{ $user->created_at->format('d.m.y')  }}
                     </span>
                        </li>
                        <li class="main__info-item">
                     <span>
                     Телефон:
                     </span>
                            <span title="{{ $user->setting->number }}">
                     {{ $user->setting->number  }}
                     </span>
                        </li>
                        <li class="main__info-item">
                     <span>
                     E-Mail:
                     </span>
                            <span title="{{ $user->setting->email }}">
                     {{ $user->setting->email  }}
                     </span>
                        </li>
                        <li class="main__info-item">
                     <span>
                     Город:
                     </span>
                            <span title="{{ $user->setting->city }}">
                     {{ $user->setting->city  }}
                     </span>
                        </li>
                    </ul>
                    <div class="text-center mrg-top-20">
                        <button class="btn remove-pass">Сбросить пароль</button>
                    </div>
                </div>
            </div>
            <div class="aside-dd">
                <div class="main__info mrg-top-20">
                    <ul class="main__info-list">
                        <li class="main__info-item">
                     <span>
                     Всего лидов:
                     </span>
                            <span>
                     {{ $user->leads->count()  }}
                     </span>
                        </li>
                        <li class="main__info-item">
                     <span>
                     В работе:
                     </span>
                            <span>
                     {{ $user->pending()  }}
                     </span>
                        </li>
                        <li class="main__info-item">
                     <span>
                     Отклонено:
                     </span>
                            <span>
                     {{ $user->rejected()  }}
                     </span>
                        </li>
                        <li class="main__info-item">
                     <span>
                     Оплачено:
                     </span>
                            <span>
                     {{ $user->paid()  }}₽
                     </span>
                        </li>
                        <li class="main__info-item">
                     <span>
                     Нарушений:
                     </span>
                            <span>
                     {{ $user->number_of_violations()  }}
                     </span>
                        </li>
                        <li class="main__info-item">
                     <span>
                     Статус:
                     </span>
                            <span>
                     {{ $user->status()  }}
                     </span>
                        </li>
                    </ul>
                    <div class="text-center mrg-top-20">
                        <a href="{{route('admin.user.report',$user->id)}}" class="btn remove-pass">Сообщить о нарушении</a>
                    </div>
                    <div class="text-center">
                        <a href="{{route('admin.user_payment_settings',$user->id)}}" class="btn remove-pass">Оплаты</a>
                    </div>
                    <div class="text-center">
                        <a href="{{route('admin.user.ban',$user->id)}}" class="btn remove-pass">{{ $user->active==1 ? 'Заблокировать' : 'Активировать' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(isset($new))
        @include('admin.newleads')
    @elseif(isset($file['include']))
            @include($file['include'],['user' => $user])
    @else
        @include('admin.leads')
    @endif
</div>
