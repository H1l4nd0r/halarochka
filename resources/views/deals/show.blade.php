@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title d-flex justify-content-between">
      <h5></h5>
      <x-abutton href="/deals">Закрыть</x-abutton>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Покупатель, товар</h5>
            <p class="card-text">Клиент: {{ $deal->client->last_name }} {{ $deal->client->first_name }} {{ $deal->client->middle_name }}</p>
            <p class="card-text">Товар: {{ $deal->goodname }} </p>
            <p class="card-text">Договор: {{ $deal->id }} от {{ $deal->created_at }} </p>
            <p class="card-text">Статус: {{ $deal->status }}</p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Условия</h5>
            <p class="card-text">Цена товара: {{ $deal->startprice }} </p>
            <p class="card-text">Первый взнос: {{ $deal->firstpayment }} </p>
            <p class="card-text">Срок: {{ $deal->term }} мес </p>
          </div>
        </div>
      </div>  
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">График платежей</h5>
            <div class="row">
              <div class="col-sm-3">Дата</div>
              <div class="col-sm-3">Сумма платежа</div>
              <div class="col-sm-3">Осталось</div>
              <div class="col-sm-3">статус</div>
            </div>
            @foreach ($deal->schedule as $payday)
            <div class="row">
              <div class="col-sm-3">{{ $payday->payday }}</div>
              <div class="col-sm-3">{{ $payday->fullsumm }}</div>
              <div class="col-sm-3">{{ $payday->leftsumm }}</div>
              <div class="col-sm-3">{{ $payday->status }}</div>
            </div>
              
            @endforeach
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            
            <div class="card-title d-flex justify-content-between">
              <h5 class="card-title">Платежи</h5>
              @can('admin')
              <x-abutton href="/repayments/create?deal_id={{ $deal->id }}">Добавить</x-abutton>
              @endcan
            </div>
            <div class="row">
              <div class="col-sm-3">Дата</div>
              <div class="col-sm-3">Сумма платежа</div>
            </div>
            @foreach ($deal->repayments as $payment)
            <div class="row">
              <div class="col-sm-3">{{ $payment->factday }} </div>
              <div class="col-sm-3">{{ $payment->summ }}</div>
            </div>
            @endforeach
          </div>
        </div>
      </div>  
    </div>

     @can('admin')
    <x-abutton href="/deals/{{ $deal->id }}/edit">Редактировать</x-abutton>
    @endcan
  </div>

@endsection