@extends('layouts.app')

@section('content')
    <main class="main">
        <div class="container main__content">
            <div class="d-flex main__content-head">
                <h1 class="main__content-title">{!! ViewService::init()->type(['title'])->view('publicOffer') !!}</h1>
                <a href="{{ url()->previous() }}" class="red-link ml-auto">Вернуться назад</a>
            </div>
            {!! ViewService::init()->type(['text'])->view('publicOffer') !!}
        </div>
    </main>

@endsection

