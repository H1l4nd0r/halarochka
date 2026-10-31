@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title">
      @can('create')
        <x-abutton href="/clients/create">Добавить клиента</x-abutton>
      @endcan
    </div>
    <table class="table">
  <thead>
    <tr>
      <th scope="col">№</th>
      <th scope="col">ФИО</th>
      <th scope="col">Дата рождения</th>
      <th scope="col">Телефон</th>
      <th scope="col">№ документа (паспорт/права)</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($clients as $client)
    <tr>
      <td scope="row">{{ $client['id'] }}</td>
      <td><a href="/clients/{{ $client->id }}">{{ $client['last_name'] }} {{ $client['first_name'] }} {{ $client['middle_name'] }}</a></td>
      <td>{{ $client['borndate'] }}</td>
      <td>{{ $client['phone'] }}</td>
      <td>{{ $client['idnum'] }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="d-flex justify-content-between align-items-center mt-3">
  <small class="text-muted">
    Всего: {{ $clients->total() }} · Страница {{ $clients->currentPage() }} из {{ $clients->lastPage() }}
  </small>
  <div>
    <select class="form-select form-select-sm d-inline-block w-auto"
            onchange="window.location.search='per_page='+this.value">
      <option value="15" @selected(request('per_page', '15') == '15')>15 на стр.</option>
      <option value="30" @selected(request('per_page') == '30')>30 на стр.</option>
      <option value="50" @selected(request('per_page') == '50')>50 на стр.</option>
      <option value="100" @selected(request('per_page') == '100')>100 на стр.</option>
    </select>
  </div>
</div>

<div class="mt-2">
  {{ $clients->links() }}
</div>

  </div>

@endsection