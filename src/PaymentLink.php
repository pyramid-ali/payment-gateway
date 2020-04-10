<?php

namespace Alish\PaymentGateway;

class PaymentLink
{
    /**
     * @var string
     */
    protected $authority;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var string
     */
    protected $gateway;

    public function __construct(string $gateway, string $authority, string $link)
    {
        $this->gateway = $gateway;
        $this->authority = $authority;
        $this->link = $link;
    }

    public static function build(string $gateway, string $authority, string $link): self
    {
        return new self($gateway, $authority, $link);
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getAuthority(): string
    {
        return $this->authority;
    }
}
