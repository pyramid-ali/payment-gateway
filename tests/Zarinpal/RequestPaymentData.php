<?php


namespace Alish\PaymentGateway\Tests\Zarinpal;


use Alish\PaymentGateway\Data\Zarinpal\ZarinpalRequestPaymentData;
use Alish\PaymentGateway\Data\ZarinpalData;

class RequestPaymentData implements ZarinpalData
{


    public function zarinpal(): ZarinpalRequestPaymentData
    {
        return (new ZarinpalRequestPaymentData(1000))
                ->description('test description')
                ->mobile('09123456789')
                ->email('test@test.com');
    }
}