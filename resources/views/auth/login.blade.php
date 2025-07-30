@extends('layouts.app')
@section('content')

<div class="container mt-3">

    <div class="card" style="max-width:300px;">

        <div class="card-body">
            <form method="post" action="/login">
@csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" required :value="old('email')">
                    <x-form-error name="email"/>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required :value="old('password')">
                    <x-form-error name="password"/>
                </div>
                
                <div class="card-title d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Зайти</button>
                    <x-abutton href="/register">Регистрация</x-abutton>  
                </div>
            </form>


        </div>

        @endsection
