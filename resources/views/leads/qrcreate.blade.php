
@extends('layouts.app')
@section('content')

    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col-md-9 ml-auto mr-auto">
                    <div class="main__content">
                        <h2 class="main__content-title">Заявка на продажу авто</h2>
                        <form method="POST" action="{{ route('car.store') }}" id ="formdata" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ isset($_GET['id']) ? $_GET['id'] : ''}}">
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
                                            <input name="phone" id="phone" type="text" class="form-control" placeholder="Не указан" required>
                                            @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
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

                                        <li><i class="fa fa-times timesicon d-none" id="1"  aria-hidden="true"></i> <img src="" id="img1" class="d-none"></li>
                                        <li><i class="fa fa-times timesicon d-none" id="2" aria-hidden="true"></i> <img src="" id="img2" class="d-none"></li>
                                        <li> <i class="fa fa-times timesicon d-none" id="3" aria-hidden="true"></i><img src="" id="img3" class="d-none"></li>
                                        <li> <i class="fa fa-times timesicon d-none"  id="4" aria-hidden="true"></i><img src="" id="img4" class="d-none"></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="text-center">
                                <button class="btn btn-red" type="submit">ОТПРАВИТЬ</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection



