<!-- Modal -->
<div class="modal fade" id="resetPassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"  >
            @csrf
            <div class="modal-header-violation pb-20">
                <label class="modal-title-violation" id="exampleModalLabel">Сбросить пароль</label>
                <button type="button" class="close close-violation" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="alert alert-danger" style="display:none"></div>
            <div class="modal-body ml-4 mr-4">
                <div class="type-pass mb-4">

                    <input id="password" type="text" placeholder="Придумайте пароль" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" name="password" required autocomplete="new-password">

                    @error('password')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                            </span>
                    @enderror


                </div>
                <div class="type-pas">
                    <input id="password-confirm" type="text" class="form-control"  placeholder="Подтвердите пароль" name="password_confirmation" required autocomplete="new-password">

                </div>
            </div>

            <div class="d-flex justify-content-center mb-4">

                <button  class="btn btn-blue reset-password">Поменять</button>
            </div>
        </div>
    </div>
</div>
