<?php
$notifications = $data->toArray();
?>

<div class="col-md-9">
    <div class="main__content">
        <h2 class="main__content-title">Уведомления</h2>
        <ul class="notifications">
            @foreach(array_reverse($notifications) as $notification)
            <li class="notifications__item notifications__new">
               {!! $notification['message'] !!}
            </li>
            @endforeach

        </ul>
    </div>
</div>
<?php
    \App\Models\MessageNotification::where('user_id',auth()->user()->id)->update([
        'seen' => true
    ]);
?>
