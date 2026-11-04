<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', [
            'user' => Auth::user(),
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