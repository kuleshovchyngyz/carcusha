<?php
$updates = \App\Models\Updates::orderBy('created_at', 'DESC')->get();
?>
<div class="col-md-9">
    <div class="main__content">
        <h2 class="main__content-title">Обновления сервиса</h2>
        <ul class="help-list">
            @foreach($updates as $update)
                <li class="help-list__item">
                    <h3>{!! $update->version !!}</h3>
                    <p>
                        {!! shortCodeParse($update->changes) !!}
                    </p>
                </li>
            @endforeach
            {{--        <a href="{{route('public_offer')}}">sdfsdf</a>--}}

        </ul>
    </div>

</div>
