@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title">
      <a href="/cash" class="btn btn-outline-primary">Закрыть</a>
    </div>
    <table class="table">
  <thead>
    <tr>
      <th scope="col">№</th>
      <th scope="col">Сумма</th>
      <th scope="col">Дата платежа</th>
      <th scope="col">Договор</th>
      <th scope="col">Клиент</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>Mark</td>
      <td>Otto</td>
      <td>@mdo</td>
      <td>@mdo</td>
    </tr>
    
  </tbody>
</table>

  </div>

@endsection