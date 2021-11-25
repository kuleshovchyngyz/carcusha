@extends('layouts.app')

@section('content')
<main class="main">
    <div class="container">
        <div class="row">
            @include('layouts.sidebar',['active'=>$name])
            @include('layouts.'.$name,['data'=>$data])
        </div>
       
    </div>
</main>

@endsection
