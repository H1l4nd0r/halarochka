<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class DealsIndexPaginationTest extends TestCase
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

    private function createDeals(int $count): void
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

        for ($i = 1; $i <= $count; $i++) {
            Deal::create([
                'client_id' => $client->id,
                'dealdate' => '2025-01-01',
                'goodname' => 'Товар',
                'startprice' => 1000000,
                'firstpayment' => 100000,
                'fee' => 10000,
                'term' => 12,
                'fullprice' => 1200000,
                // DEAL_CLOSED: bypasses the cash-fund check and reschedule() hooks
                'status' => Deal::DEAL_CLOSED,
                'idempotency_key' => (string) Str::uuid(),
            ]);
        }
    }

    // the deals table has one extra "Итого" row with <th scope="row">
    private function rowCount(string $html): int
    {
        return substr_count($html, '<th scope="row">') - 1;
    }

    public function test_index_shows_default_15_per_page_and_pagination_links(): void
    {
        $this->createDeals(35);

        $response = $this->actingAs($this->createUser())->get('/deals?status=2');

        $response->assertOk();
        $this->assertSame(15, $this->rowCount($response->getContent()));
        $response->assertSee('Всего: 35');
        $response->assertSee('Страница 1 из 3');
        $response->assertSee('page=2');
        // bootstrap-5 pagination view is used, no unstyled tailwind SVG arrows
        $this->assertTrue(str_contains($response->getContent(), 'page-link'));
        $this->assertFalse(str_contains($response->getContent(), '<svg'));
    }

    public function test_totals_row_covers_the_whole_filtered_set(): void
    {
        $this->createDeals(35);

        $response = $this->actingAs($this->createUser())->get('/deals?status=2');

        $response->assertOk();
        // 35 шт in the "Итого" row (whole filtered set, not the current page)
        $response->assertSee('35 шт');
        // sums over all 35 deals: 35 x 1 000 000 / 100 000 / 10 000
        $response->assertSee('35 000 000');
        $response->assertSee('3 500 000');
        $response->assertSee('350 000');
    }

    public function test_index_second_page_shows_next_rows(): void
    {
        $this->createDeals(35);

        $response = $this->actingAs($this->createUser())->get('/deals?status=2&page=2');

        $response->assertOk();
        $this->assertSame(15, $this->rowCount($response->getContent()));
        $response->assertSee('Страница 2 из 3');
        // status filter is preserved in pagination links
        $response->assertSee('status=2');
    }

    public function test_index_supports_custom_per_page(): void
    {
        $this->createDeals(35);

        $response = $this->actingAs($this->createUser())->get('/deals?status=2&per_page=30');

        $response->assertOk();
        $this->assertSame(30, $this->rowCount($response->getContent()));
        $response->assertSee('Страница 1 из 2');
        // per_page and status are preserved in pagination links
        $response->assertSee('per_page=30');
        $response->assertSee('status=2');
        // totals still cover the whole set
        $response->assertSee('35 000 000');
    }

    public function test_index_clamps_per_page_to_100(): void
    {
        $this->createDeals(35);

        $response = $this->actingAs($this->createUser())->get('/deals?status=2&per_page=200');

        $response->assertOk();
        // clamped to 100 > 35 rows, so all 35 fit on one page
        $this->assertSame(35, $this->rowCount($response->getContent()));
    }

    public function test_guest_cannot_access_deals_index(): void
    {
        $this->createDeals(5);

        $response = $this->get('/deals');

        $response->assertRedirect('/login');
    }
}
