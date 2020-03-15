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
     * @param  string  $description
     * @return PaymentLink
     * @throws PaymentGatewayCreateException
     */
    public function create(int $amount, string $description): PaymentLink;

    /**
     * @param  int  $amount
     * @param  string  $authority
     * @return SuccessfulPayment
     * @throws PaymentVerifyException
     */
    public function verify(int $amount, string $authority): SuccessfulPayment;


    public function gateway(): string;
}