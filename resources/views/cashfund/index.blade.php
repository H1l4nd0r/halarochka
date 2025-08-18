@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title">
      <span class="btn text-bg-warning p-2">Инвестиции {{ number_format($investments,0,'.',' ')  }}</span>
      @can('admin')
      <x-abutton href="/cash/create">Добавить инвестицию</x-abutton>
      @endcan
      <span class="btn text-bg-warning p-2">В кассе {{ number_format($available,0,'.',' ')  }}</span>
    </div>
    <table class="table">
  <thead>
    <tr>
      <th scope="col">№</th>
      <th scope="col">Сумма</th>
      <th scope="col">Дата операции</th>
      <th scope="col">Дата заведения</th>
      <th scope="col">Тип</th>
      <th scope="col">Описание</th>
      
      
    </tr>
  </thead>
  <tbody>
    @foreach ($funds as $rep)
    <tr>
      <th scope="row">{{ $rep->id }}</th>
      <td>{{ $rep->summ }}</td>
      <td>{{ $rep->factday }}</td>
      <td>{{ $rep->created_at }}</td>
      <td>{{ $rep->type_text }}</td>
      <td>{{ $rep->description }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

  </div>

@endsection