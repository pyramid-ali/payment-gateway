<?php

namespace Alish\PaymentGateway\Drivers;


use Alish\PaymentGateway\Exception\ConfigNotFoundException;
use Alish\PaymentGateway\Exception\PayloadNotFoundException;
use Alish\PaymentGateway\Exception\PaymentGatewayCreateException;
use Alish\PaymentGateway\Exception\PaymentVerifyException;
use Alish\PaymentGateway\PaymentGateway;
use Alish\PaymentGateway\PaymentLink;
use Alish\PaymentGateway\SuccessfulPayment;
use Alish\PaymentGateway\Utils\HasConfig;
use Alish\PaymentGateway\Utils\ZarinpalErrorCodes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Zarinpal extends PaymentGateway
{
    use HasConfig;

    protected string $url = 'zarinpal.com/pg';

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param  int  $amount
     * @return PaymentLink
     * @throws ConfigNotFoundException
     * @throws PayloadNotFoundException
     * @throws PaymentGatewayCreateException
     */
    public function create(int $amount): PaymentLink
    {
        $response = Http::post(
            $this->endpoint('rest/WebGate/PaymentRequest.json'),
            $this->paymentJsonBody($amount)
        );

        if ($response->successful() && $response['Status'] === 100) {
            $authority = $response['Authority'];

            return PaymentLink::build($this->gateway(), $authority, $this->redirectUrl($authority));
        }

        throw new PaymentGatewayCreateException(ZarinpalErrorCodes::message($this->errorCode($response)), $this->errorCode($response));
    }

    /**
     * @param  int  $amount
     * @return array
     * @throws PayloadNotFoundException
     * @throws ConfigNotFoundException
     */
    protected function paymentJsonBody(int $amount): array
    {
        return array_filter([
            'MerchantID' => $this->merchantId(),
            'Amount' => $amount,
            'CallbackURL' => $this->callback(),
            'Description' => $this->getPayload('description'),
            'Mobile' => $this->getPayload('mobile'),
            'Email' => $this->getPayload('email'),
        ]);
    }

    public function verify(): SuccessfulPayment
    {
        $body = [
            'MerchantID' => $this->merchantId(),
            'Amount' => $this->getPayload('amount'),
            'Authority' => $this->getPayload('authority'),
        ];

        $response = Http::post($this->endpoint('rest/WebGate/PaymentVerification.json'), $body);

        if ($response->successful() && $response['Status'] === 100) {
            return SuccessfulPayment::make($response['RefID']);
        }

        throw new PaymentVerifyException(ZarinpalErrorCodes::message($this->errorCode($response)), $this->errorCode($response));
    }

    protected function errorCode($response): ?int
    {
        return isset($response['Status']) ? $response['Status'] : null;
    }

    protected function callback(): string
    {
        return URL::to($this->getConfig('callback'));
    }

    protected function merchantId(): string
    {
        return $this->getConfig('merchant_id');
    }

    protected function sandbox(): bool
    {
        return $this->getConfig('sandbox', false, false);
    }

    protected function zaringate(): ?string
    {
        return $this->getConfig('zaringate', null, false);
    }

    protected function endpoint(string $url): string
    {
        $prefix = $this->sandbox() ? 'https://sandbox.' : 'https://www.';

        return $prefix.$this->url.Str::start($url, '/');
    }

    public function redirectUrl(string $authority): string
    {
        $suffix = '';

        if ($this->zaringate()) {
            $suffix = Str::start($this->zaringate(), '/');
        }

        return $this->endpoint('StartPay/'.$authority).$suffix;
    }

    public function gateway(): string
    {
        return 'zarinpal';
    }
}
