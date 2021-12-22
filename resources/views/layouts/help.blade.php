<?php
$questions = \App\Models\Question::all();
?>
<div class="col-md-9">
    <div class="main__content">
        <h2 class="main__content-title">Помощь</h2>
        <ul class="help-list">
            @foreach($questions as $question)
                <li class="help-list__item">
                    <h3>{!! $question->question !!}</h3>
                    <p>
                        {!! shortCodeParse($question->answer) !!}
                    </p>
                </li>
            @endforeach
{{--        <a href="{{route('public_offer')}}">sdfsdf</a>--}}

        </ul>
    </div>

</div>
