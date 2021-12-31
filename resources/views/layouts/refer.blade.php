<div class="col-md-9">

    <div class="main__content-v2 refer">
        <h2 class="main__content-title">Реферальная программа</h2>
        <div class="main__setting-row">
            <div class="row">
                <div class="col-md-6">
                    <div class="main__setting-item main__setting-item--edit">
                        <div>Реферальная ссылка:
                            <input type="text" id="reference" class="form-control hidden" readonly="" placeholder="Не указан" value="{{ route('login') }}?ref={{ Auth::user()->invitation_code }}">
                            <button class="btn btn--copy"></button>
                            <ul class="main__info-list">
                                <li class="main__info-item">
                                                    <span>
                                                        <strong>Промокод:</strong>
                                                    </span>
                                    <span class="z-index-3">
                                                        <strong>{{ Auth::user()->invitation_code }}</strong>
                                                        <span class="info-icon tb-icon dd-btn">
                                                            <div class="dd-btn__info">
                                                                <div class="dd-btn__info-content">
                                                                    Партнеры, которые введут этот промокод, будут считаться привлеченными вами
                                                                </div>
                                                            </div>
                                                        </span>
                                                    </span>


                                </li>
                            </ul>
                        </div>
                    </div>
                </div>



                <div class="col-md-6">
                    <div class="main__setting-item">
                        <ul class="main__info-list setting-right-list">
                            <li class="main__info-item">
                                                <span>
                                                    <strong>Всего партнёров:</strong>
                                                </span>
                                <span class="z-index-3">
                                                    <strong>{{ Auth::user()->partners()->count() }}</strong>
                                                </span>
                            </li>
                            <li class="main__info-item">
                                                <span>
                                                    <strong>Партнёрских лидов:</strong>
                                                </span>
                                <span class="z-index-3">
                                                    <strong>{{ Auth::user()->number_of_partner_leads() }}</strong>
                                                </span>
                            </li>
                            <li class="main__info-item">
                                                <span>
                                                    <strong>Начислено средств:</strong>
                                                </span>
                                <span class="z-index-3">
                                                    <strong>{{ Auth::user()->total_amount_from_referral() }} ₽</strong>
                                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>



        <div class="responsive-table" id="">
            <div class="divTable main__table tabletwo">
                <div class="divTableBody">
                    <div class="divTableRow divTable-head">
                        <div class="divTableCell" style="width: 230px;">Телефон</div>
                        <div class="divTableCell" style="width: 230px;">E-Mail</div>
                        <div class="divTableCell text-center" style="width: 175px;">Лиды</div>
                        <div class="divTableCell text-center" style="width: 265px;">Начислено</div>
                    </div>
                    @foreach( Auth::user()->partners() as $partner)
                        <div class="divTableRow t-row">
                            <div class="table-col-1">
                                <div class="divTableCell" style="width: 230px;">{{ $partner->setting->number }}</div>
                                <div class="divTableCell" style="width: 230px;">
                                    <span>{{ $partner->setting->email }}</span>
                                </div>
                            </div>
                            <div class="table-col-2">
                                <div class="divTableCell text-center" style="width: 175px;">
                                    <span>{{ $partner->numberOfLeads()  }}/ <span class="text-primary">{{ $partner->pending() }}</span> / <span class="text-success">{{ $partner->successful()  }}</span> / <span class="text-danger">{{ $partner->rejected()  }}</span></span>
                                </div>
                                <div class="divTableCell text-center" style="width: 265px;">
                                    {{ $data[$partner->id] }} ₽
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>




</div>
