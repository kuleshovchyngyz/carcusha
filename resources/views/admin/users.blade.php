<div class="main__content">
    <h2 class="main__content-title">Пользователи</h2>
    <input type="text" class="form-control search-tabel-mob" placeholder="Поиск по пользователям">
    <div class="table-responsive">
        <table class="main__table">
            <thead>
            <tr>
                <th>Логин</th>
                <th>Телефон</th>
                <th>Регистрация</th>
                <th>Лидов</th>
                <th>В работе</th>
                <th>Отклонено</th>
                <th>Оплачено</th>
                <th>Нарушения</th>
                <th>Статус</th>
                <th>Рефералы</th>
                <th>Баланс</th>
                <th><a href="{{ route('admin.users') }}@if($data['sort']!='')?sort={{ $data['sort'] }}@endif">Объяв</a></th>
            </tr>
            </thead>
            <tbody>
            @foreach($data['users']->sortByDesc('created_at') as $user)
                @if(!$user->hasRole('admin'))
                <tr>
                    <td><a href="{{ route('admin.user', $user->id) }}" class="main__table-link">User #{{ $user->id  }}<span class="mob-stsus text-success"></span></a></td>
                    @if(!$user->setting)
                        @dd($user)
                    @endif
                    <td>{{ $user->setting->number  }}</td>
                    <td>{{ $user->created_at->format('d.m.y')  }}</td>
                    <td>{{ $user->leads->count()  }}</td>
                    <td>{{ $user->pending()  }}</td>
                    <td>{{ $user->rejected()  }}</td>
                    <td>{{ $user->paid()  }}</td>
                    <td>{{ $user->number_of_violations()  }}</td>
                    @if($user->active==1)
                        <td class="text-success">{{ $user->status()  }}</td>
                    @else
                        <td class="text-danger">{{ $user->status()  }}</td>
                    @endif
                    <td>{{ $user->partners()->count() }}</td>
                    <td>{{ $user->balance->balance }} ₽</td>
                    <td>{{ $user->new_leads_quantity() }} </td>
                </tr>
                @endif
            @endforeach

            </tbody>
        </table>
    </div>
</div>
