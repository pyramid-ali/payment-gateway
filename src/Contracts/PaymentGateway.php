<?php

namespace Alish\PaymentGateway\Contracts;

use Alish\PaymentGateway\Exception\PaymentGatewayCreateException;
use Alish\PaymentGateway\Exception\PaymentVerifyException;
use Alish\PaymentGateway\PaymentLink;
use Alish\PaymentGateway\SuccessfulPayment;

interface PaymentGateway
{
    /**
     * @param  int  $amount
     * @return PaymentLink
     * @throws PaymentGatewayCreateException
     */
    public function create(int $amount): PaymentLink;

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
