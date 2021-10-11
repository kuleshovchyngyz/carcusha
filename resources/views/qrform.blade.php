@extends('layouts.app')

@section('content')
    <main class="main">
        <div class="container">
            <div class="row">


                @include('leads.'.$name,['data'=>$data])
            </div>
        </div>
    </main>

@endsection
