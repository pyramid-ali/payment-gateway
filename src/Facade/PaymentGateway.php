<?php

namespace Alish\PaymentGateway\Facade;

use Alish\PaymentGateway\Drivers\Sandbox;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed request($data, $response = null)
 * @method static mixed verify($data, $response = null)
 * @method static string getDefaultDriver()
 */
class PaymentGateway extends Facade
{

    public static function simulate(?string $driver = null, array $responses = [])
    {
        static::swap($sandbox = new Sandbox([
            'simulate' => $driver,
            'responses' => $responses
        ]));

        return $sandbox;
    }

    protected static function getFacadeAccessor()
    {
        return 'payment-gateway';
    }
}
