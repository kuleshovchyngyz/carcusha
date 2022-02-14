<div class="modal fade" id="userQrCode" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content w-400 rounded-0">
            <div class="modal-header" style="border: 0px; padding-top: 10px; padding-left: 40px">
                <h5 class="modal-title-new" id="exampleModalLabel">Ваш QR-код</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex justify-content-center" style="margin-bottom: 60px">
                @if(@auth()->user())
                <img src="{{ asset( 'qrcodes/qrqr_'.auth()->user()->id.'.png') }}" style="width: 208px; height: 208px" alt="" class="logo">
                @endif
            </div>

        </div>
    </div>
</div>
