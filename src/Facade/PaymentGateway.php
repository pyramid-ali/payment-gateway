<?php

namespace Alish\PaymentGateway\Facade;

use Alish\PaymentGateway\Data\RequestPaymentData;
use Alish\PaymentGateway\Fakes\PaymentGatewayFake;
use Alish\PaymentGateway\Responses\RequestPaymentResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static request(RequestPaymentData $data, RequestPaymentResponse $response)
 * @method static payload($payload)
 */
class PaymentGateway extends Facade
{
    public static function fake(?string $driver = null)
    {
        static::swap($fake = new PaymentGatewayFake($driver ?? config('payment-gateway.default')));

        return $fake;
    }

    protected static function getFacadeAccessor()
    {
        return \Alish\PaymentGateway\PaymentGatewayManager::class;
    }
}
