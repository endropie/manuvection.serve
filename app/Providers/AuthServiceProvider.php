<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('gate', function ($app) {
            return new Gate($app, function () {
                return auth()->user();
            });
        });
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */

    public function boot()
    {
        foreach (config('auth.gates', []) as $name) {
            app('gate')->define($name, function ($user) use ($name) {
                if (!$user) return false;
                return (bool) collect($user->ability ?? [])->intersect(['*', $name])->count();
            });
        }
    }
}
