<?php

namespace App\Providers;

use App\Services\LastFMService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableServiceProvider;

class LastFMServiceProvider extends ServiceProvider implements DeferrableServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LastFMService::class, function () {
            return new LastFMService();
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

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [LastFMService::class];
    }
}
