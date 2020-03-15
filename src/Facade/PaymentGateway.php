<?php


namespace Alish\PaymentGateway\Facade;


use Alish\PaymentGateway\Fakes\PaymentGatewayFake;
use Illuminate\Support\Facades\Facade;

class PaymentGateway extends Facade
{

    public static function fake(?string $driver = null)
    {
        static::swap($fake = new PaymentGatewayFake($driver ?? config('payment-gateway.default')));

        return $fake;
    }

    protected static function getFacadeAccessor()
    {
        return \Alish\PaymentGateway\PaymentGateway::class;
    }


}