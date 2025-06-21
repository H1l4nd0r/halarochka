@extends('layouts.app')
@section('content')
<div class="card">
  <div class="card-body">
    <div class="row">
      
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Клиентов</h5>
            {{ $clientsnum }}
          </div>
        </div>
      </div> 
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Договоров</h5>
            {{ $docsnum }}
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Платежей</h5>
            {{ $repsnum }}
          </div>
        </div>
      </div> 
    </div>
  </div>  
</div>
@endsection