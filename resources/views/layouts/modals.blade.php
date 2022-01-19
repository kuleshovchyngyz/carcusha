<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-new">
            <form  method="POST" action="{{ route('query_for_payment') }}" >
                @csrf

                <div class="d-flex main__content-head modal-head-new">
                    <span class="modal-title-new" id="exampleModalLabel">Запросить выплату</span>
                    <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-40">
                    <ul class="main__info-list">
                        <li class="main__info-item">
                            <span class="z-index-3">
                                <strong>Балланс:</strong>
                            </span>
                            <span>
                                <strong><a class="payment_amount">{{ Auth::user()->availableAmount() }}</a></strong>
                                <i class="fa fa-rub" aria-hidden="true"></i>
                          </span>
                        </li>
                    </ul>
                    <div class="sum">
                        <label class="label-modal">Сумма для вывода:</label>
                        <input type="text" class="input-modal" name="payment_amount" required>

                    </div>
{{--                    <div class="bankcard">--}}
{{--                        <label> Куда вывести (номер банковской карты) </label>--}}
{{--                        <input type="text"  name = "bankcardnumber" id="bankcardnumber"  onfocus=" let value = this.value; this.value = null; this.value=value" required>--}}

{{--                    </div>--}}
                </div>
                <div class="modal-footer-new p-40">
                    <button class="btn btn-blue" type="submit" >Заказать</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php

  if(auth()->user()->notifier==null){
      $bought = false;
      $on_work = false;
  }else{
      $bought = auth()->user()->notifier->bought;
      $on_work = auth()->user()->notifier->on_work;
  }
?>
<div class="modal fade" id="notification_setting" tabindex="-1" role="dialog" aria-labelledby="notification_setting" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form  method="POST" action="{{ route('notification_setting') }}" >
                @csrf
                <div class="modal-header">
                    <h5 class="pl-6 modal-title" id="exampleModalLabel">Настройки уведомлений</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="pl-5">
                        <span>Выберите статусы, уведомления о которых вы будете получать</span>
                    </div>
                    <!-- Default checkbox -->
                    <div class="w-75 pl-3 d-flex justify-content-between">
                        <label  >
                            Взяли в работу
                        </label>
                        <input
                            type="checkbox"
                            name="on_work"
                            @if($on_work) checked @endif
                        />
                    </div>

                    <!-- Checked checkbox -->
                    <div class="w-75 pl-3 d-flex justify-content-between">
                        <label >
                            Выкупили
                        </label>
                        <input
                            class="sd"
                            type="checkbox"
                            name="bought"
                        @if($bought) checked @endif
                        />
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button class="btn btn-light" type="submit" >Сохранить</button>
                </div>

            </form>
        </div>
    </div>
</div><?php

