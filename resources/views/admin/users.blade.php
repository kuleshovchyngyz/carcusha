<div class="main__content">
    <h2 class="main__content-title">Пользователи</h2>
    <input type="text" class="form-control search-tabel-mob" placeholder="Поиск по пользователям">
    <div class="table-responsive">
        <table class="main__table">
            <thead>
            <tr>
                <th>Логин</th>
                <th>Телефон</th>
                <th>E-Mail</th>
                <th>Регистрация</th>
                <th>Лидов</th>
                <th>Наруш.</th>
{{--                <th><a href="{{ route('admin.users') }}@if($data['sort']!='')?sort={{ $data['sort'] }}@endif">Объяв</a></th>--}}
                <th>Объяв</th>
                <th>Реф.</th>
                <th>Начисления</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data['users']->sortByDesc('created_at') as $user)
                <tr>

                    <td>
                      @if($user->active==1)
                           <span class="status-dot text-success"></span>
                      @else
                            <span class="status-dot text-danger"></span>
                      @endif
                        <a href="{{ route('admin.user', $user->id) }}" class="main__table-link">User #{{ $user->id  }}<span class="mob-stsus text-success"></span></a></td>
                    @if(!$user->setting)
{{--                        @dd($user)--}}
                    @else

                    <td>{{ $user->setting->number  }}</td>
                    <td>{{ $user->setting->email  }}</td>
                    <td>{{ $user->created_at->format('d.m.y')  }}</td>
                    <td><span>{{ $user->leads->count()  }} /
                            <span class="text-primary">{{ $user->pending  }}</span> /
                            <span class="text-success">{{ $user->successful  }}</span> /
                            <span class="text-danger">{{ $user->rejected  }}</span></span></td>
                    <td>{{ $user->number_of_violations()  }}</td>
                    <td>{{ $user->numberOfNewLeads }} </td>
                    <td>{{ $user->refers->count() }}</td>
                    <td>{{ $user->balance->balance }} ₽ / <span class="text-success">@if($user->paid){{ $user->paid  }} ₽@else 0 ₽@endif</span></td>
                    {{--                    <td>{{ $user->paid  }}</td>--}}

{{--                    <td>{{ $user->balance->balance }} ₽</td>--}}
                    @endif
                </tr>

            @endforeach

            </tbody>
        </table>
    </div>
</div>
