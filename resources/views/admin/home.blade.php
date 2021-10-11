@extends('admin.app')

@section('content')
    <main class="main">
        <div class="container">
            @if(isset($new))
                @include('admin.'.$name,['data'=>$data,'new'=>$new])
            @else
                @include('admin.'.$name,['data'=>$data])
            @endif
        </div>
    </main>
@endsection

