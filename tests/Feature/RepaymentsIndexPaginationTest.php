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

class RepaymentsIndexPaginationTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'name' => 'Тест',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    private function createRepayments(int $count): void
    {
        $client = Client::create([
            'first_name' => 'Иван',
            'middle_name' => 'Иванович',
            'last_name' => 'Фамилия',
            'phone' => '+70000000000',
            'borndate' => '1990-01-01',
            'iddoc' => 'паспорт',
            'idnum' => '123456',
            'idempotency_key' => (string) Str::uuid(),
        ]);

        $deal = Deal::create([
            'client_id' => $client->id,
            'dealdate' => '2025-01-01',
            'goodname' => 'Товар',
            'startprice' => 1000000,
            'firstpayment' => 100000,
            'fee' => 10000,
            'term' => 12,
            'fullprice' => 1200000,
            // DEAL_NEW: bypasses the cash-fund check and reschedule() in the created hook
            'status' => Deal::DEAL_NEW,
            'idempotency_key' => (string) Str::uuid(),
        ]);

        for ($i = 1; $i <= $count; $i++) {
            Repayment::create([
                'deal_id' => $deal->id,
                'factday' => '2025-0' . min($i, 9) . '-15',
                'status' => 1,
                'summ' => 100000,
                'idempotency_key' => (string) Str::uuid(),
            ]);
        }
    }

    private function rowCount(string $html): int
    {
        return substr_count($html, '<th scope="row">');
    }

    public function test_index_shows_default_15_per_page_and_pagination_links(): void
    {
        $this->createRepayments(35);

        $response = $this->actingAs($this->createUser())->get('/repayments');

        $response->assertOk();
        $this->assertSame(15, $this->rowCount($response->getContent()));
        $response->assertSee('Всего: 35');
        $response->assertSee('Страница 1 из 3');
        $response->assertSee('page=2');
        // bootstrap-5 pagination view is used, no unstyled tailwind SVG arrows
        $this->assertTrue(str_contains($response->getContent(), 'page-link'));
        $this->assertFalse(str_contains($response->getContent(), '<svg'));
    }

    public function test_index_second_page_shows_next_rows(): void
    {
        $this->createRepayments(35);

        $response = $this->actingAs($this->createUser())->get('/repayments?page=2');

        $response->assertOk();
        $this->assertSame(15, $this->rowCount($response->getContent()));
        $response->assertSee('Страница 2 из 3');
    }

    public function test_index_supports_custom_per_page(): void
    {
        $this->createRepayments(35);

        $response = $this->actingAs($this->createUser())->get('/repayments?per_page=30');

        $response->assertOk();
        $this->assertSame(30, $this->rowCount($response->getContent()));
        $response->assertSee('Страница 1 из 2');
        // per_page is preserved in pagination links
        $response->assertSee('per_page=30');
    }

    public function test_index_clamps_per_page_to_100(): void
    {
        $this->createRepayments(35);

        $response = $this->actingAs($this->createUser())->get('/repayments?per_page=200');

        $response->assertOk();
        // clamped to 100 > 35 rows, so all 35 fit on one page
        $this->assertSame(35, $this->rowCount($response->getContent()));
    }

    public function test_guest_cannot_access_repayments_index(): void
    {
        $this->createRepayments(5);

        $response = $this->get('/repayments');

        $response->assertRedirect('/login');
    }
}
