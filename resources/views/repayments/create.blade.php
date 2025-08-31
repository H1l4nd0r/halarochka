@extends('layouts.app')
@section('content')

<div class="container mt-3">

    <div class="card">

        <div class="card-body">
            <div class="card-title d-flex justify-content-between">
                @if( $deal)
                    <label for="startprice" class="form-label">Договор № {{ $deal->id}} от {{ $deal->created_at}} клиент {{ $deal->client->last_name}} {{ $deal->client->first_name}}</label>
                    <x-abutton href="/deals/{{ $deal->id}}">Отмена</x-abutton>
                @else
                    <x-abutton href="/repayments">Отмена</x-abutton>
                @endif
                
            </div>
            <form method="post" action="/repayments">
@csrf
                @if( $deal)
                    <input type="hidden" name="deal_id" value="{{ $deal->id }}"/>
                @else
                    <div class="mb-3">
                        <label for="startprice" class="form-label">Договор</label>
                        <select class="form-select" aria-label="Default select example" name="deal_id">
                            <option selected>...</option>
                            @foreach ($deals as $deal)
                                <option value="{{ $deal->id }}">{{ $deal->id }}&nbsp;{{ $deal->created_at }}&nbsp;{{ $deal->client->first_name }}&nbsp;{{ $deal->client->first_name }}&nbsp;{{ $deal->client->phone }}&nbsp;{{ $deal->goodname }}&nbsp;{{ $deal->startprice }}</option> 
                            @endforeach
                        </select>
                        @error('client_id')
                            <div class="text-danger">{{ $message }}</div>    
                        @enderror
                    </div>
                @endif

                <div class="mb-3">
                    <label for="factday" class="form-label">Дата платежа</label>
                    <input type="date" class="form-control" id="factday" name="factday" required value="{{ old('factday') }}">
                    @error('factday')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="summ" class="form-label">Сумма</label>
                    <input type="text" class="form-control" id="last_name"  name="summ" required value="{{ old('summ') }}">
                    @error('summ')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>


        </div>

        @endsection
