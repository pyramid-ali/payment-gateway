<?php


namespace Alish\PaymentGateway\Responses;


interface RequestPaymentResponse
{
    public function zarinpal(ZarinpalRequestPaymentResponse $response);
}