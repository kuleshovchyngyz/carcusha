
<div class="col-md-9">
    <div class="main__content">
        <div class="d-flex justify-content-between">
            <h2 class="main__content-title">Авто</h2>
            <a href="{{ route('admin.user',$user->id) }}">
                <span class="main__content-title new_leads">Все лиды </span>
            </a>
        </div>
        <div class="table-responsive">
            <table class="main__table">
                <thead>
                <tr>
                    <th>Номер</th>
                    <th>Автомобиль</th>
                    <th>Телефон</th>
                    <th>Объявления</th>
                </tr>
                </thead>
                <tbody>
                @foreach($user->new_leads() as $lead)
                    @if($lead!=false)
                        <tr>
                            <td>#{{ $lead->bitrix_user_id }}  @if($lead->checked()!==false)  {!! file_get_contents(asset('img/unchecked.svg')) !!} @endif</td>
                            <td>{{ $lead->vendor }} {{ $lead->vendor_model }}, {{ $lead->vendor_year }}</td>
                            <td>{{ $lead->phonenumber }}</td>

                            <td>{{ $lead->checked_texts }}</td>

                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
            <br>
            <div class="text-center">

                <a href="{{route('admin.user.verify_leads',$user->id)}}" type="button" class="btn btn-red" >
                    ВСЁ В ПОРЯДКЕ
                </a>
            </div>
        </div>
    </div>
</div>
