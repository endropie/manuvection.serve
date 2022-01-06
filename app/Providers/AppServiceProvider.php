<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app('validator')->extend('phone', function ($attribute, $value, $parameters) {
            $value = trim($value);
            if ($value == '') return true;
            $match = '/^([0-9\s\-\+\(\)]*)$/';
            if (preg_match($match, $value)) return true;
            else {
                return false;
            }
        }, 'The :attribute is invalid phone number');

    }
}
