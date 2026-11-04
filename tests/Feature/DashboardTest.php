<?php

namespace Tests\Feature;

use App\Models\Cashfund;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Payday;
use App\Models\Repayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Фикстуры, покрывающие все метрики дашборда.
     */
    private function createFixtures(): User
    {
        $user = User::create([
            'name' => 'Тест',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $client = Client::create([
            'first_name' => 'Иван',
            'middle_name' => 'Иванович',
            'last_name' => 'Иванов',
            'phone' => '+70000000000',
            'email' => 'ivan@example.com',
            'borndate' => '1990-01-01',
            'iddoc' => 'Паспорт',
            'idnum' => '1234567',
            'idempotency_key' => (string) Str::uuid(),
        ]);

        // Инвестиция, чтобы хватало средств при активации договора
        Cashfund::create([
            'summ' => 1000000,
            'type' => Cashfund::CASHFUND_INVESTMENT,
            'factday' => now(),
            'idempotency_key' => (string) Str::uuid(),
        ]);

        // Активный договор: хук created строит расписание и проводки кассы
        // (выдача -1 000 000, первый взнос +100 000, 12 платежей по 100 000)
        // dealdate 13 месяцев назад: все платежи расписания в прошлом
        // (чтобы не попадали в метрику «ближайшие 7 дней»)
        $dealdate = now()->subMonths(13);
        request()->merge(['dealdate' => $dealdate->toDateString()]);

        $active = Deal::create([
            'client_id' => $client->id,
            'goodname' => 'Ноутбук',
            'startprice' => 1000000,
            'firstpayment' => 100000,
            'fee' => 100000,
            'term' => 12,
            'fullprice' => 1200000,
            'status' => Deal::DEAL_ACTIVE,
            'dealdate' => $dealdate,
            'idempotency_key' => (string) Str::uuid(),
        ]);

        // Платёж по расписанию в ближайшие 7 дней
        Payday::create([
            'deal_id' => $active->id,
            'payday' => now()->addDays(3),
            'status' => 0,
            'fullsumm' => 75000,
            'leftsumm' => 75000,
        ]);

        // Проблемный договор (статус 3)
        Deal::create([
            'client_id' => $client->id,
            'goodname' => 'Телефон',
            'startprice' => 200000,
            'firstpayment' => 0,
            'fee' => 20000,
            'term' => 6,
            'fullprice' => 220000,
            'status' => Deal::DEAL_OVERDUE,
            'dealdate' => now()->subMonths(5),
            'idempotency_key' => (string) Str::uuid(),
        ]);

        // Новая заявка (статус 0)
        Deal::create([
            'client_id' => $client->id,
            'goodname' => 'Плечевая',
            'startprice' => 500000,
            'firstpayment' => 0,
            'fee' => 50000,
            'term' => 10,
            'fullprice' => 550000,
            'status' => Deal::DEAL_NEW,
            'dealdate' => now(),
            'idempotency_key' => (string) Str::uuid(),
        ]);

        // Закрытый договор
        $closed = Deal::create([
            'client_id' => $client->id,
            'goodname' => 'Бутсы',
            'startprice' => 300000,
            'firstpayment' => 0,
            'fee' => 30000,
            'term' => 6,
            'fullprice' => 330000,
            'status' => Deal::DEAL_CLOSED,
            'dealdate' => now()->subMonths(12),
            'idempotency_key' => (string) Str::uuid(),
        ]);

        Payday::create([
            'deal_id' => $closed->id,
            'payday' => now()->subDays(10),
            'status' => 2,
            'fullsumm' => 55000,
            'leftsumm' => 0,
        ]);

        // Движение кассы в текущем месяце: оплата +50 000, выдача -30 000
        Cashfund::create([
            'deal_id' => $active->id,
            'summ' => 50000,
            'type' => Cashfund::CASHFUND_REPAYMENT,
            'factday' => now(),
            'idempotency_key' => (string) Str::uuid(),
        ]);
        Cashfund::create([
            'deal_id' => $active->id,
            'summ' => -30000,
            'type' => Cashfund::CASHFUND_DISBURSEMENT,
            'factday' => now(),
            'idempotency_key' => (string) Str::uuid(),
        ]);

        // Поступившая оплата
        Repayment::create([
            'deal_id' => $active->id,
            'factday' => now(),
            'status' => 0,
            'summ' => 50000,
            'idempotency_key' => (string) Str::uuid(),
        ]);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_shows_metrics(): void
    {
        $user = $this->createFixtures();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();

        // Касса: 1 000 000 − 1 000 000 + 100 000 + 50 000 − 30 000 = 120 000
        $response->assertSee(number_format(120000, 0, '.', ' '));

        // Портфель в поле: 1 000 000 + 200 000 = 1 200 000 (2 договора)
        $response->assertSee(number_format(1200000, 0, '.', ' '));
        $response->assertSee('2 догов.');

        // К оплате: 12 × 100 000 (расписание активного) + 75 000 = 1 275 000
        $response->assertSee(number_format(1275000, 0, '.', ' '));

        // Этот месяц: пришло 50 000, ушло 30 000
        $response->assertSee(number_format(50000, 0, '.', ' '));
        $response->assertSee(number_format(30000, 0, '.', ' '));

        // Ближайшие 7 дней: 1 платёж на 75 000
        $response->assertSee(number_format(75000, 0, '.', ' '));
        $response->assertSee('1 плат.');

        // Новые заявки: 1 на 500 000
        $response->assertSee(number_format(500000, 0, '.', ' '));
        $response->assertSee('1 заявок');

        // Проблемные: 1 на 200 000
        $response->assertSee(number_format(200000, 0, '.', ' '));

        // Базовые счётчики
        $response->assertSee('Клиентов');
        $response->assertSee('Договоров');
        $response->assertSee('Платежей');

        // Таблица «Портфель по статусам»
        $response->assertSee('Портфель по статусам');
        $response->assertSee('Просрочен / Реструктурирован');
    }

    public function test_dashboard_cards_have_icons_and_tooltips(): void
    {
        $user = $this->createFixtures();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();

        // Иконки карточек
        $response->assertSee('bi-wallet2');
        $response->assertSee('bi-credit-card');
        $response->assertSee('bi-hourglass-split');
        $response->assertSee('bi-arrow-left-right');
        $response->assertSee('bi-calendar-event');
        $response->assertSee('bi-file-earmark-plus');
        $response->assertSee('bi-exclamation-triangle');
        $response->assertSee('bi-people');
        $response->assertSee('bi-file-earmark-text');
        $response->assertSee('bi-receipt');
        $response->assertSee('bi-kanban');

        // Тултипы с описанием метрик (escape=false: в атрибутах есть кавычки)
        $response->assertSee('data-bs-toggle="tooltip"', false);
        $response->assertSee('data-bs-title="Свободные средства', false);
        $response->assertSee('data-bs-title="Сумма выданных средств', false);
    }
}
