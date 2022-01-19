<?php

$user = $data;
?>
<div class="row">
    <div class="col-md-3">
        <div class="main__aside" >
            <div class="main__dd-btn"></div>
            <div class="main__info">
                <input type="hidden" value="{{$user->id}}" id="user_id">
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
                            <span title="{{ $user->email }}">
                     {{ $user->email  }}
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
                        <button type="button" class="btn remove-pass" data-toggle="modal" data-target="#resetPassword">
                            Сбросить пароль
                        </button>
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
{{--                        <a href="{{route('admin.user.report',$user->id)}}" class="btn remove-pass">Сообщить о нарушении</a>--}}
                        <a data-toggle="modal" data-target="#reportUser" class="btn remove-pass"> Сообщить о нарушении</a>
                    </div>
                    <div class="text-center">
                        <a href="{{route('admin.user_payment_settings',$user->id)}}" class="btn remove-pass">Оплаты</a>
                    </div>
                    <div class="text-center">
                        @if($user->active==1)
                        <a  data-toggle="modal" data-target="#banUser" class="btn remove-pass">Заблокировать</a>
                        @else
                        <a href="{{route('admin.user.unban',$user->id)}}" class="btn remove-pass">Активировать</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reportUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-violation">
                <form method="POST" action="{{ route('admin.user.report',$user->id) }}">
                    @csrf
                    <div class="modal-header-violation pb-20">
                        <label class="modal-title-violation" id="exampleModalLabel">Укажите причину нарушения</label>
                        <button type="button" class="close close-violation" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body-violation">

                            <textarea type="text" class="form-control text-area-type2 " name="reason" placeholder="Причина нарушения"></textarea>

                    </div>

                    <div class="modal-footer-violation p-40">
                        <button class="btn-blue btn-violation" type="submit" >Сообщить о нарушении</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="banUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-violation">
                <form method="POST" action="{{ route('admin.user.ban',$user->id) }}">
                    @csrf
                    <div class="modal-header-violation pb-20">
                        <label class="modal-title-violation" id="exampleModalLabel">Укажите причину блокировки</label>
                        <button type="button" class="close close-violation" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body-violation">
                        <input name="fromModal" type="hidden" value="1">
                        <textarea type="text" class="form-control text-area-type2 " name="reason" placeholder="Причина нарушения"></textarea>
                    </div>
                    <div class="modal-footer-violation p-40">
                        <button class="btn-blue btn-violation" type="submit" >Заблокировать пользователя</button>
                    </div>
                </form>
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
@include('admin.modals')
