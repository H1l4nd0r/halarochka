@extends('layouts.app')
@section('content')
<div class="container mt-3">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr class="align-top">
                        <th scope="col">Инвестиции</th>
                        <th scope="col" class="text-end">Получено по закрытым р.</th>
                        <th scope="col" class="text-end">Ожидается по активным р.</th>
                        <th scope="col" class="text-end">Итого ожидаемая прибыль р.</th>
                        <th scope="col" class="text-end">Итого с инвестициями р.</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <tr class="align-top">
                        <td class="text-end">{{ number_format($totalInvestments,0,'.',' ') }}</td>
                        <td class="text-end">{{ number_format($totalFees['closedFees'],0,'.',' ') }}</td>
                        <td class="text-end">{{ number_format($totalFees['expectingFees'],0,'.',' ') }}</td>
                        <td class="text-end">{{ number_format($totalFees['closedFees'] + $totalFees['expectingFees'],0,'.',' ') }}</td>
                        <td class="text-end">{{ number_format($totalInvestments+$totalFees['closedFees'] + $totalFees['expectingFees'],0,'.',' ') }}</td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
