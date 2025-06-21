@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title">
       @can('admin')
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
      <td><a href="/deals/{{ $rep->deal->id }}">№ {{ $rep->deal->id }} от {{ $rep->deal->created_at }}</a></td>
      <td>{{ $rep->created_at }}</td>
      <td>{{ $rep->factday }}</td>
      <td>{{ $rep->summ }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

  </div>

@endsection