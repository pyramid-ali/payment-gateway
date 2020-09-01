<?php

namespace Alish\PaymentGateway;

class SuccessfulPayment
{
    /**
     * @var string
     */
    protected $refId;

    public function __construct(?string $refId = null)
    {
        $this->refId = $refId;
    }

    public static function make(?string $refId = null): self
    {
        return new self($refId);
    }

    public function getRefId(): string
    {
        return $this->refId;
    }
}
