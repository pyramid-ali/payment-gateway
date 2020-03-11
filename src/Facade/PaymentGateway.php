<?php


namespace Alish\PaymentGateway\Facade;


use Illuminate\Support\Facades\Facade;

class PaymentGateway extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'PaymentGateway';
    }


}