<?php


namespace Alish\PaymentGateway;


use Alish\PaymentGateway\Drivers\Zarinpal;
use Illuminate\Support\Manager;

class PaymentGateway extends Manager
{

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        $this->config['payment-gateway']['default'];
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