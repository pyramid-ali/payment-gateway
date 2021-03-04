<?php


namespace Alish\PaymentGateway\Exceptions;


use Throwable;

class ConfigNotFoundException extends \Exception
{

    public function __construct($key)
    {
        parent::__construct("config with $key not found");
    }

}