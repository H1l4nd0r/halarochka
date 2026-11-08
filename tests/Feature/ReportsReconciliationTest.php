<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Deal;
use App\Models\Repayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReportsReconciliationTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthUser(): User
    {
        return User::create([
            'name' => 'Тест',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    private function createClient(): Client
    {
        return Client::create([
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
    }

    private function makeRepayments(int $dealId, array $sums): void
    {
        foreach ($sums as $sum) {
            Repayment::create([
                'deal_id' => $dealId,
                'factday' => now(),
                'status' => 1,
                'summ' => $sum,
                'idempotency_key' => (string) Str::uuid(),
            ]);
        }
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/reports/reconciliation')->assertRedirect('/login');
    }

    public function test_report_shows_closed_deal_with_payment_mismatch(): void
    {
        $user = $this->createAuthUser();
        $client = $this->createClient();

        // Закрытый договор: полная стоимость 1 200 000, поступило 1 100 000
        $mismatch = Deal::create([
            'client_id' => $client->id,
            'goodname' => 'Ноутбук',
            'startprice' => 1000000,
            'firstpayment' => 100000,
            'fee' => 100000,
            'term' => 12,
            'fullprice' => 1200000,
            'status' => Deal::DEAL_CLOSED,
            'dealdate' => now()->subMonths(13),
            'idempotency_key' => (string) Str::uuid(),
        ]);
        $this->makeRepayments($mismatch->id, [550000, 550000]); // итого 1 100 000

        $response = $this->actingAs($user)->get('/reports/reconciliation');

        $response->assertOk();
        // Договор с расхождением присутствует
        $response->assertSee('№' . $mismatch->id);
        // Полная стоимость
        $response->assertSee(number_format(1200000, 0, '.', ' '));
        // Сумма платежей
        $response->assertSee(number_format(1100000, 0, '.', ' '));
        // Разница -100 000
        $response->assertSee('-' . number_format(100000, 0, '.', ' '));
        // Недоплата отображается красным со знаком минус
        $response->assertSee('text-danger">-100 000</td>', false);
    }

    public function test_report_hides_closed_deal_where_payments_match_fullprice(): void
    {
        $user = $this->createAuthUser();
        $client = $this->createClient();

        // Закрытый договор без расхождений: полная стоимость 220 000, поступило 220 000
        $balanced = Deal::create([
            'client_id' => $client->id,
            'goodname' => 'Телефон',
            'startprice' => 200000,
            'firstpayment' => 0,
            'fee' => 20000,
            'term' => 6,
            'fullprice' => 220000,
            'status' => Deal::DEAL_CLOSED,
            'dealdate' => now()->subMonths(5),
            'idempotency_key' => (string) Str::uuid(),
        ]);
        $this->makeRepayments($balanced->id, [110000, 110000]); // итого 220 000

        $response = $this->actingAs($user)->get('/reports/reconciliation');

        $response->assertOk();
        // Договор без расхождений не отображается
        $response->assertDontSee('№' . $balanced->id);
        // Пустое состояние
        $response->assertSee('Несоответствий нет');
    }

    public function test_report_shows_overpayment_in_green(): void
    {
        $user = $this->createAuthUser();
        $client = $this->createClient();

        // Закрытый договор с переплатой: полная стоимость 220 000, поступило 250 000
        $overpaid = Deal::create([
            'client_id' => $client->id,
            'goodname' => 'Телефон',
            'startprice' => 200000,
            'firstpayment' => 0,
            'fee' => 20000,
            'term' => 6,
            'fullprice' => 220000,
            'status' => Deal::DEAL_CLOSED,
            'dealdate' => now()->subMonths(5),
            'idempotency_key' => (string) Str::uuid(),
        ]);
        $this->makeRepayments($overpaid->id, [250000]); // итого 250 000, переплата +30 000

        $response = $this->actingAs($user)->get('/reports/reconciliation');

        $response->assertOk();
        // Договор с переплатой присутствует
        $response->assertSee('№' . $overpaid->id);
        // Полная стоимость и сумма платежей
        $response->assertSee(number_format(220000, 0, '.', ' '));
        $response->assertSee(number_format(250000, 0, '.', ' '));
        // Переплата отображается зелёным
        $response->assertSee('text-success">30 000</td>', false);
    }

    public function test_report_totals_split_over_and_under(): void
    {
        $user = $this->createAuthUser();
        $client = $this->createClient();

        // Недоплата: полная стоимость 1 200 000, поступило 1 100 000 => -100 000
        $under = Deal::create([
            'client_id' => $client->id,
            'goodname' => 'Ноутбук',
            'startprice' => 1000000,
            'firstpayment' => 100000,
            'fee' => 100000,
            'term' => 12,
            'fullprice' => 1200000,
            'status' => Deal::DEAL_CLOSED,
            'dealdate' => now()->subMonths(13),
            'idempotency_key' => (string) Str::uuid(),
        ]);
        $this->makeRepayments($under->id, [1100000]); // недоплата -100 000

        // Переплата: полная стоимость 220 000, поступило 250 000 => +30 000
        $over = Deal::create([
            'client_id' => $client->id,
            'goodname' => 'Телефон',
            'startprice' => 200000,
            'firstpayment' => 0,
            'fee' => 20000,
            'term' => 6,
            'fullprice' => 220000,
            'status' => Deal::DEAL_CLOSED,
            'dealdate' => now()->subMonths(5),
            'idempotency_key' => (string) Str::uuid(),
        ]);
        $this->makeRepayments($over->id, [250000]); // переплата +30 000

        $response = $this->actingAs($user)->get('/reports/reconciliation');

        $response->assertOk();
        // Итоги считаются по типам: переплаты и недоплаты отдельно
        $response->assertSee('Переплаты: 30 000', false);
        $response->assertSee('Недоплаты: -100 000', false);
    }
}
