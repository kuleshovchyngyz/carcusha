<div class="col-md-9">
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
                    <div>Код приглашения:
                        <div class="invitation-code">{{ Auth::user()->invitation_code }}</div>
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
                    <th>Телефон</th>
                    <th>Лидов</th>
                    <th>В работе</th>
                    <th>Статус</th>
                    <th>Начислено</th>
                </tr>
                </thead>
                <tbody>


@foreach( Auth::user()->payments_by_refer() as $payment)

                <tr>
                    @if($payment->referred_user()!==null)
                    <td>User {{ $payment->referred_user()->id }}</td>
                    <td >{{ $payment->referred_user()->setting->number }}</td>
                    <td>{{ $payment->referred_user()->leads->count() }}</td>
                    <td>{{ $payment->referred_user()->pending()  }}</td>
                    <td>{{ $payment->referred_user()->status() }}</td>
                    <td>{{ $payment->amount }} ₽</td>
                    @else
                        @dump($payment)
                    @endif
                </tr>

@endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
