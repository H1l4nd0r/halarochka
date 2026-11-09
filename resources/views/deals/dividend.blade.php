@extends('layouts.app')
@section('content')

<div class="container mt-3">

    <div class="card">

        <div class="card-body">
            <div class="card-title d-flex justify-content-between">
                <h5>Вывод дивидендов · Договор №{{ $deal->id }}</h5>
                <x-abutton href="/deals/{{ $deal->id }}">Отмена</x-abutton>
            </div>

            <div class="mb-3">
                <p class="card-text mb-1">Наценка (прибыль): {{ number_format($deal->fee,0,'.',' ') }} (р.)</p>
                <p class="card-text mb-1">Уже выведено: {{ number_format($deal->fee_paid,0,'.',' ') }} (р.)</p>
                <p class="card-text mb-0">Осталось к выводу: <strong>{{ number_format($deal->fee_remaining,0,'.',' ') }}</strong> (р.)</p>
            </div>

            <form method="post" action="/deals/{{ $deal->id }}/dividends">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
                <div class="mb-3">
                    <label for="factday" class="form-label">Дата операции</label>
                    <input type="date" class="form-control" id="factday" name="factday" required value="{{ old('factday', date('Y-m-d')) }}">
                    @error('factday')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Сумма (р.)</label>
                    <input type="number" class="form-control" id="amount" name="amount" min="1" step="1" value="{{ old('amount') }}">
                    <div class="form-text">Заполните сумму ИЛИ процент. Если заполнены оба — расчёт идёт по проценту.</div>
                    @error('amount')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="percent" class="form-label">Доля от наценки, %</label>
                    <input type="number" class="form-control" id="percent" name="percent" min="0.01" max="100" step="0.01" value="{{ old('percent') }}">
                    @error('percent')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <div class="alert alert-secondary" id="amountPreview">
                        К выводу: — (р.)
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Комментарий</label>
                    <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}">
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Вывести дивиденды</button>
            </form>

            <script>
                const FEE = {{ $deal->fee }};
                const REMAINING = {{ $deal->fee_remaining }};
                const amountInput = document.getElementById('amount');
                const percentInput = document.getElementById('percent');
                const preview = document.getElementById('amountPreview');

                function updatePreview() {
                    let amount = 0;
                    if (percentInput.value !== '') {
                        amount = Math.round(FEE * parseFloat(percentInput.value) / 100);
                    } else if (amountInput.value !== '') {
                        amount = Math.round(parseFloat(amountInput.value));
                    }
                    if (amount > REMAINING) {
                        preview.textContent = 'К выводу: ' + amount.toLocaleString('ru-RU') + ' (р.) — превышает доступный остаток ' + REMAINING.toLocaleString('ru-RU') + ' (р.)';
                        preview.classList.remove('alert-secondary');
                        preview.classList.add('alert-danger');
                    } else {
                        preview.textContent = 'К выводу: ' + (amount > 0 ? amount.toLocaleString('ru-RU') : '—') + ' (р.)';
                        preview.classList.remove('alert-danger');
                        preview.classList.add('alert-secondary');
                    }
                }
                amountInput.addEventListener('input', updatePreview);
                percentInput.addEventListener('input', updatePreview);
                updatePreview();
            </script>
        </div>

    </div>

@endsection