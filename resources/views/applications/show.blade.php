@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title d-flex justify-content-between">
      <h5></h5>
      <x-abutton href="/applications">Закрыть</x-abutton>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Покупатель, товар</h5>
            <p class="card-text">Клиент: {{ $deal->client->last_name }} {{ $deal->client->first_name }} {{ $deal->client->middle_name }}</p>
            <p class="card-text">Товар: {{ $deal->goodname }} </p>
            <p class="card-text">Договор: {{ $deal->id }} от {{ $deal->dealdate->format('d-m-Y') }} </p>
            <p class="card-text">Статус: {{ $deal->status_text }}</p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Условия</h5>
            <p class="card-text">Цена товара: {{ $deal->startprice }} (р.) </p>
            <p class="card-text">Первый взнос: {{ $deal->firstpayment }} (р.) </p>
            <p class="card-text">Наценка: {{ $deal->fee }} (р.)</p>
            <p class="card-text">Срок: {{ $deal->term }} мес </p>
            <p class="card-text">Полная стоимость рассрочки: {{ $deal->fullprice }} </p>
          </div>
        </div>
      </div>  
    </div>

    <div class="row">
      
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex justify-content-between">
              <h5 class="card-title">Документы</h5>
            </div>
            @foreach($deal->files as $index => $file)
              <div class="document">
                  <a target="_blank" href="{{ Storage::url($file['path']) }}" download="{{ $file['name'] }}">
                      {{ $file['name'] }} ({{ round($file['size'] / 1024, 1) }} KB)<br>
                      <img src="{{ Storage::url($file['path']) }}" download="{{ $file['name'] }}" class="col-sm-4"/>
                  </a>
                <form action="/applications/{{$deal->id}}/delpic/{{ $index }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-primary">Удвлить</button>
                </form>
              </div>
          @endforeach
          </div>
        </div>



      </div>  
    </div>

     @can('editApps',$deal)
    <x-abutton href="/applications/{{ $deal->id }}/edit">Редактировать</x-abutton>
    @endcan
  </div>

@endsection