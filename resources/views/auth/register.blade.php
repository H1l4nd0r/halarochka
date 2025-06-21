@extends('layouts.app')
@section('content')

<div class="container mt-3">

    <div class="card" style="max-width:400px;">

        <div class="card-body">
            <form method="post" action="/register">
@csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="name" name="name" required :value="old('name')">
                    <x-form-error name="name"/>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" required :value="old('email')">
                    <x-form-error name="email"/>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="text" class="form-control" id="password" name="password" required :value="old('password')">
                    <x-form-error name="password"/>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Пароль повторно</label>
                    <input type="text" class="form-control" id="password_confirmation" name="password_confirmation" required :value="old('password_confirmation')">
                    <x-form-error name="password_confirmation"/>
                </div>
                
                
                <div class="card-title d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                    <x-abutton href="/">Вход</x-abutton>  
                </div>
            </form>


        </div>

        @endsection
