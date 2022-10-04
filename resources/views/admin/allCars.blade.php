@extends('admin.app')

@section('content')
    <main class="main">
        <div class="container">


            <div class="main__content">
                <h2 class="main__content-title">Фантомные лиды</h2>
                <input type="text" class="form-control search-tabel-mob" placeholder="Поиск по пользователям">
                <div class="table-responsive">
                    <table class="main__table">
                        <thead>
                        <tr>
                            <th>Автомобиль</th>
                            <th>Дата</th>
                            <th>Логин</th>
                            <th>Статус CRM</th>
                            <th>Статус</th>
                            <th>Bitrix ID</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach ($leads as $lead)
                            <tr>
                                <td>{{ $lead->vendor }} {{ $lead->vendor_model }}, {{ $lead->vendor_year }}</td>
                                <td>{{ $lead->created_at->format('Y-m-d h:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.user', $lead->user->id) }}" class="main__table-link">User
                                        #{{ $lead->user->id  }}</a>
                                </td>
                                <td>{{ $lead->status->name }}</td>
                                <td>{{ $statuses[$lead->status->id]  }}</td>
                                <td>{{ $lead->bitrix_lead_id }}</td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection

