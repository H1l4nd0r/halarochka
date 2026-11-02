<?php

namespace Tests\Feature;

use App\Models\Cashfund;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class CashfundIndexPaginationTest extends TestCase
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

    private function createFunds(int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            Cashfund::create([
                'summ' => 500000,
                'factday' => '2025-0' . min($i, 9) . '-15',
                'type' => Cashfund::CASHFUND_INVESTMENT, // default filter on /cash
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
        $this->createFunds(35);

        $response = $this->actingAs($this->createUser())->get('/cash');

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
        $this->createFunds(35);

        $response = $this->actingAs($this->createUser())->get('/cash?page=2');

        $response->assertOk();
        $this->assertSame(15, $this->rowCount($response->getContent()));
        $response->assertSee('Страница 2 из 3');
    }

    public function test_index_supports_custom_per_page(): void
    {
        $this->createFunds(35);

        $response = $this->actingAs($this->createUser())->get('/cash?per_page=30');

        $response->assertOk();
        $this->assertSame(30, $this->rowCount($response->getContent()));
        $response->assertSee('Страница 1 из 2');
        // per_page is preserved in pagination links
        $response->assertSee('per_page=30');
    }

    public function test_index_clamps_per_page_to_100(): void
    {
        $this->createFunds(35);

        $response = $this->actingAs($this->createUser())->get('/cash?per_page=200');

        $response->assertOk();
        // clamped to 100 > 35 rows, so all 35 fit on one page
        $this->assertSame(35, $this->rowCount($response->getContent()));
    }

    public function test_per_page_from_cookie(): void
    {
        $this->createFunds(35);

        $response = $this->actingAs($this->createUser())
            ->withCookie('per_page', '30')
            ->get('/cash');

        $response->assertOk();
        $this->assertSame(30, $this->rowCount($response->getContent()));
    }

    public function test_url_per_page_overrides_cookie(): void
    {
        $this->createFunds(35);

        $response = $this->actingAs($this->createUser())
            ->withCookie('per_page', '30')
            ->get('/cash?per_page=15');

        $response->assertOk();
        $this->assertSame(15, $this->rowCount($response->getContent()));
    }

    public function test_cookie_per_page_is_clamped(): void
    {
        $this->createFunds(35);

        $response = $this->actingAs($this->createUser())
            ->withCookie('per_page', '500')
            ->get('/cash');

        $response->assertOk();
        // clamped to 100 > 35 rows, so all 35 fit on one page
        $this->assertSame(35, $this->rowCount($response->getContent()));
    }

    public function test_guest_cannot_access_cash_index(): void
    {
        $this->createFunds(5);

        $response = $this->get('/cash');

        $response->assertRedirect('/login');
    }
}
