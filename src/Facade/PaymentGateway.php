<?php

namespace Alish\PaymentGateway\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static request($data, $response = null)
 * @method static verify($data, $response = null)
 */
class PaymentGateway extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'payment-gateway';
    }
}
