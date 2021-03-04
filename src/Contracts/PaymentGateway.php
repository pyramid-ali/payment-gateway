<?php

namespace Alish\PaymentGateway\Contracts;

use Alish\PaymentGateway\Data\RequestPaymentData;
use Alish\PaymentGateway\Exceptions\PaymentGatewayCreateException;
use Alish\PaymentGateway\Exceptions\PaymentVerifyException;
use Alish\PaymentGateway\PaymentLink;
use Alish\PaymentGateway\SuccessfulPayment;

interface PaymentGateway
{

    public function request(RequestPaymentData $data);

    /**
     * @param array|object $payload
     * @return self
     */
    public function payload($payload): self ;

    /**
     * @return SuccessfulPayment
     * @throws PaymentVerifyException
     */
    public function verify(): SuccessfulPayment;

    public function gateway(): string;
}
