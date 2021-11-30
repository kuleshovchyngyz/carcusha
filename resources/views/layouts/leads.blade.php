<?php
$leads = $data;
?>


<div class="col-md-9">
    <div class="main__content table-content">
        <h2 class="main__content-title">Авто</h2>
        <div class="responsive-table">
            <div class="divTable main__table">
                <div class="divTableBody">
                    <div class="divTableRow divTable-head">
                        <div class="divTableCell" style="width:48px"></div>
                        <div class="divTableCell" style="width: 145px;">Дата</div>
                        <div class="divTableCell" style="width: 213px;">Автомобиль</div>
                        <div class="divTableCell" style="width: 157px;">Телефон</div>
                        <div class="divTableCell" style="width: 264px;">Статус</div>
                        <div class="divTableCell" style="width: 73px;">Оплата</div>
                    </div>
                    @foreach($leads as $key=>$lead)
                    <div class="divTableRow t-row">
                        <div class="divTableCell btn-show" style="width:48px" data-id="info-{{$key+1}}"></div>
                        <div class="table-col-1">
                            <div class="divTableCell" style="width: 145px;">{{ $lead->created_at->format('d-m-Y') }}</div>
                            <div class="divTableCell" style="width: 213px;" data-lead-id="{{ $lead->id }}">
                                <span>{{ $lead->vendor }} {{ $lead->vendor_model }}, {{ $lead->vendor_year }}</span>
                                @if($lead->checked())
                                <span class="danger-icon tb-icon dd-btn">
                                                    <div class="dd-btn__info">
                                                        Данный автомобиль обнаружен
                                                        на досках объявлений.
                                                    </div>
                                </span>
                                @endif
                            </div>
                            <div class="divTableCell" style="width: 157px;">{{ $lead->phonenumber }}</div>
                        </div>
                        <div class="table-col-2">
                            <div class="divTableCell" style="width: 264px;">
                                <span class="{{$lead->color()}}"> {{ $lead->status()->user_statuses->name }}</span>
                                @if($lead->status()->user_statuses->comments!='')
                                    <span class="info-icon tb-icon dd-btn">
                                        <div class="dd-btn__info">
                                           {{   shortCodeParse($lead->status()->user_statuses->comments) }}
                                        </div>
                                    </span>
                                @endif
                            </div>
                            <div class="divTableCell" style="width: 73px;">
                                <span>{{ ViewService::init($lead)->view('total_payments_by_lead') }} ₽</span>
{{--                                <span>{{ $lead->all_amount() }} ₽</span>--}}
                                @if(!$lead->is_on_pending() && $lead->all_amount()>0)
                                    <span class="lock-icon tb-icon dd-btn">
                                       <div class="dd-btn__info">
                                         Сумма заморожена, пока не
                                           не завершаться переговоры.
                                       </div>
                                     </span>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="dd-body" id="info-{{$key+1}}" style="display: none;">
                        @foreach($lead->leadHistory() as $history)
                        <div class="d-flex dd-body__item" style="width: 100%;">
                            <div class="table-time">{{ $history->created_at->format('d-m-Y H:i') }}</div>
                            <div class="table-i text-right">
                                <span>{{ $history-> user_status()->name }}</span>
                                @if($history-> user_status()->comments!='')
                                <span class="info-icon tb-icon dd-btn">
                                                    <div class="dd-btn__info">
                                                       {{ shortCodeParse($history-> user_status()->comments) }}
                                                    </div>
                                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>















{{--<div class="col-md-9">--}}
{{--    <div class="main__content">--}}
{{--        <h2 class="main__content-title">Авто</h2>--}}
{{--        <div class="table-responsive">--}}
{{--            <table class="main__table">--}}
{{--                <thead>--}}
{{--                <tr>--}}
{{--                    <th>Дата</th>--}}
{{--                    <th>Номер</th>--}}
{{--                    <th>Автомобиль</th>--}}
{{--                    <th>Телефон</th>--}}
{{--                    <th>Статус</th>--}}
{{--                    <th>Оплата</th>--}}
{{--                </tr>--}}
{{--                </thead>--}}
{{--                <tbody>--}}
{{--                @foreach($leads as $lead)--}}
{{--                <tr>--}}
{{--                    <td class="d-flex">{{ $lead->created_at->format('d-m-Y H:i') }}</td>--}}
{{--                    <td class="snowflake">#{{ $lead->bitrix_user_id }}--}}
{{--                        @if($lead->number==1)--}}
{{--                            <img src="{{ asset('img/qr.png') }}" alt="">--}}
{{--                        @endif--}}
{{--                    </td>--}}
{{--                    <td>{{ $lead->vendor }} {{ $lead->vendor_model }}, {{ $lead->vendor_year }}</td>--}}
{{--                    <td>{{ $lead->phonenumber }}</td>--}}

{{--                    <td class="main__table-dd-wrap" style="color:{{ $lead->status()->color }};">{{  ViewService::init($lead,'leadStatusName')->view('leadStatusName') }}--}}
{{--                        <ul class="main__table-dd">--}}
{{--                            <li>--}}
{{--                                <span >{{ $lead->history()==false ? "" : $lead->history()->status()->name }}</span>--}}
{{--                                <span>{{ $lead->history()==false ? "" :  $lead->history()->updated_at->format('d.m.y') }}</span>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                                $lead->status()->name--}}
{{--                                <span class="text-warning"> {{  $lead->status()->name }}</span>--}}
{{--                                <span>{{ $lead->updated_at->format('d.m.y') }}</span>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </td>--}}






{{--                    <td class="snowflake">{{ $lead->all_amount() }} ₽--}}
{{--                        @if(!$lead->is_on_pending() && $lead->all_amount()>0)--}}
{{--                            @dd(asset('img/snowflake.svg'))--}}
{{--                        {{ ViewService::init()->view('snowflake') }}--}}

{{--                            <img src="{{ asset('img/snowflake.png') }}" alt="">--}}

{{--                        @endif--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--                @endforeach--}}
{{--                </tbody>--}}
{{--            </table>--}}

{{--        </div>--}}
{{--    </div>--}}

{{--</div>--}}
