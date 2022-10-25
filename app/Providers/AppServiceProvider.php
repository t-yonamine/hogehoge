<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Gate::define('admin', function () {
            return !Auth::user()?->school_id;
        });

        \Gate::define('staff', function () {
            return Auth::user()?->school_id;
        });
    }
}
