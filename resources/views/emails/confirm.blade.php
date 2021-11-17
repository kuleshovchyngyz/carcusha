@component('mail::message')
# Здравствуйте,

{{ $message }}
{{ $not }}

@component('mail::panel')
    {{ $code }}
@endcomponent

Спасибо,<br>
{{ config('app.name') }}
@endcomponent


