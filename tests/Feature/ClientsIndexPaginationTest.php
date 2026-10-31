<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClientsIndexPaginationTest extends TestCase
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

    private function createClients(int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            Client::create([
                'first_name' => 'Иван',
                'middle_name' => 'Иванович',
                'last_name' => 'Фамилия' . $i,
                'phone' => '+70000000000',
                'borndate' => '1990-01-01',
                'iddoc' => 'паспорт',
                'idnum' => '123456',
                'idempotency_key' => (string) Str::uuid(),
            ]);
        }
    }

    private function rowCount(string $html): int
    {
        return substr_count($html, '<td scope="row">');
    }

    public function test_index_shows_default_15_per_page_and_pagination_links(): void
    {
        $this->createClients(35);

        $response = $this->actingAs($this->createUser())->get('/clients');

        $response->assertOk();
        $this->assertSame(15, $this->rowCount($response->getContent()));
        $response->assertSee('Всего: 35');
        $response->assertSee('Страница 1 из 3');
        $response->assertSee('page=2');
    }

    public function test_index_second_page_shows_next_rows(): void
    {
        $this->createClients(35);

        $response = $this->actingAs($this->createUser())->get('/clients?page=2');

        $response->assertOk();
        $this->assertSame(15, $this->rowCount($response->getContent()));
        $response->assertSee('Страница 2 из 3');
    }

    public function test_index_supports_custom_per_page(): void
    {
        $this->createClients(35);

        $response = $this->actingAs($this->createUser())->get('/clients?per_page=30');

        $response->assertOk();
        $this->assertSame(30, $this->rowCount($response->getContent()));
        $response->assertSee('Страница 1 из 2');
        // per_page is preserved in pagination links
        $response->assertSee('per_page=30');
    }

    public function test_index_clamps_per_page_to_100(): void
    {
        $this->createClients(35);

        $response = $this->actingAs($this->createUser())->get('/clients?per_page=200');

        $response->assertOk();
        // clamped to 100 > 35 rows, so all 35 fit on one page
        $this->assertSame(35, $this->rowCount($response->getContent()));
    }

    public function test_guest_cannot_access_clients_index(): void
    {
        $this->createClients(5);

        $response = $this->get('/clients');

        $response->assertRedirect('/login');
    }
}
