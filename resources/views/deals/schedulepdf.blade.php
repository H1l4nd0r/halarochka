<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name','Abrafin.ru График платежей') }}</title>
    <style>
         * {
            font-family: DejaVu Sans, sans-serif !important;
        }

        body, table, td, th, p, div, span {
            font-family: DejaVu Sans, sans-serif !important;
        }
    </style>
    @vite(['resources/sass/app.scss','resources/js/app.js'])
</head>
<body>
    <div id="app">

        <div class="container mt-3">
        
            <div class="card">
            
                <div class="card-body">
                    <table>
                        <tr><td><p class="card-text">Клиент: {{ $deal->client->last_name }} {{ $deal->client->first_name }} {{ $deal->client->middle_name }}</p></td></tr>
                        <tr><td>Товар: {{ $deal->goodname }}</td></tr>
                        <tr><td>Договор: {{ $deal->id }} от {{ $deal->created_at }} </td></tr>
                        <tr><td>Статус: {{ $deal->status_text }}</td></tr>
                        <tr><td></td></tr>
                        <tr><td><h1>Условия</h1></td></tr>
                        <tr><td>Цена товара: {{ $deal->startprice }}</td></tr>
                        <tr><td>Первый взнос: {{ $deal->firstpayment }}</td></tr>
                        <tr><td>Наценка: {{ $deal->fee }}% </td></tr>
                        <tr><td>Срок: {{ $deal->term }} мес</td></tr>
                        <tr><td>Полная стоимость рассрочки: {{ $deal->fullprice }}</td></tr>
                    </table>
                    <h1>График платежей</h1>
                    <table style="border:1px dotted black;">
                        <tr>
                            <td>Дата</td>
                            <td>Сумма платежа</td>
                            <td>Осталось</td>
                            <td>статус</td>
                        <tr> 
                        @foreach ($deal->schedule as $payday)
                            <tr>
                                <td>{{ $payday->payday }}</td>
                                <td>{{ $payday->fullsumm }}</td>
                                <td>{{ $payday->leftsumm }}</td>
                                <td>{{ $payday->status_text }}</td>
                            <tr> 
                        @endforeach
                    </table>
                    <h1>Платежи</h1>
                    <table style="border:1px dotted black;">
                        <tr>
                            <td>Дата</td>
                            <td>Сумма платежа</td>
                        <tr> 
                        @foreach ($deal->repayments as $payment)
                            <tr>
                                <td>{{ $payment->factday }}</td>
                                <td>{{ $payment->summ }}</td>
                            <tr> 
                        @endforeach
                    </table>

                </div>

            </div>
        </div>
    </div>
</body>
</html>