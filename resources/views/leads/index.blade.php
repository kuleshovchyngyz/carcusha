@extends('layouts.app')
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                @include('layouts.sidebar')
                @include('layouts.create')
            </div>
        </div>
    </main>
@endsection

