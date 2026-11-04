<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Client;
use App\Models\Repayment;
use App\Models\Payday;
use App\Models\Cashfund;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{
    //
    public function index(){
        // Базовые счётчики
        $docsnum = Deal::count();
        $clientsnum = Client::count();
        $repsnum = Repayment::count();

        // Касса: свободные средства (сумма всех проводок)
        $cashAvailable = (int) Cashfund::sum('summ');

        // Портфель в поле: активные + проблемные договоры
        $activeStatuses = [Deal::DEAL_ACTIVE, Deal::DEAL_OVERDUE];
        $portfolioCount = Deal::whereIn('status', $activeStatuses)->count();
        $portfolioSum = (int) Deal::whereIn('status', $activeStatuses)->sum('startprice');

        // К оплате: остаток по расписанию платежей по незакрытым договорам
        $expectedToReceive = (int) Payday::where('status', '!=', 2)
            ->whereNotIn('deal_id', Deal::whereIn('status', [2, 4])->select('id'))
            ->sum('leftsumm');

        // Текущий месяц: пришло (первоначальные взносы + оплаты) / ушло (выдачи)
        $monthStart = now()->startOfMonth();
        $monthIn = (int) Cashfund::whereIn('type', [Cashfund::CASHFUND_FIRSTPAYMENT, Cashfund::CASHFUND_REPAYMENT])
            ->where('factday', '>=', $monthStart)
            ->sum('summ');
        $monthOut = (int) Cashfund::where('type', Cashfund::CASHFUND_DISBURSEMENT)
            ->where('factday', '>=', $monthStart)
            ->sum('summ'); // хранится со знаком минус

        // Платежи по расписанию в ближайшие 7 дней
        $next7 = Payday::whereBetween('payday', [now(), now()->addDays(7)])
            ->where('status', '!=', 2)
            ->whereNotIn('deal_id', Deal::whereIn('status', [2, 4])->select('id'))
            ->get(['id', 'leftsumm']);
        $next7Count = $next7->count();
        $next7Sum = (int) $next7->sum('leftsumm');

        // Новые заявки, ожидающие активации
        $newCount = Deal::where('status', Deal::DEAL_NEW)->count();
        $newSum = (int) Deal::where('status', Deal::DEAL_NEW)->sum('startprice');

        // Проблемные договоры (просрочен / реструктурирован)
        $problemCount = Deal::where('status', Deal::DEAL_OVERDUE)->count();
        $problemSum = (int) Deal::where('status', Deal::DEAL_OVERDUE)->sum('startprice');

        // Портфель по статусам: количество и сумма
        $statusTotals = Deal::select('status', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(startprice) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $statusLabels = [
            Deal::DEAL_NEW => 'Новый',
            Deal::DEAL_ACTIVE => 'Активный',
            Deal::DEAL_CLOSED => 'Закрыт',
            Deal::DEAL_OVERDUE => 'Просрочен / Реструктурирован',
        ];

        $statuses = [];
        foreach ($statusLabels as $code => $label) {
            $row = $statusTotals->get((string) $code);
            $statuses[] = [
                'status' => $code,
                'label' => $label,
                'count' => $row ? (int) $row->cnt : 0,
                'sum' => $row ? (int) $row->total : 0,
            ];
        }

        return view('dashboard',[
            'docsnum' => $docsnum,
            'clientsnum' => $clientsnum,
            'repsnum' => $repsnum,
            'cashAvailable' => $cashAvailable,
            'portfolioCount' => $portfolioCount,
            'portfolioSum' => $portfolioSum,
            'expectedToReceive' => $expectedToReceive,
            'monthIn' => $monthIn,
            'monthOut' => $monthOut,
            'next7Count' => $next7Count,
            'next7Sum' => $next7Sum,
            'newCount' => $newCount,
            'newSum' => $newSum,
            'problemCount' => $problemCount,
            'problemSum' => $problemSum,
            'statuses' => $statuses,
        ]);
    }
}
