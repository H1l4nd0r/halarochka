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
    public function boot(): void{
        Model::preventLazyLoading();
        //
        Gate::define('admin',function($user){
            $roles = $this->getUserRoles($user);
            return in_array('admin', $roles);
        });

        Gate::define('create',function($user){

            $roles = $this->getUserRoles($user);

            // check role and record was created by this user or user can create or he is admin
            if (in_array('admin', $roles) || in_array('creator', $roles)) {
                return true;
            }

            return false;
        });

        Gate::define('edit',function($user, $record = null){

            $roles = $this->getUserRoles($user);

            // check role and record was created by this user or user can create or he is admin
            if (in_array('admin', $roles) || in_array('editor', $roles)&&$record !== null && $record->user_id === $user->id) {
                return true;
            }

            return false;
        });

        Gate::define('editApps',function($user, $record = null){

            $roles = $this->getUserRoles($user);

            // check role and record was created by this user or user can create or he is admin
            if (in_array('admin', $roles) || in_array('editor', $roles)) {
                return true;
            }

            return false;
        });

        Gate::define('delete',function($user, $record = null){

            $roles = $this->getUserRoles($user);

            // check role and record was created by this user or user can create or he is admin
            if (in_array('admin', $roles) || in_array('deleter', $roles)&&$record !== null && $record->user_id === $user->id) {
                return true;
            }

            return false;
        });

    }

    protected function getUserRoles($user){
        if (!session()->has('roles')) {
            $rolesData = $user->roles()->pluck('name')->toArray();
            session()->put('roles', $rolesData);
        }

        return session()->get('roles', []);
    }
}
