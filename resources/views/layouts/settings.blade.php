
<div class="col-md-9">
<div class="main__content">
    <form  method="POST" id="settings" action="{{ route('settings.edit') }}" >
        @csrf
    <h2 class="main__content-title">Настройки аккаунта</h2>
    <div class="main__setting">
        <div class="main__setting-row">
            <div class="row">
                <div class="col-md-6">
                    <div class="main__setting-item">
                        <div>Телефон:
                            <input type="text" id="phone" name="number" class="form-control  @error('number') is-invalid @enderror" placeholder="Не указан" value="@if( ViewService::init()->view('number') !== null){{ ViewService::init()->view('number') }}@endif">
                            @error('number')

                            <span class="invalid-feedbackerror" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            {!! ViewService::init()->view('isPhoneConfirmed') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                        <div class="main__setting-item main__setting-item--edit">
                            <div>E-Mail:
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ ViewService::init()->view('email') }}" placeholder="Не указан">
                                @error('email')
                                <span class="invalid-feedbackerror" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                <br>
                                @enderror
                                {!! ViewService::init()->view('isEmailConfirmed') !!}
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
            <div class="row">
                <h3 class="main__setting-title w-100">Настройки оплаты</h3>
                <div class="col-md-6">
                    <div class="main__setting-item main__setting-item--edit">
                        <div>"ФИО или наименование Организации":
                            <input name = "fullName" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->fullName }}@endif">
                        </div>
                    </div>
                    <div class="main__setting-item main__setting-item--edit">
                        <div>Номер Паспорта:
                            <input name = "passportNumber" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->passportNumber }}@endif">
                        </div>
                    </div>
                    <div class="main__setting-item main__setting-item--edit">
                        <div>Банк:
                            <input name = "bankName" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->bankName }}@endif">
                        </div>
                    </div>
                    <div class="main__setting-item main__setting-item--edit">
                        <div>БИК:
                            <input name = "bik" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->bik }}@endif">
                        </div>
                    </div>
                    <div class="main__setting-item main__setting-item--edit">
                        <div>ИНН:
                            <input name = "inn" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->inn }}@endif">
                        </div>
                    </div>
                    <div class="main__setting-item main__setting-item--edit">
                        <div>р/с:
                            <input name = "rs" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->rs }}@endif">
                        </div>
                    </div>
                    <div class="main__setting-item main__setting-item--edit">
                        <div>к/с:
                            <input name = "ks" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->ks }}@endif">
                        </div>
                    </div>
                    <div class="main__setting-item main__setting-item--edit">
                        <div>Номер карты:
                            <input name = "cardNumber" id="bankcardnumber" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->cardNumber }}@endif">
                        </div>
                    </div>
                </div>
            </div>

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
<script>
    function submitEmail() {
        $( ".main__setting" ).append( $( "<input type='hidden'  name = 'confirmEmail'>" ) );
        document.getElementById("settings").submit();
    }
    function submitPhone() {
        $( ".main__setting" ).append( $( "<input type='hidden'  name = 'confirmPhone'>" ) );
        document.getElementById("settings").submit();
    }
</script>
