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

  </div>

@endsection