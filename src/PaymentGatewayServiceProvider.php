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
        $this->publishes([
            __DIR__.'/../config/airlock.php' => config_path('airlock.php'),
        ], 'airlock-config');

        $this->app->bind(PaymentGateway::class, function ($app) {
            return new PaymentGateway($app);
        });
    }

    public function provides()
    {
        return [
            PaymentGateway::class
        ];
    }

}