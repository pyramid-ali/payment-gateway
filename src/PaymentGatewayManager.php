<?php

namespace Alish\PaymentGateway;

use Alish\PaymentGateway\Drivers\Parsian;
use Alish\PaymentGateway\Drivers\Zarinpal;
use Illuminate\Support\Manager;

class PaymentGatewayManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config['payment-gateway']['default'];
    }

    public function createZarinpalDriver()
    {
        return new Zarinpal($this->config()['zarinpal']);
    }

    public function createParsianDriver()
    {
        return new Parsian($this->config()['parsian']);
    }

    public function config(): array
    {
        return $this->config['payment-gateway'];
    }

    public function __call($method, $parameters)
    {
        $data = $parameters[0];
        $parameter = $data->{$this->driver()}();

        return $this->driver()->$method($parameter);
    }
}
