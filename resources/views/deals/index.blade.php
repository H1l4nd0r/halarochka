@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title">
       @can('admin')
      <x-abutton href="/deals/create">Добавить договор</x-abutton>
      @endcan
    </div>
    <table class="table">
  <thead>
    <tr>
      <th scope="col">№</th>
      <th scope="col">Дата договора</th>
      <th scope="col">Клиент</th>
      <th scope="col">Цена товара (р.)</th>
      <th scope="col">Взнос (р.)</th>
      <th scope="col">Наценка (р.)</th>
      <th scope="col">Статус</th>
      <th scope="col">Товар</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($deals as $deal)
    <tr>
      <th scope="row">{{ $deal->id }}</th>
      <td><a href="/deals/{{ $deal->id }}">{{ $deal->dealdate }}</a></td>
      <td><a href="/clients/{{ $deal->client->id }}">{{ $deal->client->last_name }} {{ $deal->client->first_name }} {{ $deal->client->middle_name }}</a></td>
      <td>{{ $deal->startprice }}</td>
      <td>{{ $deal->firstpayment }}</td>
      <td>{{ $deal->fee }}</td>
      <td>{{ $deal->status_text }}</td>
      <td>{{ $deal->goodname }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

  </div>

@endsection