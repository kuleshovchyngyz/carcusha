

<div class="modal fade" id="AddTelegramBot" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <form method="POST" action="{{ route('admin.bot.create') }}">
    @csrf
                  <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Добавление  бота</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="form-group">
                          <input type="text" class="form-control input_type1" id="token" name="token" placeholder="Код компаний" value="{{ ViewService::init()->view('telegramBot') }}">
                      </div>
                  </div>
                  <div class="modal-footer text-center">
                      <button type="submit" class="btn btn-red">Создать</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
