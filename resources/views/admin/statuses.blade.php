
<?php

$statuses = $data;
?>
<div class="main__content">
    <h2 class="main__content-title">Статусы</h2>
    <input type="text" class="form-control search-tabel-mob" placeholder="Поиск по пользователям">
    <form  method="POST" action="{{ route('admin.store_user_statuses') }}" >
        @csrf
    <div class="table-responsive">
        <table class="main__table statuses">
            <thead>
            <tr>
                <th>Статус CRM</th>
                <th class="status">Статус</th>
                <th>Вознаграждение (₽)</th>
                <th>Отправлять уведомление</th>
            </tr>
            </thead>
            <tbody>
            @foreach($statuses as $status)
                    <tr>
                        <td>{{ $status->index.': '.$status->name  }}</td>
                        @if($status->user_statuses==null)
                            <td><input class="status_input" id="status" name="{{'name=='.$status->id}}" type="text" value=""></td>
                            <td><input  name="{{'amount=='.$status->id}}" type="text" value=""></td>
                            <td><input name="{{'notify=='.$status->id}}" class="form-check-input" type="checkbox"  id="flexCheckDefault"></td>
                        @else
                            <td><input class="status_input" name="{{'name=='.$status->id}}" type="text" value="{{ $status->user_statuses->name }}"></td>
                            <td><input name="{{'amount=='.$status->id}}" type="text" value="{{ $status->user_statuses->amount }}"></td>
                            <td><input name="{{'notify=='.$status->id}}" class="form-check-input" type="checkbox"  id="flexCheckDefault" @if($status->user_statuses->notify==1) checked @endif></td>
                        @endif
                    </tr>
            @endforeach
            </tbody>
        </table>
    </div>
        <br>
        <br>

        <div class="text-center">
            <button class="btn btn-red">Сохранить</button>
        </div>
    </form>
</div>
