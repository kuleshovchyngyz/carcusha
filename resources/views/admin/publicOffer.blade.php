

<div class="main__content">
    <form  method="POST" action="{{ route('admin.store.offers') }}" >
        @csrf
        <div class="text-center">
            <input name="title" class="mb-2 w-75"  value="{{ $data[0] }}">
            <textarea name="text" id="" class="setting-textarea-admin" >{{ $data[1] }}</textarea>
        </div>
        <div class="text-center">
            <button class="btn btn-blue">Сохранить</button>
        </div>
    </form>
</div>
