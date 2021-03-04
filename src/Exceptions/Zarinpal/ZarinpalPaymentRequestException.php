<?php


namespace Alish\PaymentGateway\Exceptions\Zarinpal;


use Alish\PaymentGateway\Exceptions\PaymentRequestException;
use Throwable;

class ZarinpalPaymentRequestException extends PaymentRequestException
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