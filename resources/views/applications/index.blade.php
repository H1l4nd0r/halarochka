@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title d-flex justify-content-between">
      <span class="btn text-bg-warning p-2">В кассе {{ number_format($available,0,'.',' ') }}</span>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th scope="col">№</th>
          <th>Дата Заявки</th>
          <th>Клиент</th>
          <th class="text-end">Цена товара (р.)</th>
          <th class="text-end">Взнос (р.)</th>
          <th class="text-end">Наценка (р.)</th>
          <th class="text-end">Статус</th>
          <th>Товар</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($deals as $deal)
        <tr>
          <th scope="row">{{ $deal->id }}</th>
          <td><a href="/deals/{{ $deal->id }}">{{ $deal->dealdate->format('d-m-Y') }}</a></td>
          <td><a href="/clients/{{ $deal->client->id }}">{{ $deal->client->last_name }} {{ $deal->client->first_name }} {{ $deal->client->middle_name }}</a></td>
          <td class="text-end">{{ number_format($deal->startprice,0,'.',' ') }}</td>
          <td class="text-end">{{ number_format($deal->firstpayment,0,'.',' ')  }}</td>
          <td class="text-end">{{ number_format($deal->fee,0,'.',' ')  }}</td>
          <td class="text-end">{{ $deal->status_text }}</td>
          <td>{{ $deal->goodname }}</td>
        </tr>
        @endforeach

      </tbody>
    </table>

  </div>

@endsection