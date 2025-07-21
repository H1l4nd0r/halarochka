<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Role;

class RegisteredUserController extends Controller
{
    public function create(){
        return view('auth.register');
    }

    public function store(){
        
        $atts = request()->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required', Password::min(8), 'confirmed']
        ]);

        $user = User::create($atts);

        $uc = User::all()->count();

        if($uc==1){
            // make user admin
            $adminRole = Role::where('name','=','admin')->first();
            $user->roles()->attach($adminRole,[
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Auth::login($user);
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        // Привязка сессии к пользователю
        session()->put('user_session_bind', $user->id);
        session()->save();

        return redirect('/dashboard');
    }
}
