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
                            <th>ID</th>
                            <th>Автомобиль</th>
                            <th>Дата</th>
                            <th>Логин</th>
                            <th>Статус CRM</th>
                            <th>Статус</th>
                            <th>Operation</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach ($fantoms as $fantom)
                            <tr>
                                <td>{{ $fantom->id }}</td>
                                <td>{{ $fantom->vendor }} {{ $fantom->vendor_model }}, {{ $fantom->vendor_year }}</td>
                                <td>{{ $fantom->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.user', $fantom->user->id) }}" class="main__table-link">User
                                        #{{ $fantom->user->id  }}</a>
                                </td>
                                <td>{{ $fantom->status->name }}</td>
                                <td>{{ $statuses[$fantom->status->id]  }}</td>
                                <td>
                                    <select class="form-control w-50 d-inline" onchange="location = this.value;">
                                        <option selected="" value="" disabled>Опции</option>
                                        <option value="{{ route('admin.fantom.back.bitrix', $fantom->id) }}">Обратно в
                                            Битрикс
                                        </option>
                                        <option
                                            value="{{ route('admin.fantom.close', $fantom->id) }}">Скрыть
                                        </option>
                                        <option
                                            value="{{ route('admin.fantom.delete', $fantom->id ) }}">Удалить
                                        </option>


                                    </select>
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection

