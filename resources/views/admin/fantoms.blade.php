@extends('admin.app')

@section('content')
    <main class="main">
        <div class="container">


            <div class="main__content">
                <h2 class="main__content-title">Выплаты</h2>
                <input type="text" class="form-control search-tabel-mob" placeholder="Поиск по пользователям">
                <div class="table-responsive">
                    <table class="main__table">
                        <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Логин</th>
                            <th>Статус CRM </th>
                            <th>Статус</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($fantoms as $fantom)
                            <td>{{ $fantom->created_at->format('Y-m-d') }}</td>
                            <td>{{ $fantom->id }}</td>
                            <td></td>
                            <td></td>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection

