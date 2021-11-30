<?php
$notifications = $data->toArray();
?>

<div class="col-md-9">
    <div class="main__content">
        <div class="text-right">
            <a  class="btn btn-red"  data-toggle="modal" data-target="#notification_setting">
                Настройки
            </a>
        </div>
        <div class="top_links">
            <h2 class="main__content-title">Уведомления</h2>
        </div>
        <ul class="notifications">
            @foreach(array_reverse($notifications) as $notification)
            <li class="notifications__item notifications__new">
               {!! $notification['message'] !!}
            </li>
            @endforeach
        </ul>
    </div>
</div>
@include('layouts.modals')
<?php
    \App\Models\MessageNotification::where('user_id',auth()->user()->id)->update([
        'seen' => true
    ]);
?>
