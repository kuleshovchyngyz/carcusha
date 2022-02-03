@if($errors->any())
    {{ implode('', $errors->all(':message')) }}
@endif

@extends('layouts.app')
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                @include('layouts.sidebar')

                <div class="col-md-9">
                    <div class="main__content">
                        <h2 class="main__content-title-blue">Добавление авто</h2>
                        <form method="POST" action="{{ route('lead.store') }}" id ="formdata" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="folder_id" id="folder_id" value="{{  uniqid() }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="main__setting-item">
                                        <div>Марка авто:
                                            <div class="form-group">
                                                <select name="car_vendor" class="form-control">
                                                    {{ print_r($buffer) }}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="main__setting-item">
                                        <div>Модель авто:
                                            <div class="form-group">
                                                <select name="car_model" class="form-control">
                                                    <option>Модель авто</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="main__setting-item">
                                        <div>Год выпуска:
                                            <select name="car_year" class="form-control">
                                                <option value="">Год выпуска авто</option>
                                                {{ print_r($years) }}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="main__setting-item">
                                        <div>Телефон продавца:
                                            <input name="phone" id="phone" type="text" class="form-control phone-error" placeholder="Не указан" required>
                                            <p class="error text-danger d-none"></p>
{{--                                            @error('phone')--}}
{{--                                            <span class="invalid-feedback" role="alert">--}}
{{--                                        <strong>{{ $message }}</strong>--}}
{{--                                    </span>--}}
{{--                                            @enderror--}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="main__setting-item upload-file w-100">
                                        Добавить фото
                                        <div class="form-control " id="fileuploader">
                                            Прикрепить фото <i class="fa fa-upload" aria-hidden="true"></i>
                                        </div>
                                            <input type="file" name="image[]" id="pictures" multiple="" hidden="" >

                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <ul class="filelist" id="filelist">
                                    <div class="onlyFour">
                                        <li><i class="fa fa-times timesicon d-none" id="1"  aria-hidden="true"></i> <img src="" id="img1" class="uploadImage d-none"></li>
                                        <li><i class="fa fa-times timesicon d-none" id="2" aria-hidden="true"></i> <img src="" id="img2" class="uploadImage d-none"></li>
                                        <li> <i class="fa fa-times timesicon d-none" id="3" aria-hidden="true"></i><img src="" id="img3" class="uploadImage d-none"></li>
                                        <li> <i class="fa fa-times timesicon d-none"  id="4" aria-hidden="true"></i><img src="" id="img4" class="uploadImage d-none"></li>
                                    </div>
                                    </ul>
                                </div>
                            </div>
                            <script>
                                function submitForm(btn) {
                                    let val = $("input[name=phone]").val()
                                    console.log(val.length)
                                    if(val.length==0){
                                        $('p.error').removeClass('d-none')
                                        $('p.error').empty()
                                        $('p.error').append('Пожалуйста, заполните это поле.');
                                    }else if(val.length>0 && val.length<16){
                                        $('p.error').removeClass('d-none')
                                        $('p.error').empty()
                                        $('p.error').append('Пожалуйста введите правильный номер телефона.');
                                    }else{
                                        $('p.error').empty()
                                        btn.disabled = true;
                                        $('#pictures').removeAttr( "name" );
                                        btn.form.submit();
                                    }

                                }
                            </script>
                            <div class="text-center">

                                <input id="submitButton" class="btn btn-blue" type="button" value="ОТПРАВИТЬ" onclick="submitForm(this);" />
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>
    @if(!$errors->any())
    <div class="modal fade show" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" style="display: block; padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px">
            <div class="modal-info">
                <div class="modal__title__blue">
                    Мы не выкупаем авто, если:
                </div>
                <ul class="modal__list">
                    <li>- Оно старше 15 лет (2006 года и ранее);</li>
                    <li>- Оно размещено на Avito и Auto.Ru;</li>
                    <li>- Продавец не заинтересован в продаже.</li>
                </ul>
                <button class="btn btn-blue agreed">СОГЛАСИТЬСЯ</button>
                <div class="text-center">
                    <a href="{{ url()->previous() }}" class="blue-link back">Вернуться назад</a>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection



