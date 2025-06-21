@extends('layouts.app')
@section('content')

<div class="container mt-3">

    <div class="card">

        <div class="card-body">
            <div class="card-title d-flex justify-content-end">
                <x-abutton href="/clients">Отмена</x-abutton>
            </div>
            <form method="post" action="/clients">
@csrf
                <div class="mb-3">
                    <label for="first_name" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required :value="old('first_name')">
                    <x-form-error name="first_name"/>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Фамилия</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required :value="old('last_name')">
                    <x-form-error name="last_name"/>
                </div>
                <div class="mb-3">
                    <label for="middle_name" class="form-label">Отчетство</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name" required :value="old('middle_name')">
                    <x-form-error name="middle_name"/>
                </div>
                <div class="mb-3">
                    <label for="borndate" class="form-label">Дата рождения</label>
                    <input type="date" class="form-control" id="borndate" name="borndate" required :value="old('borndate')">
                    <x-form-error name="borndate"/>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Телефон</label>
                    <input type="phone" class="form-control" id="phone" name="phone" required :value="old('phone')">
                    <x-form-error name="phone"/>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email"  name="email" :value="old('email')">
                    <x-form-error name="email"/>
                </div>
                <div class="mb-3">
                    <label for="iddoc" class="form-label">Вид документа</label>
                    <input type="text" class="form-control" id="iddoc"  name="iddoc" required :value="old('iddoc')">
                    <x-form-error name="iddoc"/>
                </div>
                <div class="mb-3">
                    <label for="idnum" class="form-label">Серия и номер документа</label>
                    <input type="text" class="form-control" id="idnum"  name="idnum" required :value="old('idnum')">
                    <x-form-error name="idnum"/>
                </div>

                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>


        </div>

        @endsection
