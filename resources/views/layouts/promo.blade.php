<div class="col-md-9">
<div class="main__content promo-block">
        <h2 class="main__content-title">Промоматериалы</h2>
        <p>Вы можете скачать и распечатать визитки и плакаты с Вашим QR-кодом.
            Распространите материалы, чтобы продавцы сами нашли нас. А вы получите деньги за их авто!
        </p>
    <form action="{{ route('user.promo') }}" method="post">
        @csrf
        <div class="row">
                <div class="col-md-6">
                    <div>Названия компании:
                        <input type="text" id="company" name="company" class="form-control" placeholder="Не указан" value="@if(Auth::user()->promo!==null){{ Auth::user()->promo->name }} @endif">
                    </div>
                </div>
                <div class="col-md-6">
                    <div>Телефон:
                        <input type="text" id="number" name="number" class="form-control" placeholder="Не указан" value="@if(Auth::user()->promo===null){{ Auth::user()->setting->number }}@else {{ Auth::user()->promo->phone }} @endif">
                    </div>
                </div>
                <div class="col-md-6">
                    <div>E-Mail:
                        <input type="text" id="email" name="email" class="form-control" placeholder="Не указан" value="@if(Auth::user()->promo===null){{ Auth::user()->setting->email }}@else {{ Auth::user()->promo->email }} @endif">
                    </div>
                </div>
                <div class="col-md-6">
                    <div>Адрес:
                        <input type="text" id="address" name="address" class="form-control" placeholder="Не указан" value="@if(Auth::user()->promo!==null){{ Auth::user()->promo->address }} @endif">
                    </div>
                </div>
            <div class="col-md-6">

            </div>
            <div class="col-md-6 pt-1 text-right">
                <button class="btn header__btn red-btn" type="submit">Показать</button>
            </div>
        </div>
    </form>


        <div class="row no-gutters">
            <div class="col-md-6">
                <h3>Визитки</h3>
                <img src="{{ asset('qrcodes/card1.jpg') }}" class="img-promo" alt="">
                <img src="{{ asset('qrcodes/card_qrsmall_'.\auth()->user()->id.'.png') }}" class="img-promo" alt="">
                <a href="{{route('download.business.card')}}" class="red-link promo-link">Скачать визитки в PDF</a>
            </div>
            <div class="col-md-6 d-flex">
                <div class="ml-auto mr-auto">
                    <h3>Плакаты</h3>
                    <img src="{{ asset('qrcodes/card_qr_'.\auth()->user()->id.'.png') }}" class="img-promo" alt="" style="
    max-width: 361px;">
                    <a href="{{route('download_card')}}" class="red-link promo-link">Скачать плакат в PDF</a>

                </div>
            </div>
        </div>
    </div>
</div>
