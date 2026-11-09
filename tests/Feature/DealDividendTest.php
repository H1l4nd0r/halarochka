<?php

namespace Tests\Feature;

use App\Models\Cashfund;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class DealDividendTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $email, array $roles = []): User
    {
        $user = User::create([
            'name' => 'Тест',
            'email' => $email,
            'password' => Hash::make('password'),
        ]);

        foreach ($roles as $roleName) {
            $role = Role::firstWhere('name', $roleName);
            if (! $role) {
                $role = new Role();
                $role->name = $roleName;
                $role->save();
            }
            $user->roles()->attach($role->id);
        }

        return $user;
    }

    private function makeClient(): Client
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

    private function makeClosedDeal(int $fee = 100000): Deal
    {
        return Deal::create([
            'client_id' => $this->makeClient()->id,
            'goodname' => 'Ноутбук',
            'startprice' => 1000000,
            'firstpayment' => 100000,
            'fee' => $fee,
            'term' => 12,
            'fullprice' => 1100000,
            'status' => Deal::DEAL_CLOSED,
            'dealdate' => now()->subMonths(13),
            'idempotency_key' => (string) Str::uuid(),
        ]);
    }

    private function makeActiveDeal(): Deal
    {
        // инвестиция, чтобы хватило средств при активации (хук created строит расписание)
        Cashfund::create([
            'summ' => 1000000,
            'type' => Cashfund::CASHFUND_INVESTMENT,
            'factday' => now(),
            'idempotency_key' => (string) Str::uuid(),
        ]);

        $dealdate = now()->subMonths(2);
        request()->merge(['dealdate' => $dealdate->toDateString()]);

        return Deal::create([
            'client_id' => $this->makeClient()->id,
            'goodname' => 'Телефон',
            'startprice' => 500000,
            'firstpayment' => 50000,
            'fee' => 50000,
            'term' => 6,
            'fullprice' => 500000,
            'status' => Deal::DEAL_ACTIVE,
            'dealdate' => $dealdate,
            'idempotency_key' => (string) Str::uuid(),
        ]);
    }

    private function dividendPayload(array $extra = []): array
    {
        return array_merge([
            'factday' => now()->toDateString(),
            'idempotency_key' => (string) Str::uuid(),
        ], $extra);
    }

    // ---------- 1. Тип операции в кассе и фильтр ----------

    public function test_cash_index_lists_dividend_type_in_filter(): void
    {
        $this->actingAs($this->makeUser('user@example.com'))
            ->get('/cash')
            ->assertOk()
            ->assertSee('Вывод дивидендов');
    }

    public function test_cash_index_filters_by_dividend_type(): void
    {
        $this->makeClosedDeal();

        Cashfund::create([
            'summ' => 500000,
            'type' => Cashfund::CASHFUND_INVESTMENT,
            'factday' => now(),
            'idempotency_key' => (string) Str::uuid(),
        ]);
        Cashfund::create([
            'summ' => -30000,
            'type' => Cashfund::CASHFUND_DIVIDEND,
            'deal_id' => $this->makeClosedDeal()->id,
            'factday' => now(),
            'idempotency_key' => (string) Str::uuid(),
        ]);

        $response = $this->actingAs($this->makeUser('user@example.com'))
            ->get('/cash?type=' . Cashfund::CASHFUND_DIVIDEND);

        $response->assertOk();
        // только одна проводка этого типа
        $this->assertSame(1, substr_count($response->getContent(), '<th scope="row">'));
        $response->assertSee('-30000');
        // тип виден в таблице
        $response->assertSee('Вывод дивидендов');
    }

    // ---------- 2. Кнопка на карточке закрытого договора ----------

    public function test_dividend_button_visible_to_admin_on_closed_deal(): void
    {
        $deal = $this->makeClosedDeal();

        $this->actingAs($this->makeUser('admin@example.com', ['admin']))
            ->get('/deals/' . $deal->id)
            ->assertOk()
            ->assertSee('Вывести дивиденды')
            ->assertSee("/deals/{$deal->id}/dividends");
    }

    public function test_dividend_button_hidden_from_non_admin_and_on_active_deal(): void
    {
        $closed = $this->makeClosedDeal();
        $active = $this->makeActiveDeal();

        // не-admin по закрытому договору
        $this->actingAs($this->makeUser('user@example.com'))
            ->get('/deals/' . $closed->id)
            ->assertOk()
            ->assertDontSee('Вывести дивиденды');

        // admin по активному договору
        $this->actingAs($this->makeUser('admin2@example.com', ['admin']))
            ->get('/deals/' . $active->id)
            ->assertOk()
            ->assertDontSee('Вывести дивиденды');
    }

    // ---------- 3. Форма вывода ----------

    public function test_dividend_form_shown_to_admin_and_forbidden_for_others(): void
    {
        $deal = $this->makeClosedDeal(100000);

        // роли кэшируются в сессии — сбрасываем её между запросами разных пользователей
        $this->flushSession();
        $this->actingAs($this->makeUser('user@example.com'))
            ->get('/deals/' . $deal->id . '/dividends')
            ->assertForbidden();

        $this->flushSession();
        $this->actingAs($this->makeUser('admin@example.com', ['admin']))
            ->get('/deals/' . $deal->id . '/dividends')
            ->assertOk()
            ->assertSee('Осталось к выводу')
            ->assertSee(number_format(100000, 0, '.', ' '));
    }

    // ---------- 4. Вывод дивидендов ----------

    public function test_store_dividend_by_amount(): void
    {
        $admin = $this->makeUser('admin@example.com', ['admin']);
        $deal = $this->makeClosedDeal(100000);

        $response = $this->actingAs($admin)
            ->post('/deals/' . $deal->id . '/dividends', $this->dividendPayload([
                'amount' => 30000,
                'description' => 'Дивиденды партнеру',
            ]));

        $response->assertRedirect('/deals/' . $deal->id);
        $response->assertSessionHas('success');

        $fund = Cashfund::where('type', Cashfund::CASHFUND_DIVIDEND)->first();
        $this->assertNotNull($fund);
        $this->assertSame(-30000, (int) $fund->summ);
        $this->assertSame($deal->id, $fund->deal_id);
        $this->assertSame($admin->id, $fund->user_id);
        $this->assertSame('Дивиденды партнеру', $fund->description);

        $this->assertSame(30000, (int) $deal->refresh()->fee_paid);
        $this->assertSame(70000, $deal->fee_remaining);
    }

    public function test_store_dividend_by_percent(): void
    {
        $admin = $this->makeUser('admin@example.com', ['admin']);
        $deal = $this->makeClosedDeal(100000);

        $response = $this->actingAs($admin)
            ->post('/deals/' . $deal->id . '/dividends', $this->dividendPayload([
                'percent' => 25,
            ]));

        $response->assertRedirect('/deals/' . $deal->id);

        $fund = Cashfund::where('type', Cashfund::CASHFUND_DIVIDEND)->first();
        $this->assertNotNull($fund);
        $this->assertSame(-25000, (int) $fund->summ);
        $this->assertStringContainsString("Вывод дивидендов: договор №{$deal->id}", $fund->description);

        $this->assertSame(25000, (int) $deal->refresh()->fee_paid);
        $this->assertSame(75000, $deal->fee_remaining);
    }

    public function test_store_requires_amount_or_percent(): void
    {
        $admin = $this->makeUser('admin@example.com', ['admin']);
        $deal = $this->makeClosedDeal();

        $response = $this->actingAs($admin)
            ->post('/deals/' . $deal->id . '/dividends', $this->dividendPayload());

        $response->assertRedirect();
        $response->assertSessionHasErrors(['amount', 'percent']);
        $this->assertSame(0, Cashfund::where('type', Cashfund::CASHFUND_DIVIDEND)->count());
        $this->assertSame(0, (int) $deal->refresh()->fee_paid);
    }

    public function test_cannot_withdraw_more_than_fee(): void
    {
        $admin = $this->makeUser('admin@example.com', ['admin']);
        $deal = $this->makeClosedDeal(100000);

        $response = $this->actingAs($admin)
            ->post('/deals/' . $deal->id . '/dividends', $this->dividendPayload([
                'amount' => 120000,
            ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('amount');
        $this->assertSame(0, Cashfund::where('type', Cashfund::CASHFUND_DIVIDEND)->count());
        $this->assertSame(0, (int) $deal->refresh()->fee_paid);
    }

    public function test_partial_withdrawal_then_overdraw_fails(): void
    {
        $admin = $this->makeUser('admin@example.com', ['admin']);
        $deal = $this->makeClosedDeal(100000);

        // первый вывод 60 000
        $this->actingAs($admin)
            ->post('/deals/' . $deal->id . '/dividends', $this->dividendPayload(['amount' => 60000]))
            ->assertRedirect('/deals/' . $deal->id);
        $this->assertSame(60000, (int) $deal->refresh()->fee_paid);

        // второй: 50 000 > остатка 40 000
        $response = $this->actingAs($admin)
            ->post('/deals/' . $deal->id . '/dividends', $this->dividendPayload(['amount' => 50000]));

        $response->assertSessionHasErrors('amount');
        $this->assertSame(60000, (int) $deal->refresh()->fee_paid);
        $this->assertSame(1, Cashfund::where('type', Cashfund::CASHFUND_DIVIDEND)->count());
    }

    public function test_non_admin_cannot_store_dividend(): void
    {
        $deal = $this->makeClosedDeal();

        $response = $this->actingAs($this->makeUser('user@example.com'))
            ->post('/deals/' . $deal->id . '/dividends', $this->dividendPayload(['amount' => 1000]));

        $response->assertForbidden();
        $this->assertSame(0, Cashfund::count());
        $this->assertSame(0, (int) $deal->refresh()->fee_paid);
    }
}