<?php

namespace App\Providers;

use App\Services\ReccService;

use Illuminate\Support\ServiceProvider;

class ReccServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ReccService::class, function ($app) {
            return new ReccService($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function provides()
    {
        return [ReccService::class];
    }
}
