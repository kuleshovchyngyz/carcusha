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

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection

