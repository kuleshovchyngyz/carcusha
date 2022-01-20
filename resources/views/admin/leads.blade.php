<?php
    $sort = '';
    if(isset($_GET['sort'])){
        $sort = $_GET['sort'];
    }

    if($sort == ''){
        $leads = $user->leads;
        $sort = 'asc';
    }else if($sort == 'asc'){
        $leads = $user->leads->sortBy('bitrix_lead_id');
        $sort = 'desc';
    }else if($sort == 'desc'){
        $leads = $user->leads->sortByDesc('bitrix_lead_id');
        $sort = '';
    }

?>
<div class="col-md-9">
    <div class="main__content">
        <div class="d-flex justify-content-between">
            <h2 class="main__content-title">Авто</h2>
{{--            @dd( route('admin.user.new'))--}}
            <a href="{{route('admin.user.new',$user->id)}}">
                <span class="main__content-title new_leads">Объявлений ({{ $user->new_leads_quantity() }})</span>
            </a>
        </div>

        <div class="table-responsive">
            <table class="main__table">
                <thead>
                <tr>
                    <th><a class="no-decor-link" href="{{ route('admin.user',$user->id) }}@if($sort!='')?sort={{ $sort }}@endif">Номер</a></th>
                    <th>Автомобиль</th>
                    <th>Телефон</th>
                    <th>Статус</th>
                    <th>Оплата</th>
                </tr>
                </thead>
                <tbody>
                @foreach($leads as $lead)
                    @if($lead!=false)
                        <tr>
                            <td>#{{ $lead->bitrix_lead_id }}  @if($lead->checked())  <img src="{{ asset('img/unchecked.png') }}" alt=""> @endif</td>
                            <td>{{ $lead->vendor }} {{ $lead->vendor_model }}, {{ $lead->vendor_year }}</td>
                            <td>{{ $lead->phonenumber }}</td>
                            @if($lead->status==false)

                                <td>NULL</td>
                            @else

                                {{--                                @dump($lead->history())--}}
                                {{--                                @dump($lead)--}}
                                <td class="main__table-dd-wrap" style="color:{{ $lead->status->color }};">{{ $lead->status->name }}

                                    <ul class="main__table-dd">
                                        <li>

                                            <span >{{ $lead->history()==false ? "" : $lead->history()->status()->name }}</span>
                                            <span>{{ $lead->history()==false ? "" :  $lead->history()->updated_at->format('d.m.y') }}</span>
                                        </li>
                                        <li>
                                            <span class="text-warning"> {{ $lead->status->name }}</span>
                                            <span>{{ $lead->updated_at->format('d.m.y') }}</span>
                                        </li>
                                    </ul>
                                </td>

                            @endif
                            {{--                            <td>777777777 ₽</td>--}}
{{--                            <td>{{ $lead->payment_by_status() }} ₽</td>--}}
                            <td>{{ ViewService::init($lead)->view('total_payments_by_lead') }} ₽</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
<br>
        </div>
    </div>
</div>
