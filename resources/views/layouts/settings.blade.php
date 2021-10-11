<div class="col-md-9">
<div class="main__content">
    <form  method="POST" action="{{ route('settings.edit') }}" >
        @csrf
    <h2 class="main__content-title">Настройки аккаунта</h2>
    <div class="main__setting">
        <div class="main__setting-row">
            <div class="row">
                <div class="col-md-6">
                    <div class="main__setting-item">
                        <div>Телефон:
                            <input type="text" id="phone" name="number" class="form-control" placeholder="Не указан" value="{{ Auth::user()->setting->number }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="main__setting-item main__setting-item--edit">
                        <div>E-Mail:
                            <input type="email" name="email" class="form-control" value="{{ Auth::user()->setting->email }}" placeholder="Не указан">

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="main__setting-item main__setting-item--edit">
                        <div>Город:
                            <input type="text" class="form-control" name = "city" placeholder="Не указан" value="{{ Auth::user()->setting->city }}">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="main__setting-row">
            <div class="row">
                <h3 class="main__setting-title w-100">Настройки уведомлений</h3>
                <div class="col-md-2 col-6">
                    <div>
                        <input type="checkbox" name="number_notification" id="number_notification" @if( Auth::user()->setting->number_notification) checked @endif>
                        По телефону
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div>
                        <input type="checkbox" name="email_notification" id="email_notification" @if( Auth::user()->setting->email_notification) checked @endif>
                        По почте
                    </div>
                </div>
            </div>
        </div>
        <div class="main__setting-row">
{{--            <div class="row">--}}
{{--                <h3 class="main__setting-title w-100">Настройки оплаты</h3>--}}
{{--                <div class="col-md-6">--}}
{{--                    <div class="main__setting-item main__setting-item--edit">--}}
{{--                        <div>Номер карты:--}}
{{--                            <input name = "card_number"id="bankcardnumber" type="text" class="form-control" placeholder="Не указан" value="{{ Auth::user()->setting->card_number }}">--}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="text-danger">
                Номер карты необходим для вывода средств с баланса!
            </div>
        </div>
    </div>
        <div class="text-center">
            <button class="btn btn-red" type="submit">СОХРАНИТЬ</button>
        </div>

    </form>
</div>
</div>
