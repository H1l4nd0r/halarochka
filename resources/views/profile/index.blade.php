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

            <hr>

            <h5 class="mb-3">Двухфакторная аутентификация</h5>

            @if (session('two_factor_secret'))

                <p class="text-muted small">
                    Отсканируйте QR-код в Google Authenticator (или введите ключ вручную)
                    и введите 6-значный код из приложения
                </p>

                @if ($qrSvg)
                    <div class="text-center mb-3" style="display:inline-block;">
                        {!! $qrSvg !!}
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Ключ (ввод вручную)</label>
                    <input type="text" class="form-control font-monospace" value="{{ session('two_factor_secret') }}" readonly>
                </div>

                <form method="post" action="{{ route('profile.two-factor.verify') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="two_factor_code" class="form-label">Код из Google Authenticator</label>
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                               class="form-control" id="two_factor_code" name="code"
                               value="{{ old('code') }}" required>
                        <x-form-error name="code"/>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" form="cancel-two-factor" class="btn btn-outline-secondary">Отмена</button>
                        <button type="submit" class="btn btn-primary">Подтвердить</button>
                    </div>
                </form>

                <form id="cancel-two-factor" method="post" action="{{ route('profile.two-factor.cancel') }}">
                    @csrf
                </form>

            @elseif ($user->two_factor_secret)

                <div class="mb-3">
                    <span class="badge bg-success">Включено</span>
                    <div class="text-muted small mt-1">
                        При входе в систему будет запрашиваться код из Google Authenticator
                    </div>
                </div>

                <form method="post" action="{{ route('profile.two-factor.disable') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="disable_two_factor_code" class="form-label">Код из Google Authenticator</label>
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                               class="form-control" id="disable_two_factor_code" name="code"
                               value="{{ old('code') }}" required>
                        <x-form-error name="code"/>
                    </div>

                    <div class="d-flex justify-content-between">
                        <x-abutton href="/dashboard">Отмена</x-abutton>
                        <button type="submit" class="btn btn-outline-danger">Отключить</button>
                    </div>
                </form>

            @else

                <p class="text-muted small">
                    Подключите Google Authenticator для дополнительной защиты учётной записи
                </p>

                <form method="post" action="{{ route('profile.two-factor.start') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Подключить Google Authenticator</button>
                </form>

            @endif

        </div>

    </div>

</div>

@endsection