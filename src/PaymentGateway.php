<?php


namespace Alish\PaymentGateway;

use Alish\PaymentGateway\Contracts\PaymentGateway as Gateway;
use Alish\PaymentGateway\Exceptions\PayloadDriverNotFoundException;
use Alish\PaymentGateway\Exceptions\PayloadNotFoundException;

abstract class PaymentGateway implements Gateway
{
    protected array $payload = [];

    /**
     * @param  array|object  $payload
     * @return $this
     * @throws PayloadDriverNotFoundException
     */
    public function payload($payload): self
    {
        switch (gettype($payload)) {
            case 'array':
                $this->payload = $payload;
                break;
            case 'object':
                $this->payload = $this->payloadDriver($payload);
                break;
            default:
                throw new \InvalidArgumentException('Parameter passed as payload should be array or object');
        }
        return $this;
    }

    /**
     * @param  object  $payload
     * @return array
     * @throws PayloadDriverNotFoundException
     */
    protected function payloadDriver(object $payload): array
    {
        if (!method_exists($payload, $this->gateway())) {
            throw new PayloadDriverNotFoundException($this->gateway());
        }

        $returnValue = $payload->{$this->gateway()}();

        if (gettype($returnValue) !== 'array') {
            throw new \InvalidArgumentException('invalid return type');
        }

        return $payload->{$this->gateway()}();
    }

    /**
     * @param  string  $key
     * @param  bool  $strict
     * @return mixed|null
     * @throws PayloadNotFoundException
     */
    public function getPayload(string $key, $strict = false)
    {
        if (isset($this->payload[$key])) {
            return $this->payload[$key];
        }

        if ($strict) {
            throw new PayloadNotFoundException($key);
        }

        return null;
    }

}