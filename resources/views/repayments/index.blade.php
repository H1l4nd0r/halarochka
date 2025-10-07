@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title">
       @can('create')
      <x-abutton href="/repayments/create">Добавить оплату</x-abutton>
      @endcan
    </div>
    <table class="table">
  <thead>
    <tr>
      <th scope="col">№</th>
      <th scope="col">Договор</th>
      <th scope="col">Дата создания</th>
      <th scope="col">Дата платежа</th>
      <th scope="col">Сумма</th>
      
    </tr>
  </thead>
  <tbody>
    @foreach ($repayments as $rep)
    <tr>
      <th scope="row">{{ $rep->id }}</th>
      <td><a href="/deals/{{ $rep->deal->id }}">№ {{ $rep->deal->id }} от {{ $rep->deal->dealdate->format('d-m-Y') }}</a></td>
      <td>{{ $rep->created_at->format('d-m-Y') }}</td>
      <td>{{ $rep->factday->format('d-m-Y') }}</td>
      <td>{{ $rep->summ }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

  </div>

@endsection