<?php


namespace Alish\PaymentGateway\Exception;


use Throwable;

class ConfigNotFoundException extends \Exception
{

    public function __construct($key)
    {
        parent::__construct("config with $key not found");
    }

}