<div class="modal fade" id="AddTelegramBot" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-violation">
            <form method="POST" action="{{ route('admin.bot.create') }}">
                @csrf
                <div class="modal-header-violation pb-20">
                    <label class="modal-title-violation" id="exampleModalLabel">Добавление бота</label>
                    <button type="button" class="close close-violation" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body-violation">

{{--                    <textarea type="text" class="form-control text-area-type2 " name="reason" placeholder="Причина нарушения"></textarea>--}}
                    <input type="text" class="input-modal input_type1" id="token" name="token" placeholder="Код компаний"  value="{{ ViewService::init()->view('telegramBot') }}" name="payment_amount" required>
                </div>

                <div class="modal-footer-violation p-40">
                    <button class="btn-blue btn-violation" type="submit" >Создать</button>
                </div>
            </form>
        </div>
    </div>
</div>



