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

            //print_r($user->roles()->get()->toArray());
            if(!session()->has('roles')){
                //$adminRole = Role::where('name','=','admin')->first();
                //$user->roles()->where('role_id', $adminRole->id )->exists() ;
                // TODO combine with registration
                $rolesData = $user->roles()->get()->toArray();
                $roles = array_column($rolesData, 'name');
                session()->put('roles', $roles);
                session()->save();
            }

            //print_r(session()->get('roles'));
            //echo 'CHECK:',\array_search('admin',session()->get('roles')??[])!==false;
            return \array_search('admin',session()->get('roles')??[])!==false;
        });

    }
}
