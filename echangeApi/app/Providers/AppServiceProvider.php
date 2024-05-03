<?php

namespace App\Providers;

use CloudCreativity\LaravelJsonApi\LaravelJsonApi;
use Illuminate\Support\Facades\Schema;
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

        Schema::defaultStringLength(191);
        LaravelJsonApi::defaultApi('v1');
        LaravelJsonApi::$validationFailures = true;
    }
}
