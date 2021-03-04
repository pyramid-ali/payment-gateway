<?php


namespace Alish\PaymentGateway\Data\Zarinpal;


use Illuminate\Support\Arr;

class ZarinpalVerifyPaymentData
{
    protected ?string $merchantId = null;

    protected string $authority;

    protected int $amount;

    public function __construct(?string $merchantId = null)
    {
        $this->merchantId = $merchantId;
    }

    public function authority(string $authority): self
    {
        $this->authority = $authority;
        return $this;
    }

    public function amount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function json(array $prerequisites)
    {
        return array_filter([
            'merchant_id' => $this->merchantId ?? Arr::get($prerequisites, 'merchant_id'),
            'amount' => $this->amount,
            'authority' => $this->authority
        ]);
    }
}