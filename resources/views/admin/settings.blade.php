<?php
$questions = $data['question'];
$payment = $data['payment'];
?>

<div class="main__content">
    <form  method="POST" action="{{ route('admin.store_settings') }}" >
        @csrf
        <h2 class="main__content-title">Справки</h2>
        @foreach($questions as $question )
                <div  class="new-setting-list">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="new-satings-input-wrap">
                                Вопрос:
                                <input type="text" name="questions[]" id="question1" value="{{ $question->question }}" class="form-control">
                                <input type="hidden" name="question_id[]" value="{{ $question->id }}">
                            </label>
                        </div>
                        <div class="col-md-8">
                            <div class="title-field">Ответ:</div>
                            <textarea name="answers[]" id="" class="setting-textar" >
                                {{ $question->answer }}
                            </textarea>
                        </div>
                    </div>
                </div>
        @endforeach
        <div id="question">

        </div>
        <div class="d-flex">
            <a class="btn header__btn color-red ml-auto add-new" id="addQuestion">  <i class="fa fa-plus" aria-hidden="true"></i>Добавить вопрос</a>
        </div>
        <div class="text-center">
            <button class="btn btn-red">Сохранить</button>
        </div>
    </form>
</div>
