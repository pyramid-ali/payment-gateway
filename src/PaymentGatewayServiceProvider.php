<?php

namespace Alish\PaymentGateway;

use Illuminate\Support\ServiceProvider;

class PaymentGatewayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/payment-gateway.php' => config_path('payment-gateway.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/payment-gateway.php', 'payment-gateway'
        );

        $this->app->bind('payment-gateway', function ($app) {
            return new PaymentGatewayManager($app);
        });
    }

    public function provides()
    {
        return [
            'payment-gateway',
        ];
    }
}
