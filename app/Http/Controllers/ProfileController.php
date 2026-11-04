<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct(private TwoFactorService $twoFactor)
    {
    }

    public function index()
    {
        $user = Auth::user();

        $qrSvg = null;

        // Идёт подключение Google Authenticator — показываем QR-код
        if (session('two_factor_secret')) {
            $otpauth = $this->twoFactor->qrCodeUrl(session('two_factor_secret'), $user->email);
            $qrSvg = $this->twoFactor->qrCodeSvg($otpauth);
        }

        return view('profile.index', [
            'user' => $user,
            'qrSvg' => $qrSvg,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Текущий пароль указан неверно',
            'password.confirmed' => 'Подтверждение пароля не совпадает',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
        ]);

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Пароль успешно изменён');
    }
}