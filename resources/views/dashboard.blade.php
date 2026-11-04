@extends('layouts.app')
@section('content')

<div class="container mt-3">

    <div class="row">

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-wallet2 me-2 text-primary"></i>Касса
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Свободные средства: инвестиции + поступившие оплаты − выданные займы (сумма всех проводок кассы)"></i>
                    </h5>
                    <div class="fs-4">{{ number_format($cashAvailable, 0, '.', ' ') }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-credit-card me-2 text-primary"></i>Портфель в поле
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Сумма выданных средств по активным и проблемным договорам"></i>
                    </h5>
                    <div class="fs-4">{{ number_format($portfolioSum, 0, '.', ' ') }}</div>
                    <div class="text-muted small">{{ $portfolioCount }} догов.</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-hourglass-split me-2 text-info"></i>К оплате
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Остаток по расписанию платежей по незакрытым договорам"></i>
                    </h5>
                    <div class="fs-4">{{ number_format($expectedToReceive, 0, '.', ' ') }}</div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-3">

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-arrow-left-right me-2 text-success"></i>Этот месяц
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Поступления (первоначальные взносы и оплаты) и выдачи за текущий месяц"></i>
                    </h5>
                    <div class="text-success">Пришло: {{ number_format($monthIn, 0, '.', ' ') }}</div>
                    <div class="text-danger">Ушло: {{ number_format(abs($monthOut), 0, '.', ' ') }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-calendar-event me-2 text-warning"></i>Ближайшие 7 дней
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Ожидаемые по расписанию платежи в ближайшие 7 дней: количество и сумма"></i>
                    </h5>
                    <div class="fs-4">{{ number_format($next7Sum, 0, '.', ' ') }}</div>
                    <div class="text-muted small">{{ $next7Count }} плат.</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-file-earmark-plus me-2 text-secondary"></i>Новые заявки
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Договоры в статусе «Новый», ожидающие активации"></i>
                    </h5>
                    <div class="fs-4">{{ number_format($newSum, 0, '.', ' ') }}</div>
                    <div class="text-muted small">{{ $newCount }} заявок</div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-3">

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-exclamation-triangle me-2 text-danger"></i>Проблемные
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Договоры со статусом «Просрочен / Реструктурирован»"></i>
                    </h5>
                    <div class="fs-4">{{ number_format($problemSum, 0, '.', ' ') }}</div>
                    <div class="text-muted small">{{ $problemCount }} догов.</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-people me-2"></i>Клиентов
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Всего клиентов в системе"></i>
                    </h5>
                    <div class="fs-4">{{ $clientsnum }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-file-earmark-text me-2"></i>Договоров
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Всего договоров всех статусов"></i>
                    </h5>
                    <div class="fs-4">{{ $docsnum }}</div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-3">

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-receipt me-2"></i>Платежей
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Всего поступивших оплат"></i>
                    </h5>
                    <div class="fs-4">{{ $repsnum }}</div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-3">

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-kanban me-2"></i>Портфель по статусам
                        <i class="bi bi-info-circle ms-1 small text-muted" data-bs-toggle="tooltip"
                           data-bs-title="Распределение договоров по статусам: количество и сумма"></i>
                    </h5>

                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Статус</th>
                                <th class="text-end" style="width: 120px;">Кол-во</th>
                                <th class="text-end" style="width: 200px;">Сумма</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($statuses as $s)
                            <tr>
                                <td>
                                    @switch($s['status'])
                                        @case (0)
                                            <span class="badge bg-secondary">{{ $s['label'] }}</span>
                                            @break
                                        @case (1)
                                            <span class="badge bg-primary">{{ $s['label'] }}</span>
                                            @break
                                        @case (2)
                                            <span class="badge bg-success">{{ $s['label'] }}</span>
                                            @break
                                        @default
                                            <span class="badge bg-danger">{{ $s['label'] }}</span>
                                    @endswitch
                                </td>
                                <td class="text-end">{{ $s['count'] }}</td>
                                <td class="text-end">{{ number_format($s['sum'], 0, '.', ' ') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection