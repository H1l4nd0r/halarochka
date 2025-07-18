@extends('layouts.app')
@section('content')

<div class="container mt-3">

    <div class="card">

        <div class="card-body">
            <div class="card-title d-flex justify-content-end">
                <x-abutton href="/deals">Отмена</x-abutton>
            </div>
            <form method="post" action="/deals" enctype="multipart/form-data">
@csrf
                <div class="mb-3">
                    <label for="startprice" class="form-label">Клиент</label>
                    <select class="form-select" aria-label="Default select example" name="client_id">
                        <option {{ old('user_id')!=null?'':'selected' }}>...</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id?'selected':'' }}>{{ $client->id }}&nbsp;{{ $client->last_name }}&nbsp;{{ $client->first_name }}&nbsp;{{ $client->middle_name }}&nbsp;{{ $client->phone }}</option> 
                        @endforeach
                    </select>
                    @error('client_id')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="dealdate" class="form-label">Дата заключения договора</label>
                    <input type="date" class="form-control" id="dealdate" name="dealdate" required value="{{ old('dealdate') }}">
                    @error('dealdate')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="goodname" class="form-label">Описание сделки/товара</label>
                    <textarea class="form-control" id="goodname" rows="3" name="goodname">{{ old('goodname') }}</textarea>
                    @error('goodname')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="startprice" class="form-label">Начальная цена товара (р.)</label>
                    <input type="number" class="form-control" id="startprice" name="startprice" value="{{ old('startprice') }}">
                    @error('startprice')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="firstpayment" class="form-label">Первоначальный взнос (р.)</label>
                    <input type="number" class="form-control" id="firstpayment" name="firstpayment" value="{{ old('firstpayment') }}">
                    @error('firstpayment')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="fee" class="form-label">Наценка  (р.)</label>
                    <input type="number" class="form-control" id="fee" name="fee" value="{{ old('fee') }}">
                    @error('fee')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="term" class="form-label">Срок (мес)</label>
                    <input type="number" class="form-control" id="term" name="term" value="{{ old('term') }}">
                    @error('term')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="formFile" class="form-label">Загрузить документ</label>
                    <input class="form-control" type="file" id="formFile" name="files[]" multiple>
                </div>

                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>


        </div>

        @endsection
