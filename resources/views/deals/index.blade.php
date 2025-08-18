@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title d-flex justify-content-between">
      <form action="/deals">Статус договора: 
        @foreach([1 => 'Активные', 2 => 'Закрытые', 3 => 'Просроченые',
            4 => 'Реструктурированные'] as $value => $label)
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="radioDefault{{ $value }}" value="{{ $value }}" onclick="this.form.submit();" {{ request('status', 1) == $value ? 'checked' : '' }}>
              <label class="form-check-label" for="radioDefault{{ $value }}">
                {{ $label }}
              </label>
            </div>
        @endforeach
        

      </form>
      <span class="btn text-bg-warning p-2">В кассе {{ number_format($available,0,'.',' ') }}</span>
      @can('admin')
        <x-abutton href="/deals/create">Добавить договор</x-abutton>
      @endcan
    </div>
    <table class="table">
      <thead>
        <tr>
          <th scope="col">№</th>
          <th>Дата договора</th>
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
        <tr>
          <th scope="row">Итого</th>
          <td></td>
          <td></td>
          <td class="text-end">{{ number_format($totals['tdisbursed'],0,'.',' ')  }}</td>
          <td class="text-end">{{ number_format($totals['tfirstpayments'],0,'.',' ')  }}</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
    </table>

  </div>

@endsection