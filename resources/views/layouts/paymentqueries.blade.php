<?php
$paids = $data;
?>


<div class="col-md-9">
    <div class="main__content-v2">
        <h2 class="main__content-title">Выплаты</h2>
        <div class="responsive-table" id="payments_table">
            <div class="divTable main__table tabletwo">
                <div class="divTableBody">
                    <div class="divTableRow divTable-head">
                        <div class="divTableCell" style="width: 145px;">Дата</div>
                        <div class="divTableCell" style="width: 250px;">Номер карты</div>
                        <div class="divTableCell text-center" style="width: 240px;">Сумма</div>
                        <div class="divTableCell text-center" style="width: 265px;">Статус</div>
                    </div>
                    @foreach($paids as $paid)
                    <div class="divTableRow t-row">
                        <div class="table-col-1">
                            <div class="divTableCell" style="width: 145px;">{{$paid->updated_at->format('d-m-Y') }}</div>
                            <div class="divTableCell" style="width: 250px;">
                                <span>{{ auth()->user()->setting->card_number }}</span>
                            </div>
                        </div>
                        <div class="table-col-2">
                            <div class="divTableCell text-center" style="width: 240px;">
                                <span>{{ $paid->amount }} ₽</span>
                            </div>
                            <div class="divTableCell text-center" style="width: 265px;">
                                @if($paid->status=='complete')<span class="green">{{ 'переведено' }}</span>@else<span class="orange">{{ 'в ожидании' }}</span>@endif
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>

    </div>


</div>

