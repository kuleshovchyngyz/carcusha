<?php
$leads = $data;
?>


<div class="col-md-9">
    <div class="main__content">
        <h2 class="main__content-title">Авто</h2>
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
                @foreach($leads as $lead)
                <tr>
                    <td class="d-flex">{{ $lead->created_at->format('d-m-Y H:i') }}</td>
                    <td class="snowflake">#{{ $lead->bitrix_user_id }}
                        @if($lead->number==1)
                            {!! file_get_contents(asset('img/qr.svg')) !!}
                        @endif
                    </td>
                    <td>{{ $lead->vendor }} {{ $lead->vendor_model }}, {{ $lead->vendor_year }}</td>
                    <td>{{ $lead->phonenumber }}</td>

                    <td class="main__table-dd-wrap" style="color:{{ $lead->status()->color }};">{{ $lead->status()->name }}

                        <ul class="main__table-dd">
                            <li>
                                <span >{{ $lead->history()==false ? "" : $lead->history()->status()->name }}</span>
                                <span>{{ $lead->history()==false ? "" :  $lead->history()->updated_at->format('d.m.y') }}</span>
                            </li>
                            <li>
                                <span class="text-warning"> {{ $lead->status()->name }}</span>
                                <span>{{ $lead->updated_at->format('d.m.y') }}</span>
                            </li>
                        </ul>
                    </td>






                    <td class="snowflake">{{ $lead->all_amount() }} ₽
                        @if(!$lead->is_on_pending() && $lead->all_amount()>0)
                        {!! file_get_contents(asset('img/snowflake.svg')) !!}

                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
