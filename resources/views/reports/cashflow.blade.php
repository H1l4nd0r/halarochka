@extends('layouts.app')
@section('content')
<div class="container mt-3">
    <div class="card">
        
        <div class="card-body">
            <table class="table">
            <thead>
                <tr class="align-top">
                <th scope="col">Статус договоров</th>
                <th scope="col">Выдано р.</th>
                <th scope="col">Ожидается р.</th>
                <th scope="col">Получено р.</th>
                <th scope="col">Осталось р.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stats as $stat )
                <tr class="align-top">
                    <th scope="row">{{ $stat->status_text }}</th>
                    <td>{{ number_format($stat->tdisbursed,0,'.',' ') }}</td>
                    <td>{{ number_format($stat->texpected+$stat->tfirstpayment,0,'.',' ') }}</td>
                    <td>{{ number_format($stat->texpected+$stat->tfirstpayment-$stat->tleft,0,'.',' ') }}</td>
                    <td>{{ number_format($stat->tleft,0,'.',' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
            </table>

        </div>
        
    </div>
</div>
@endsection