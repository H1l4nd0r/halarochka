<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function __construct(private TwoFactorService $twoFactor)
    {
    }

    /**
     * Подключение Google Authenticator: генерация секрета, хранение в сессии.
     */
    public function start()
    {
        $secret = $this->twoFactor->generate();

        session()->put('two_factor_secret', $secret);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Отсканируйте QR-код в Google Authenticator и введите код из приложения');
    }

    /**
     * Подтверждение кода из приложения: сохранение секрета в БД.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Введите код из Google Authenticator',
            'code.digits' => 'Код должен содержать 6 цифр',
        ]);

        $secret = session('two_factor_secret');

        if (! $secret) {
            return redirect()->route('profile.index');
        }

        if (! $this->twoFactor->verify($request->input('code'), $secret)) {
            throw ValidationException::withMessages([
                'code' => 'Неверный код',
            ]);
        }

        Auth::user()->update([
            'two_factor_secret' => $secret,
        ]);

        session()->forget('two_factor_secret');

        return redirect()
            ->route('profile.index')
            ->with('success', 'Google Authenticator подключён. Теперь при входе в систему будет запрашиваться код');
    }

    /**
     * Отмена подключения Google Authenticator.
     */
    public function cancel()
    {
        session()->forget('two_factor_secret');

        return redirect()->route('profile.index');
    }

    /**
     * Отключение Google Authenticator по текущему коду.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Введите код из Google Authenticator',
            'code.digits' => 'Код должен содержать 6 цифр',
        ]);

        $user = Auth::user();

        if (! $user->two_factor_secret) {
            return redirect()->route('profile.index');
        }

        if (! $this->twoFactor->verify($request->input('code'), $user->two_factor_secret)) {
            throw ValidationException::withMessages([
                'code' => 'Неверный код',
            ]);
        }

        $user->update(['two_factor_secret' => null]);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Google Authenticator отключён');
    }

    /**
     * Экран ввода кода Google Authenticator при входе в систему.
     */
    public function login()
    {
        $user = $this->pendingLoginUser();

        if (! $user) {
            return redirect()->route('login');
        }

        return view('auth.two-factor', [
            'user' => $user,
        ]);
    }

    /**
     * Проверка кода Google Authenticator при входе в систему.
     */
    public function loginVerify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Введите код из Google Authenticator',
            'code.digits' => 'Код должен содержать 6 цифр',
        ]);

        $user = $this->pendingLoginUser();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->twoFactor->verify($request->input('code'), $user->two_factor_secret)) {
            throw ValidationException::withMessages([
                'code' => 'Неверный код',
            ]);
        }

        session()->forget('2fa_login_user_id');

        Auth::login($user, remember: true);

        // Полная регенерация сессии
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Привязка сессии к пользователю
        session()->put('user_session_bind', $user->id);
        $request->session()->save();

        return redirect('/dashboard');
    }

    /**
     * Пользователь, прошедший парольный шаг входа и ожидающий код 2FA.
     */
    protected function pendingLoginUser(): ?User
    {
        $userId = session('2fa_login_user_id');

        if (! $userId) {
            return null;
        }

        $user = User::find($userId);

        if (! $user || ! $user->two_factor_secret) {
            session()->forget('2fa_login_user_id');

            return null;
        }

        return $user;
    }
}
