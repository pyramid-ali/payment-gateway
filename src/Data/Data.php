<?php


namespace Alish\PaymentGateway\Data;


use Alish\PaymentGateway\Data\Zarinpal\ZarinpalData;

interface Data
{

    public function zarinpal(): ZarinpalData;

}