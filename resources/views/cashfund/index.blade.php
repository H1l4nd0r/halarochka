@extends('layouts.app')
@section('content')

<div class="container mt-3">
  
<div class="card">
  
  <div class="card-body">
    <div class="card-title d-flex justify-content-between">
      <form action="/cash">Тип: 
        @foreach($types as $value => $label)
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="type" id="radioDefault{{ $value }}" value="{{ $value }}" onclick="this.form.submit();" {{ request('type', '0')  === (string)$value ? 'checked' : '' }}>
              <label class="form-check-label" for="radioDefault{{ $value }}">
                {{ $label }}
              </label>
            </div>
        @endforeach
        

      </form>


      <span class="btn text-bg-warning p-2">Инвестиции {{ number_format($investments,0,'.',' ')  }}</span>
      @can('create')
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
      <td>{{ $rep->factday->format('d-m-Y') }}</td>
      <td>{{ $rep->created_at->format('d-m-Y') }}</td>
      <td>{{ $rep->type_text }}</td>
      <td>{{ $rep->description }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="d-flex justify-content-between align-items-center mt-3">
  <small class="text-muted">
    Всего: {{ $funds->total() }} · Страница {{ $funds->currentPage() }} из {{ $funds->lastPage() }}
  </small>
  <div>
    <select class="form-select form-select-sm d-inline-block w-auto"
            onchange="document.cookie='per_page='+this.value+'; path=/; max-age=31536000; SameSite=Lax'; var s=window.location.search; s = /[?&]per_page=/.test(s) ? s.replace(/([?&]per_page=)[^&]*/, '$1'+this.value) : s + (s?'&':'?') + 'per_page=' + this.value; window.location.search=s;">
      <option value="15" @selected($perPage == 15)>15 на стр.</option>
      <option value="30" @selected($perPage == 30)>30 на стр.</option>
      <option value="50" @selected($perPage == 50)>50 на стр.</option>
      <option value="100" @selected($perPage == 100)>100 на стр.</option>
    </select>
  </div>
</div>

<div class="mt-2">
  {{ $funds->links() }}
</div>

  </div>

@endsection