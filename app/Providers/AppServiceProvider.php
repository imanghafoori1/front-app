<?php

namespace App\Providers;

use App\Decorators\ExchangeRateServiceDecorator;
use App\Services\ExchangeRateService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->singleton(ExchangeRateService::class, function () {
            return new ExchangeRateServiceDecorator(new ExchangeRateService, 60);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
