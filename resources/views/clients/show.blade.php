@extends('layouts.app')
@section('content')

<div class="container mt-3">

<div class="row">
<div class="col-md-6">
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

    <div class="card">
      <div class="card-body">
        @foreach($client->files ?? [] as $index => $file)
          <div class="document">
              <a target="_blank" href="{{ Storage::url($file['path']) }}" download="{{ $file['name'] }}">
                  {{ $file['name'] }} ({{ round($file['size'] / 1024, 1) }} KB)<br>
                  <img src="{{ Storage::url($file['path']) }}" download="{{ $file['name'] }}" class="col-sm-4"/>
              </a>
              <form action="/clients/{{$client->id}}/delpic/{{ $index }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-primary">Удвлить</button>
              </form>
              
          </div>
      @endforeach
      </div>
    </div>
    
    @can('edit',$client)
    <x-abutton href="/clients/{{ $client->id }}/edit">Редактировать</x-abutton>  
    @endcan
    
  </div>
</div>
</div>
<div class="col-md-6">
  <div class="card">
  
  <div class="card-body">
    <div class="card-title d-flex justify-content-between">
      <h5>Договоры</h5>
    </div>

    <div class="card">
      <div class="card-body">
        @foreach ($client->deals as $deal)
        <ul>
          <li>№ {{ $deal->id }} от <a href="/deals/{{ $deal->id }}/edit">{{ $deal->created_at }}</a>  на сумму {{ $deal->startprice }}</li>
        </ul>
        @endforeach
      </div>
    </div>

    @can('create')
    <x-abutton href="/deals/create">Добавить</x-abutton>  
    @endcan
    
  </div>
</div>
</div>  
</div>



</div>
@endsection