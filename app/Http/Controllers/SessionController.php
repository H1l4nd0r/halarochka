<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function create(){
        return view('auth.login');
    }
    
    public function store(){
        $atts = request()->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if(!Auth::validate($atts)){
            throw ValidationException::withMessages([
                'email' => 'Неверный логин или пароль'
            ]);
        }

        $user = Auth::getLastAttempted();

        // Если включён Google Authenticator — не логируем, запрашиваем код
        if($user->two_factor_secret){
            request()->session()->regenerateToken(); // Новый CSRF-токен до полного входа
            session()->put('2fa_login_user_id', $user->id);
            session()->save();

            return redirect()->route('login.two-factor');
        }

        Auth::login($user, remember: true);

        // Полная регенерация сессии
        request()->session()->invalidate();  // Удаляем старую сессию
        request()->session()->regenerateToken(); // Новый CSRF-токен

        // Привязываем сессию к пользователю
        session()->put('user_session_bind', $user->id);
        session()->save(); // Явное сохранение

        return redirect('/dashboard');
    }

    public function destroy(){
        Auth::logout();
        return redirect('/');
    }
}
