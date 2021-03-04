<?php


namespace Alish\PaymentGateway\Exceptions;


class PayloadNotFoundException extends \Exception
{

    public function __construct($key)
    {
        parent::__construct("config with $key not found");
    }

}