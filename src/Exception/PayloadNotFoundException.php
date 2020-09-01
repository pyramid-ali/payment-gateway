<?php


namespace Alish\PaymentGateway\Exception;


class PayloadNotFoundException extends \Exception
{

    public function __construct($key)
    {
        parent::__construct("config with $key not found");
    }

}