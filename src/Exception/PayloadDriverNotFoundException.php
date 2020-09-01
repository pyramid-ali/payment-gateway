<?php


namespace Alish\PaymentGateway\Exception;


use Throwable;

class PayloadDriverNotFoundException extends \Exception
{

    public $gateway;

    public function __construct(string $gateway)
    {
        parent::__construct("payload for {$gateway} not found, try to define method with same driver name [{$gateway}]");
    }

}