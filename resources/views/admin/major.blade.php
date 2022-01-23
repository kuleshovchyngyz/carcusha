@extends('admin.app')

@section('content')
<div class="main__content">
    <div class="d-flex justify-content-around">
        <h2 class="pl-5 main__content-title">Род деятельности</h2>
    </div>
    <form  method="POST" action="{{ route('admin.store.majors') }}" >
        @csrf
        <div class="d-flex justify-content-around">
            <textarea name="majors" id="" class="setting-textar w-50" >{{ $majors }}</textarea>

        </div>



        <div class="text-center">
            <button class="btn btn-blue">Сохранить</button>
        </div>
    </form>
</div>
@endsection

