<?php

namespace Alish\PaymentGateway\Drivers;


use Alish\PaymentGateway\Data\Zarinpal\ZarinpalRequestPaymentData;
use Alish\PaymentGateway\Exceptions\PaymentVerifyException;
use Alish\PaymentGateway\Exceptions\Zarinpal\ZarinpalPaymentRequestException;
use Alish\PaymentGateway\Responses\RequestPaymentResponse;
use Alish\PaymentGateway\Responses\ZarinpalRequestPaymentResponse;
use Alish\PaymentGateway\SuccessfulPayment;
use Alish\PaymentGateway\Utils\HasConfig;
use Alish\PaymentGateway\Utils\ZarinpalErrorCodes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Zarinpal
{
    use HasConfig;

    protected string $baseUrl = 'zarinpal.com/pg/v4/payment/';

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function request(ZarinpalRequestPaymentData $data, RequestPaymentResponse $successfulRequestResponse)
    {
        $response = Http::post(
            $this->requestPayEndpoint(),
            $data->json([
                'merchant_id' => $this->merchantId(),
                'callback_url' => $this->callbackUrl()
            ])
        );

        if ($response->successful() && $response->json('data.code') === 100) {
            $successfulRequestResponse->zarinpal(
                ZarinpalRequestPaymentResponse::fromJson(
                    $response->json('data'),
                    $this->sandbox()
                )
            );
        }

        throw (new ZarinpalPaymentRequestException(
            $response->json('errors.message'),
            $response->json('errors.code')
        ))->validations($response->json('errors.validations'));
    }

    public function verify(): SuccessfulPayment
    {
        $body = [
            'MerchantID' => $this->merchantId(),
            'Amount' => $this->getPayload('amount'),
            'Authority' => $this->getPayload('authority'),
        ];

        $response = Http::post($this->apiEndpoint('rest/WebGate/PaymentVerification.json'), $body);

        if ($response->successful() && $response['Status'] === 100) {
            return SuccessfulPayment::make($response['RefID']);
        }

        throw new PaymentVerifyException(ZarinpalErrorCodes::message($this->errorCode($response)), $this->errorCode($response));
    }

    protected function callbackUrl(): string
    {
        return URL::to(Arr::get($this->config, 'callback_url'));
    }

    protected function merchantId(): string
    {
        return Arr::get($this->config, 'merchant_id');
    }

    protected function sandbox(): bool
    {
        return Arr::get($this->config, 'sandbox', false);
    }

    protected function requestPayEndpoint(): string
    {
        return $this->apiEndpoint('request.json');
    }

    protected function verifyEndpoint(): string
    {
        return $this->apiEndpoint('verify.json');
    }

    protected function apiEndpoint(string $url): string
    {
        $prefix = $this->sandbox() ? 'https://sandbox.' : 'https://api.';

        return $prefix . $this->baseUrl . Str::start($url, '/');
    }
}
