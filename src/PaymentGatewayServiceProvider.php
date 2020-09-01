<?php

namespace Alish\PaymentGateway;

use Illuminate\Support\ServiceProvider;

class PaymentGatewayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/payment-gateway.php' => config_path('payment-gateway.php'),
        ], 'payment-gateway-config');
    }

    public function register()
    {
        $this->app->bind(PaymentGatewayManager::class, function ($app) {
            return new PaymentGatewayManager($app);
        });
    }

    public function provides()
    {
        return [
            PaymentGatewayManager::class,
        ];
    }
}
