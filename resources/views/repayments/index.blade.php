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

<div class="d-flex justify-content-between align-items-center mt-3">
  <small class="text-muted">
    Всего: {{ $repayments->total() }} · Страница {{ $repayments->currentPage() }} из {{ $repayments->lastPage() }}
  </small>
  <div>
    <select class="form-select form-select-sm d-inline-block w-auto"
            onchange="window.location.search='per_page='+this.value">
      <option value="15" @selected(request('per_page', '15') == '15')>15 на стр.</option>
      <option value="30" @selected(request('per_page') == '30')>30 на стр.</option>
      <option value="50" @selected(request('per_page') == '50')>50 на стр.</option>
      <option value="100" @selected(request('per_page') == '100')>100 на стр.</option>
    </select>
  </div>
</div>

<div class="mt-2">
  {{ $repayments->links() }}
</div>

  </div>

@endsection