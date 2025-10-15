@extends('layouts.app')
@section('content')
<div class="container mt-3">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr class="align-top">
                        <th scope="col">Договор</th>
                        <th scope="col" class="text-end">Дата платежа</th>
                        <th scope="col" class="text-end">Сумма платежа р.</th>
                        <th scope="col" class="text-end">Осталось р.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paydays as $payday )
                    <tr class="align-top">
                        <td scope="row">
                            <a href="/deals/{{ $payday->deal_id }}">№{{ $payday->deal_id }} от {{ $payday->deal->dealdate->format('d-m-Y') }}</a>
                        </td>
                        <td class="text-end">{{ $payday->payday->format('d-m-Y') }}</td>
                        <td class="text-end">{{ number_format($payday->fullsumm,0,'.',' ') }}</td>
                        <td class="text-end">{{ number_format($payday->leftsumm,0,'.',' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
