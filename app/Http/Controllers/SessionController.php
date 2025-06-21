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

        if(!Auth::attempt($atts, true)){
            throw ValidationException::withMessages([
                'email' => 'Неверный логин или пароль'
            ]);
        }

        request()->session()->regenerate();
        
        return redirect('/dashboard');
    }

    public function destroy(){
        Auth::logout();
        return redirect('/');
    }
}
