

@extends('layouts.app')

@section('content')
<main class="main">
    <div class="container">
        <div class="row">
            @include('layouts.sidebar',['active'=>'leads'])
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
                                            <span class="icon-group">
                                                @if($lead->status->status_type!="finished")
                                                <button type="button" id="leadPoto" class="gall-icon tb-icon leadPoto {{ $lead->folder }}"
                                                        data-lead-folder="{{ $lead->folder }}"
                                                        data-lead-name="{{ $lead->vendor }} {{ $lead->vendor_model }}, {{ $lead->vendor_year }}"
                                                        data-image-names="{{ implode('||',$images[$lead->folder] ?? []) }}"
                                                        data-toggle="modal"
                                                        data-lead-id="{{ $lead->id }}"
                                                        data-target="#gallModalCenter">
                                                </button>
                                                @endif
                                                </span>
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
                                            <span class="{{$lead->color()}}"> {{ $lead->status->user_statuses->name }}</span>
                                            @if($lead->status->user_statuses->comments!='')
                                                <span class="info-icon tb-icon dd-btn">
                                                    <div class="dd-btn__info">
                                                       {{   shortCodeParse($lead->status->user_statuses->comments) }}
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
                                    @foreach($lead->leadHistory as $history)
                                    <div class="d-flex dd-body__item" style="width: 100%;">
                                        <div class="table-time">{{ $history->created_at->format('d-m-Y H:i') }}</div>
                                        <div class="table-i text-right">
                                            <span class="{{color($history->event)}}"> {{ optional($history->userStatus)->name }} </span>
                                            @if($history->userStatus->comments!='')
                                            <span class="info-icon tb-icon dd-btn">
                                                                <div class="dd-btn__info">
                                                                   {{ shortCodeParse($history->userStatus->comments) }}
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
{{--                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateProjectModal">--}}
{{--                  updateProjectModal--}}
{{--                </button>--}}
            </div>





        </div>

    </div>
</main>
@include('layouts.photomodal')
@endsection
