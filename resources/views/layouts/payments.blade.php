<?php
$leads = $data;
?>


<div class="col-md-9">
    <div class="main__content">

            <h2 class="main__content-title">Лиды</h2>



        <div class="table-responsive">
            <table class="main__table">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Номер</th>
                    <th>Автомобиль</th>
                    <th>Телефон</th>
                    <th>Статус</th>
                    <th>Оплата</th>
                </tr>
                </thead>
                <tbody>
{{--                @dd(auth()->user()->lead_payments())--}}
                @foreach(auth()->user()->lead_payments() as $payment)
                    <tr>
                        <td>{{$payment->created_at->format('d-m-Y H:i') }}</td>
                        <td>{{ $payment->reasons->table_id }}</td>
                        <td>{{ $payment->reasons->lead()->vendor }} {{ $payment->reasons->lead()->vendor_model }}, {{ $payment->reasons->lead()->vendor_year }}</td>
                        <td>{{ $payment->reasons->lead()->phonenumber }}</td>
							 @if($payment->status()==false)
							<td style="color:{{ $payment->reasons->lead()->status->color }};">{{ $payment->reasons->lead()->status->name }}</td>

							@else
							<td style="color:{{ $payment->status()->color }};">{{ $payment->status()->name }}</td>
							@endif

                        <td>{{$payment->amount }} ₽</td>
{{--                        <td>{{$payment->payment_amount()->amount }} ₽</td>--}}
                    </tr>
                @endforeach
                </tbody>
            </table>
            <br>
            <div class="text-center">
                <button type="button"  class="btn btn-red" data-toggle="modal" data-target="#exampleModal">
                    Заказать  выплату
                </button>
            </div>
        </div>
    </div>


</div>
@include('layouts.modals')
