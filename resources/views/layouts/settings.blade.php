
<div class="col-md-9">
<div class="main__content-v2">
    <form  method="POST" id="settings" action="{{ route('settings.edit') }}" >
        @csrf

        <div class="d-flex main__content-head">
            <h1 class="main__content-title">Настройки</h1>
            <button class='red-link ml-auto' type="button" id="submitSettings">Сохранить</button>

        </div>
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
                        <div class="main__setting-item ml-55">
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
                    <div class="main__setting-item">
                        <div>Город:
                            <input type="text" class="form-control" name = "city" placeholder="Не указан" value="{{ Auth::user()->setting->city }}">

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="main__setting-item ml-55">
                        <div>Промокод:
                            {!! ViewService::init()->view('InvitationCode') !!}
                        </div>
                    </div>
                </div>

{{--                <div class="col-md-6">--}}
{{--                    <div class="main__setting-item ">--}}
{{--                        <a href="{{route('settings.telegramNotification')}}" target="_blank" class='btn btn-primary telegram' style="background-color: #e3473f; border-color: #e3473f;">Телеграм уведомление</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
{{--        <div class="main__setting-row">--}}
{{--            <div class="row">--}}
{{--                <h3 class="main__setting-title w-100">Настройки уведомлений</h3>--}}
{{--                <div class="col-md-2 col-6">--}}
{{--                    <div>--}}
{{--                        <input type="checkbox" name="number_notification" id="number_notification" @if( Auth::user()->setting->number_notification) checked @endif>--}}
{{--                        По телефону--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-2 col-6">--}}
{{--                    <div>--}}
{{--                        <input type="checkbox" name="email_notification" id="email_notification" @if( Auth::user()->setting->email_notification) checked @endif>--}}
{{--                        По почте--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="main__setting-row">
            <h3>Настройки оплаты</h3>
            <p>Чтобы осуществить вывод средств с баланса, необходимо заполнить все поля ниже.</p>
            <div class="row">

                    <div class="col-md-6">
                        <div class="main__setting-item">
                            <div>"ФИО или наименование Организации":
                                <input name = "fullName" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->fullName }}@endif">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="main__setting-item ml-55">
                            <div>Номер Паспорта:
                                <input name = "passportNumber" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->passportNumber }}@endif">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="main__setting-item ">
                            <div>Банк:
                                <input name = "bankName" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->bankName }}@endif">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="main__setting-item ml-55">
                            <div>БИК:
                                <input name = "bik" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->bik }}@endif">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="main__setting-item ">
                            <div>ИНН:
                                <input name = "inn" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->inn }}@endif">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="main__setting-item ml-55">
                            <div>р/с:
                                <input name = "rs" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->rs }}@endif">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="main__setting-item ">
                            <div>к/с:
                                <input name = "ks" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->ks }}@endif">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="main__setting-item ml-55">
                                <div>Номер карты:
                                    <input name = "cardNumber" id="bankcardnumber" type="text" class="form-control" placeholder="Не указан" value="@if( Auth::user()->paymentSetting!=null ){{ Auth::user()->paymentSetting->cardNumber }}@endif">
                                </div>
                        </div>

                    </div>

            </div>

{{--            <div class="text-danger">--}}
{{--                Номер карты необходим для вывода средств с баланса!--}}
{{--            </div>--}}
        </div>
    </div>


    </form>
</div>
</div>
<script type="javascript">

    function submitSettings() {
        $( ".main__setting" ).append( $( "<input type='hidden'  name = 'submitSettings'>" ) );
        document.getElementById("settings").submit();
    }
    function submitEmail() {
        $( ".main__setting" ).append( $( "<input type='hidden'  name = 'confirmEmail'>" ) );
        document.getElementById("settings").submit();
    }
    function submitPromo() {
        $( ".main__setting" ).append( $( "<input type='hidden'  name = 'confirmPromo'>" ) );
        document.getElementById("settings").submit();
    }
    function submitPhone() {
     alert(234523);
    }
    export default {
        components: {App}
    }
</script>
