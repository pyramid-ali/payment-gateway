<?php

namespace Alish\PaymentGateway\Exception;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class PaymentVerifyException extends RequestException
{
    public function __construct(Response $response)
    {
        parent::__construct($response);
    }
}
