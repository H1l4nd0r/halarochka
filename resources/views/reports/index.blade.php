@extends('layouts.app')
@section('content')
<div class="container mt-3">
  
  <div class="card">
  
    <div class="card-body">
      <div class="list-group">

        <a href="/reports/cashflow" class="list-group-item list-group-item-action">Выдачи и поступления</a>
        <a href="/reports/nextpayments" class="list-group-item list-group-item-action">Ближайшие предстоящие платежи</a>
      </div>


    </div>
  </div>
</div>
@endsection