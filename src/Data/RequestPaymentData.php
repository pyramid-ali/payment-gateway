<?php


namespace Alish\PaymentGateway\Data;

use Alish\PaymentGateway\Data\Zarinpal\ZarinpalRequestPaymentData;

interface RequestPaymentData extends Data
{
    public function zarinpal(): ZarinpalRequestPaymentData;
}