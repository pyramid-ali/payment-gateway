<?php


namespace Alish\PaymentGateway\Exceptions\Zarinpal;


use Alish\PaymentGateway\Exceptions\PaymentException;
use Throwable;

class ZarinpalException extends PaymentException
{

    public array $validations = [];

    public function validations(array $validations)
    {
        $this->validations = $validations;
        return $this;
    }

    public function context()
    {
        return $this->validations;
    }
}