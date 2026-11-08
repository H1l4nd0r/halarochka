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
}
