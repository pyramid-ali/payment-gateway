<?php

namespace Alish\PaymentGateway;

use Illuminate\Support\ServiceProvider;

class PaymentGatewayServiceProvider extends ServiceProvider
{

    public function register()
    {
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