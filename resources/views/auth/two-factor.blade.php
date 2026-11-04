@extends('layouts.app')
@section('content')

<div class="container mt-3">

    <div class="card" style="max-width:300px;">

        <div class="card-body">
            <h5 class="card-title mb-2">Двухфакторная аутентификация</h5>
            <p class="text-muted small mb-3">
                Введите 6-значный код из Google Authenticator
                для {{ $user->email }}
            </p>

            <form method="post" action="{{ route('login.two-factor.verify') }}">
                @csrf
                <div class="mb-3">
                    <label for="code" class="form-label">Код из Google Authenticator</label>
                    <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                           class="form-control" id="code" name="code"
                           value="{{ old('code') }}" required autofocus>
                    <x-form-error name="code"/>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">Назад</a>
                    <button type="submit" class="btn btn-primary">Зайти</button>
                </div>
            </form>
        </div>

    </div>

</div>

@endsection
