@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title d-flex justify-content-between">
      <form action="/deals">Статус договора: 
        @csrf
        @foreach([1 => 'Активные', 2 => 'Закрытые', 3 => 'Просроченые',
            4 => 'Реструктурированные'] as $value => $label)
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="radioDefault2" value="{{ $value }}" onclick="this.form.submit();" {{ request('status', 1) == $value ? 'checked' : '' }}>
              <label class="form-check-label" for="radioDefault2">
                {{ $label }}
              </label>
            </div>
        @endforeach
        

      </form>
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
          <td><a href="/deals/{{ $deal->id }}">{{ $deal->dealdate->format('d-m-Y') }}</a></td>
          <td><a href="/clients/{{ $deal->client->id }}">{{ $deal->client->last_name }} {{ $deal->client->first_name }} {{ $deal->client->middle_name }}</a></td>
          <td>{{ $deal->startprice }}</td>
          <td>{{ $deal->firstpayment }}</td>
          <td>{{ $deal->fee }}</td>
          <td>{{ $deal->status_text }}</td>
          <td>{{ $deal->goodname }}</td>
        </tr>
        @endforeach
        <tr>
          <th scope="row">Итого</th>
          <td></td>
          <td></td>
          <td>{{ $totals['tdisbursed'] }}</td>
          <td>{{ $totals['tfirstpayments'] }}</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
    </table>

  </div>

@endsection