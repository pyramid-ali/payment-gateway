<?php


namespace Alish\PaymentGateway\Data\Zarinpal;

use Illuminate\Support\Arr;

class ZarinpalRequestPaymentData
{
    protected ?string $merchantId = null;

    protected int $amount;

    protected string $description;

    protected ?string $callbackUrl = null;

    protected ?array $metadata = null;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function amount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function merchantId(string $merchantId): self
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    public function callbackUrl(string $callbackUrl): self
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function mobile(string $mobile): self
    {
        $this->metadata['mobile'] = $mobile;
        return $this;
    }

    public function email(string $email): self
    {
        $this->metadata['email'] = $email;
        return $this;
    }

    public function cardPan(string $cardPan): self
    {
        $this->metadata['card_pan'] = $cardPan;
        return $this;
    }

    public function json(array $prerequisites)
    {
        return array_filter([
            'merchant_id' => $this->merchantId ?? Arr::get($prerequisites, 'merchant_id'),
            'amount' => $this->amount,
            'description' => $this->description,
            'callback_url' => $this->callbackUrl ?? Arr::get($prerequisites, 'callback_url'),
            'metadata' => $this->metadata
        ]);
    }
}