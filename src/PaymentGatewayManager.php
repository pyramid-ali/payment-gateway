<?php

namespace Alish\PaymentGateway;

use Alish\PaymentGateway\Drivers\Zarinpal;
use Illuminate\Support\Manager;

class PaymentGatewayManager extends Manager
{

    public function getDefaultDriver()
    {
        return $this->config['payment-gateway']['default'];
    }

    public function createZarinpalDriver()
    {
        return new Zarinpal($this->config()['zarinpal']);
    }

    public function config(): array
    {
        return $this->config['payment-gateway'];
    }
}
