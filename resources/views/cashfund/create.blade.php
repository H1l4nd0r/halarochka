@extends('layouts.app')
@section('content')

<div class="container mt-3">

    <div class="card">

        <div class="card-body">
            <div class="card-title d-flex justify-content-between">
                <x-abutton href="/cash">Отмена</x-abutton>
            </div>
            <form method="post" action="/cash">
@csrf
                <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
                <div class="mb-3">
                    <label for="factday" class="form-label">Дата операции</label>
                    <input type="date" class="form-control" id="factday" name="factday" required value="{{ old('factday') }}">
                    @error('factday')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Комментарий</label>
                    <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}">
                    @error('description')
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
