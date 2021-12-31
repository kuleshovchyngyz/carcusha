<?php
$updates = $data['updates'];
?>

<div class="main__content">
    <form  method="POST" action="{{ route('admin.store_updates') }}" >
        @csrf
        <h2 class="main__content-title">Справки</h2>
        @foreach($updates as $update )
            <div  class="new-setting-list">
                <div class="row">
                    <div class="col-md-4">
                        <label class="new-satings-input-wrap">
                            Версия:
                            <input type="text" name="versions[]" id="question1" value="{{ $update->version }}" class="form-control">
                            <input type="hidden" name="version_id[]" value="{{ $update->id }}">
                        </label>
                    </div>
                    <div class="col-md-8">
                        <div class="title-field">Ответ:</div>
                        <textarea name="changes[]" id="" class="setting-textar" >
                                {{ $update->changes }}
                            </textarea>
                    </div>
                </div>
            </div>
        @endforeach
        <div id="version">

        </div>
        <div class="d-flex">
            <a class="btn header__btn color-red ml-auto add-new" id="addVersion">  <i class="fa fa-plus" aria-hidden="true"></i>Добавить</a>
        </div>
        <div class="text-center">
            <button class="btn btn-red">Сохранить</button>
        </div>
    </form>
</div>
