<?php


namespace Alish\PaymentGateway\Contracts;


use Alish\PaymentGateway\PaymentLink;
use Alish\PaymentGateway\SuccessfulPayment;

interface PaymentGateway
{
    public function create(int $amount, string $description): PaymentLink;

    public function verify(int $amount, string $authority): SuccessfulPayment;
}