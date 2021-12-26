@extends('layouts.app')

@section('content')
    <main class="main">
        <div class="container main__content">
            <div class="d-flex justify-content-between">
                <div class="publicOffer"><h1>{!! ViewService::init()->type(['title'])->view('publicOffer') !!}</h1></div>
                <a href="{{ url()->previous() }}" class="red-link">Вернуться назад</a>
            </div>
            <div class="publicOffer">{!! ViewService::init()->type(['text'])->view('publicOffer') !!}</div>
        </div>
    </main>

@endsection

