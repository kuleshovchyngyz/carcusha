<?php
<input type="text" id="phone" name="number" class="form-control  @error('number') is-invalid @enderror" placeholder="Не указан" value="@if( ViewService::init()->view('number') !== null){{ ViewService::init()->view('number') }}@endif">
