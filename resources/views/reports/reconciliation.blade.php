@extends('layouts.app')
@section('content')
<div class="container mt-3">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr class="align-top">
                        <th scope="col">Договор</th>
                        <th scope="col">Клиент</th>
                        <th scope="col" class="text-end">Полная стоимость (р.)</th>
                        <th scope="col" class="text-end">Сумма платежей (р.)</th>
                        <th scope="col" class="text-end">Разница (р.)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($deals as $deal)
                    <tr class="align-top">
                        <td><a href="/deals/{{ $deal->id }}">№{{ $deal->id }} от {{ $deal->dealdate->format('d-m-Y') }}</a></td>
                        <td><a href="/clients/{{ $deal->client_id }}">{{ $deal->client->last_name }} {{ $deal->client->first_name }}</a></td>
                        <td class="text-end">{{ number_format($deal->full_cost, 0, '.', ' ') }}</td>
                        <td class="text-end">{{ number_format($deal->paid_total, 0, '.', ' ') }}</td>
                        <td class="text-end {{ $deal->difference > 0 ? 'text-success' : 'text-danger' }}">{{ number_format($deal->difference, 0, '.', ' ') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Несоответствий нет — суммы платежей всех закрытых договоров равны их полной стоимости.</td>
                    </tr>
                    @endforelse
                    @if($deals->isNotEmpty())
                    <tr class="align-top">
                        <th scope="row" colspan="2">Итого</th>
                        <td class="text-end">{{ number_format($totals->full_cost, 0, '.', ' ') }}</td>
                        <td class="text-end">{{ number_format($totals->paid_total, 0, '.', ' ') }}</td>
                        <td class="text-end">
                            <div class="text-success">Переплаты: {{ number_format($totals->overpayments, 0, '.', ' ') }}</div>
                            <div class="text-danger">Недоплаты: {{ number_format($totals->underpayments, 0, '.', ' ') }}</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
