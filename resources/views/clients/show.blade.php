@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title d-flex justify-content-between">
      <h5>{{ $client->last_name }} {{ $client->first_name }} {{ $client->middle_name }}</h5>
      <x-abutton href="/clients">Закрыть</x-abutton>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Дата рождения</h5>
        <p class="card-text">{{ $client->borndate }} </p>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Контакты</h5>
        <p class="card-text">{{ $client->email }} {{ $client->phone }} </p>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Документы</h5>
        <p class="card-text">{{ $client->iddoc }} {{ $client->idnum }} </p>
      </div>
    </div>
    
    @can('admin')
    <x-abutton href="/clients/{{ $client->id }}/edit">Редактировать</x-abutton>  
    @endcan
    
  </div>

@endsection