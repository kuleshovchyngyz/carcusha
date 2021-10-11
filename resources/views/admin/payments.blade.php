<?php

$paids = $data;
?>



<div class="main__content">
    <h2 class="main__content-title">Выплаты</h2>
    <input type="text" class="form-control search-tabel-mob" placeholder="Поиск по пользователям">
    <div class="table-responsive">
        <table class="main__table">
            <thead>
            <tr>
                <th>Дата</th>
                <th>Логин</th>
                <th>Сумма </th>
                <th>Статус</th>
            </tr>
            </thead>

            <tbody>
                @foreach($paids as $paid)
                        <tr>
                            <td>{{ $paid->updated_at->format('Y-m-d H:i')  }}</td>
                            <td><a href="{{ route('admin.user', $paid->user->id) }}" class="main__table-link">User #{{ $paid->user->id  }}<span class="mob-stsus text-success"></span></a></td>
                            <td>{{ $paid->amount }} ₽</td>
                            <td>
                                @if($paid->status=='pending')
                                <a  class="btn header__btn color-red pay_button" data-id="{{ $paid->id }}"  id="pay_button{{ $paid->id }}">Выплатить
                                </a>
                                @else
                                    <a  class="btn  green pay_button" data-id="{{ $paid->id }}"  id="pay_button{{ $paid->id }}">Выплачено
                                    </a>
                                @endif
                            </td>
                        </tr>

                @endforeach
            </tbody>
        </table>
    </div>
</div>
