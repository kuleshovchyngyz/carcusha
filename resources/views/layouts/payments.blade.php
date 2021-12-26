<?php
$leads = $data;
?>


<div class="col-md-9">
    <div class="main__content-v2"">
        <div class="d-flex main__content-head">
            <h2 class="main__content-title">Начисления</h2>
            <button type="button"  class="red-link ml-auto" data-toggle="modal" data-target="#exampleModal">
                Заказать  выплату
            </button>
        </div>
        <div class="responsive-table" id="accruals_table">
            <div class="divTable main__table tabletwo">
                <div class="divTableBody">
                    <div class="divTableRow divTable-head">
                        <div class="divTableCell" style="width: 145px;">Дата</div>
                        <div class="divTableCell" style="width: 420px;">Автомобиль</div>
                        <div class="divTableCell text-center" style="width: 333px;">Сумма</div>
                    </div>
                    @foreach(auth()->user()->lead_payments() as $payment)
                    <div class="divTableRow t-row">
                        <div class="table-col-1">
                            <div class="divTableCell" style="width: 145px;">{{$payment->created_at->format('d-m-Y') }}</div>
                            <div class="divTableCell" style="width: 420px;">
                                <span>{{ $payment->reasons->lead()->vendor }} {{ $payment->reasons->lead()->vendor_model }}, {{ $payment->reasons->lead()->vendor_year }}</span>
                            </div>
                        </div>
                        <div class="table-col-2">
                            <div class="divTableCell text-center" style="width: 333px;">
                                <span>{{$payment->amount }} ₽</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>



    </div>


</div>
@include('layouts.modals')
