<?php


namespace Alish\PaymentGateway\Tests\Zarinpal;


use Alish\PaymentGateway\Data\Zarinpal\ZarinpalVerifyPaymentData;
use Alish\PaymentGateway\Data\ZarinpalData;

class VerifyPaymentData implements ZarinpalData
{

    public function zarinpal()
    {
        return (new ZarinpalVerifyPaymentData())
            ->amount(1000)
            ->authority('000001');
    }
}