<?php
$paids = $data;
?>


<div class="col-md-9">
    <div class="main__content">
        <h2 class="main__content-title">Лиды</h2>
        <div class="table-responsive">
            <table class="main__table">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Номер карты </th>
                    <th>Сумма </th>
                    <th>Статус</th>
                </tr>
                </thead>
                <tbody>
                @foreach($paids as $paid)
                    <tr>
                        <td>{{$paid->updated_at->format('d-m-Y H:i') }}</td>
                        <td>#{{ auth()->user()->setting->card_number }}</td>
                        <td>{{ $paid->amount }} </td>
                        @if($paid->status=='complete')<td class="green">{{ 'переведено' }}</td>@else<td class="orange">{{ 'в ожидании' }}</td>@endif
                    </tr>
                @endforeach
                </tbody>
            </table>
            <br>

        </div>
    </div>


</div>

