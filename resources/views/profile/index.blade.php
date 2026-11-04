@extends('layouts.app')
@section('content')

<div class="container mt-3">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif

    <div class="card" style="max-width: 500px;">

        <div class="card-header">
            Профиль пользователя
        </div>

        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
            </div>

            <hr>

            <h5 class="mb-3">Смена пароля</h5>

            <form method="post" action="/profile/password">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="current_password" class="form-label">Текущий пароль</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                    <x-form-error name="current_password"/>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Новый пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <x-form-error name="password"/>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Повторите новый пароль</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div class="d-flex justify-content-between">
                    <x-abutton href="/dashboard">Отмена</x-abutton>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>

        </div>

    </div>

</div>

@endsection