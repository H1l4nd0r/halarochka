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
            //print_r(session()->get('roles'));

            //$adminRole = Role::where('name','=','admin')->first();
            //return $user->roles()->where('role_id', $adminRole->id )->exists() ;
            return \array_search('admin',session()->get('roles')??[])!==false;
        });

    }
}
