<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form  method="POST" action="{{ route('query_for_payment') }}" >
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Запросить выплату</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="sum">
                        <label>Сумма</label>
                        <input type="text" name="payment_amount" required>
                        <label>Вывести <a class="payment_amount">{{ Auth::user()->availableAmount() }}</a> руб</label>
                    </div>
                    <div class="bankcard">
                        <label> Куда вывести (номер банковской карты) </label>
                        <input type="text"  name = "bankcardnumber" id="bankcardnumber"  onfocus=" let value = this.value; this.value = null; this.value=value" required>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-red" type="submit" >Заказать</button>
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

