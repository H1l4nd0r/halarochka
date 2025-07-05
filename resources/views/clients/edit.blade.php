@extends('layouts.app')
@section('content')

<div class="container mt-3">

    <div class="card">

        <div class="card-body">
            <div class="card-title d-flex justify-content-end">
                <x-abutton href="/clients/{{ $client->id }}">Отмена</x-abutton>
            </div>
            <form method="POST" action="/clients/{{ $client->id }}" enctype="multipart/form-data">
@csrf
@method('PATCH')

                <div class="mb-3">
                    <label for="first_name" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $client->first_name }}" required>
                    @error('first_name')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Фамилия</label>
                    <input type="text" class="form-control" id="last_name"  name="last_name" value="{{ $client->last_name }}"  required>
                    @error('last_name')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="middle_name" class="form-label">Отчетство</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ $client->middle_name }}"  required>
                    @error('middle_name')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="borndate" class="form-label">Дата рождения</label>
                    <input type="date" class="form-control" id="borndate" name="borndate" value="{{ optional($client->borndate)->format('Y-m-d')  }}"  required>
                    @error('borndate')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Телефон</label>
                    <input type="phone" class="form-control" id="phone" name="phone" value="{{ $client->phone }}"  required>
                    @error('phone')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $client->email }}" >
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="iddoc" class="form-label">Вид документа</label>
                    <input type="text" class="form-control" id="iddoc" name="iddoc" value="{{ $client->iddoc }}"  required>
                    @error('iddoc')
                        <div class="text-danger">{{ $message }}</div>    
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="idnum" class="form-label">Серия и номер документа</label>
                    <input type="text" class="form-control" id="idnum"  name="idnum" value="{{ $client->idnum }}"  required>
                    @error('idnum')
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
