<?php

namespace App\Providers;

use App\Models\Role;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        // render pagination with Bootstrap 5 markup (app is Bootstrap-based, no Tailwind CSS)
        Paginator::useBootstrapFive();

        // Защита от брутфорса кода 2FA при входе: 5 попыток в минуту
        RateLimiter::for('login-2fa', function (Request $request) {
            $key = 'login-2fa:' . ($request->session()->get('2fa_login_user_id') ?? $request->ip());

            return Limit::perMinute(5)->by($key);
        });
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
