<?php


?>

<div class="main__content">
    <div class="d-flex justify-content-around">
        <h2 class="pl-5 main__content-title">Объявления</h2>
    </div>
    <form  method="POST" action="{{ route('admin.store.ads') }}" >
        @csrf
        <div class="d-flex justify-content-around">
            <textarea name="ads" id="" class="setting-textar w-50" >{{ \App\Models\Ad::first()->name }}</textarea>

        </div>



        <div class="text-center">
            <button class="btn btn-red">Сохранить</button>
        </div>
    </form>
</div>
