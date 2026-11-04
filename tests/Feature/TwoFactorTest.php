<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    private function g2fa(): Google2FA
    {
        return new Google2FA();
    }

    private function createUser(array $attrs = []): User
    {
        return User::create(array_merge([
            'name' => 'Тест',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ], $attrs));
    }

    private function loginWithPassword(User $user): void
    {
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
    }

    private function invalidCode(string $secret): string
    {
        $otp = $this->g2fa()->getCurrentOtp($secret);

        return $otp === '000000' ? '111111' : '000000';
    }

    public function test_login_without_two_factor_works_as_before(): void
    {
        $user = $this->createUser();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_two_factor_requires_code_step(): void
    {
        $user = $this->createUser(['two_factor_secret' => $this->g2fa()->generateSecretKey()]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login.two-factor'));
        $this->assertGuest();
        $this->assertSame($user->id, session()->get('2fa_login_user_id'));
    }

    public function test_login_two_factor_page_redirects_without_pending_state(): void
    {
        $response = $this->get('/login/2fa');

        $response->assertRedirect(route('login'));
    }

    public function test_login_two_factor_rejects_invalid_code(): void
    {
        $g2fa = $this->g2fa();
        $secret = $g2fa->generateSecretKey();
        $user = $this->createUser(['two_factor_secret' => $secret]);

        $this->loginWithPassword($user);

        $response = $this->from('/login/2fa')->post('/login/2fa', ['code' => $this->invalidCode($secret)]);

        $response->assertRedirect('/login/2fa');
        $response->assertSessionHasErrors('code');
        $this->assertGuest();
        // можно повторить попытку
        $this->assertSame($user->id, session()->get('2fa_login_user_id'));
    }

    public function test_login_two_factor_completes_with_valid_code(): void
    {
        $g2fa = $this->g2fa();
        $secret = $g2fa->generateSecretKey();
        $user = $this->createUser(['two_factor_secret' => $secret]);

        $this->loginWithPassword($user);

        $response = $this->post('/login/2fa', ['code' => $g2fa->getCurrentOtp($secret)]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertNull(session()->get('2fa_login_user_id'));
        $this->assertSame($user->id, session()->get('user_session_bind'));
    }

    public function test_login_two_factor_is_rate_limited_after_five_attempts(): void
    {
        $user = $this->createUser(['two_factor_secret' => $this->g2fa()->generateSecretKey()]);

        $this->loginWithPassword($user);

        foreach (range(1, 5) as $i) {
            $this->post('/login/2fa', ['code' => '000000']);
        }

        $this->post('/login/2fa', ['code' => '000000'])->assertStatus(429);
    }

    public function test_profile_shows_start_button_when_two_factor_disabled(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
        $response->assertSee('Подключить Google Authenticator');
    }

    public function test_start_two_factor_stores_secret_in_session(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('profile.two-factor.start'));

        $response->assertRedirect(route('profile.index'));
        $this->assertNotNull(session()->get('two_factor_secret'));
        $this->assertNull($user->fresh()->two_factor_secret);
    }

    public function test_profile_shows_qr_code_during_setup(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->post(route('profile.two-factor.start'));

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
        $response->assertSee('<svg', false);
        $response->assertSee('Код из Google Authenticator');
    }

    public function test_cancel_two_factor_forgets_session_secret(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->post(route('profile.two-factor.start'));
        $this->assertNotNull(session()->get('two_factor_secret'));

        $response = $this->actingAs($user)->post(route('profile.two-factor.cancel'));

        $response->assertRedirect(route('profile.index'));
        $this->assertNull(session()->get('two_factor_secret'));
    }

    public function test_verify_two_factor_rejects_invalid_code_and_keeps_setup(): void
    {
        $g2fa = $this->g2fa();
        $user = $this->createUser();

        $this->actingAs($user)->post(route('profile.two-factor.start'));
        $secret = session()->get('two_factor_secret');

        $response = $this->actingAs($user)->post(route('profile.two-factor.verify'), [
            'code' => $this->invalidCode($secret),
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertNull($user->fresh()->two_factor_secret);
        // настройка не сброшена, можно повторить
        $this->assertSame($secret, session()->get('two_factor_secret'));
    }

    public function test_verify_two_factor_saves_secret_with_valid_code(): void
    {
        $g2fa = $this->g2fa();
        $user = $this->createUser();

        $this->actingAs($user)->post(route('profile.two-factor.start'));
        $secret = session()->get('two_factor_secret');

        $response = $this->actingAs($user)->post(route('profile.two-factor.verify'), [
            'code' => $g2fa->getCurrentOtp($secret),
        ]);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('success');
        $this->assertSame($secret, $user->fresh()->two_factor_secret);
        $this->assertNull(session()->get('two_factor_secret'));
    }

    public function test_profile_shows_enabled_state_with_disable_form(): void
    {
        $user = $this->createUser(['two_factor_secret' => $this->g2fa()->generateSecretKey()]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
        $response->assertSee('Включено');
        $response->assertSee('Отключить');
    }

    public function test_disable_two_factor_rejects_invalid_code(): void
    {
        $g2fa = $this->g2fa();
        $secret = $g2fa->generateSecretKey();
        $user = $this->createUser(['two_factor_secret' => $secret]);

        $response = $this->actingAs($user)->post(route('profile.two-factor.disable'), [
            'code' => $this->invalidCode($secret),
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertNotNull($user->fresh()->two_factor_secret);
    }

    public function test_disable_two_factor_with_valid_code(): void
    {
        $g2fa = $this->g2fa();
        $secret = $g2fa->generateSecretKey();
        $user = $this->createUser(['two_factor_secret' => $secret]);

        $response = $this->actingAs($user)->post(route('profile.two-factor.disable'), [
            'code' => $g2fa->getCurrentOtp($secret),
        ]);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('success');
        $this->assertNull($user->fresh()->two_factor_secret);
    }

    public function test_two_factor_secret_is_hidden_from_user_serialization(): void
    {
        $user = $this->createUser(['two_factor_secret' => 'TESTSECRET']);

        $this->assertArrayNotHasKey('two_factor_secret', $user->toArray());
    }
}
