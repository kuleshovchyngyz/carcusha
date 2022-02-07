<!-- Modal -->
<div class="modal fade" id="updateProjectModal" tabindex="-1" role="dialog" aria-labelledby="updateProjectModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateProjectModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Вы можете принять обновление, тогда произойдёт перезагрузка страницы и вы будете продолжать работать уже в новой версии приложения, либо вы можете отклонить обновление, тогда вам нужно будет обновить страницу вручную, когда это будет удобно.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

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
                  Вы можете принять обновление, тогда произойдёт перезагрузка страницы и вы будете продолжать работать уже в новой версии приложения, либо вы можете отклонить обновление, тогда вам нужно будет обновить страницу вручную, когда это будет удобно.

                </div>
                <div class="modal-footer-new p-40">
                    <button class="btn btn-blue" type="submit" >Заказать</button>
                </div>

            </form>
        </div>
    </div>
</div>
