<div class="col-md-9">

    <div class="main__content-v2">
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
                                                                    Промокод
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
                                    <span>{{ $partner->numberOfLeads()  }} / <span class="text-primary">{{ $partner->pending() }}</span> / <span class="text-success">{{ $partner->successful()  }}</span> / <span class="text-danger">{{ $partner->rejected()  }}</span></span>
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


    <br>
    <br>
    <br>
    <br>



<div class="main__content">
    <h2 class="main__content-title">Реферальная программа</h2>
    <div class="main__setting-row">
        <div class="row">
            <div class="col-md-6">
                <div class="main__setting-item main__setting-item--edit">
                    <div>Реферальная ссылка:
                        <input type="text" id="reference" class="form-control hidden" readonly="" placeholder="Не указан" value="{{ route('login') }}?ref={{ Auth::user()->invitation_code }}">
                        <button class="btn btn--copy"></button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="main__setting-item">
                    <div>Промокод: <i class="fa fa-question-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Партнеры, которые введут этот промокод, будут считаться привлеченными вами"></i>
                        <div class="invitation-code-number">{{ Auth::user()->invitation_code }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main__setting-row">
        <div class="bar-info d-flex justify-content-between">
            <div class="bar-info__item">
                Всего партнёров: <span>{{ Auth::user()->partners()->count() }}</span>
            </div>
            <div class="bar-info__item">
                Партнёрских лидов: <span>{{ Auth::user()->number_of_partner_leads() }}</span>
            </div>
            <div class="bar-info__item">
                Начислено средств: <span>{{ Auth::user()->total_amount_from_referral() }} ₽</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="main__table">
                <thead>
                <tr>
                    <th>Логин</th>
                    <th>Дата</th>
                    <th>Телефон</th>
                    <th>Лидов</th>
                    <th>В работе</th>
                    <th>Начислено</th>
                </tr>
                </thead>
                <tbody>
@foreach( Auth::user()->partners() as $partner)
                <tr>
                    <td>User {{ $partner->id }}</td>
                    <td >{{ $partner->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $partner->setting->number }}</td>
                    <td>{{ $partner->numberOfLeads()  }}</td>
                    <td>{{ $partner->pending() }}</td>
                    <td>{{ $data[$partner->id] }} ₽</td>
                </tr>

@endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
