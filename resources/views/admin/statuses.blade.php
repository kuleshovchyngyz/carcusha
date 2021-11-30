
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
                <th class="crmstatus">Статус CRM</th>
                <th class="status">Статус</th>
                <th class="comments">Коментарии</th>
                <th class="amount">₽</th>
                <th class="thblue"><div class="square_blue"></div></th>
                <th class="thgreen"><div class="square_green"></div></th>
                <th class="thred"><div class="square_red"></div></th>

                <th class="notify">Уведом.</th>
            </tr>
            </thead>
            <tbody>
            @foreach($statuses as $status)
                    <tr>
                        <td>{{ $status->index.': '.$status->name  }}</td>
                        @if($status->user_statuses==null)
                            <td><input class="status_input" id="status" name="{{'name=='.$status->id}}" type="text" value=""></td>
                            <td><input class="comments_input"  name="{{'comments=='.$status->id}}" type="text" value=""></td>
                            <td><input class="amount_input" name="{{'amount=='.$status->id}}" type="text" value=""></td>

                            <td><input class="amount_input" name="{{'color=='.$status->id}}" type="radio" value="{{ $status->color }}"></td>
                            <td><input class="amount_input" name="{{'color=='.$status->id}}" type="radio" value="{{ $status->color }}"></td>
                            <td><input class="amount_input" name="{{'color=='.$status->id}}" type="radio" value="{{ $status->color }}"></td>

                            <td><input class="notify_input" name="{{'notify=='.$status->id}}" class="form-check-input" type="checkbox"  id="flexCheckDefault"></td>
                        @else
                            <td><input class="status_input" name="{{'name=='.$status->id}}" type="text" value="{{ $status->user_statuses->name }}"></td>
                            <td><input class="comments_input" name="{{'comments=='.$status->id}}" type="text" value="{{ $status->user_statuses->comments }}"></td>
{{--                            <td><input class="comments_input" name="{{'comments=='.$status->id}}" type="text" value="{{ $status->user_statuses->comments }}"></td>--}}
                            <td><input class="amount_input" name="{{'amount=='.$status->id}}" type="text" value="{{ $status->user_statuses->amount }}"></td>

                            <td><input class="blue_input" name="{{'color=='.$status->id}}" type="radio" value="#2D9CDB" {{ selectedColor( $status->color,'blue') }}></td>
                            <td><input class="green_input" name="{{'color=='.$status->id}}" type="radio" value="#27AE60" {{ selectedColor( $status->color,'green') }}></td>
                            <td><input class="red_input" name="{{'color=='.$status->id}}" type="radio" value="#EB5757" {{ selectedColor( $status->color,'red') }}  ></td>

                            <td><input class="notify_input" name="{{'notify=='.$status->id}}" class="form-check-input" type="checkbox"  id="flexCheckDefault" @if($status->user_statuses->notify==1) checked @endif></td>
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
