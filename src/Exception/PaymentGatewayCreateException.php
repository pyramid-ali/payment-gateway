<?php

namespace Alish\PaymentGateway\Exception;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class PaymentGatewayCreateException extends RequestException
{
    public function __construct(Response $response)
    {
        parent::__construct($response);
    }
}
