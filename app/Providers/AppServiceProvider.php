<?php

namespace App\Providers;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading();
        //
        Gate::define('admin',function($user){

            if(!session()->has('roles')){
                $rolesData = $user->roles()->get()->toArray();
                $roles = array_column($rolesData, 'name');
                session()->put('roles', $roles);
                session()->save();
            }

            return \array_search('admin',session()->get('roles')??[])!==false;
        });

    }
}
