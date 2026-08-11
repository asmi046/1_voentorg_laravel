<?php

namespace App\Providers;

use App\Services\CdekDeliveryGateway;
use App\Services\CdekService;
use App\Services\DeliveryCoordinator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DeliveryCoordinator::class, function ($app) {
            return new DeliveryCoordinator([
                new CdekDeliveryGateway($app->make(CdekService::class)),
            ]);
        });

        $this->app->singleton(CdekDeliveryGateway::class, function ($app) {
            return new CdekDeliveryGateway($app->make(CdekService::class));
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
