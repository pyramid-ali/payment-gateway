<?php

namespace Alish\PaymentGateway\Drivers;

use Alish\PaymentGateway\Data\Zarinpal\ZarinpalRequestPaymentData;
use Alish\PaymentGateway\Data\Zarinpal\ZarinpalVerifyPaymentData;
use Alish\PaymentGateway\Data\ZarinpalData;
use Alish\PaymentGateway\Exceptions\Zarinpal\ZarinpalException;
use Alish\PaymentGateway\Listener\ZarinpalListener;
use Alish\PaymentGateway\Responses\Zarinpal\ZarinpalRequestPaymentResponse;
use Alish\PaymentGateway\Responses\Zarinpal\ZarinpalVerifyPaymentResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Zarinpal
{
    protected string $baseUrl = 'https://api.zarinpal.com/pg/v4/payment';

    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function request(ZarinpalData $requestPaymentData, ?ZarinpalListener $listener = null)
    {
        /** @var ZarinpalRequestPaymentData $data */
        $data = $requestPaymentData->zarinpal();

        $response = Http::post(
            $this->requestPayEndpoint(),
            $data->json([
                'merchant_id' => $this->merchantId(),
                'callback_url' => $this->callbackUrl()
            ])
        );

        if ($response->successful() && $response->json('data.code') === 100) {
            $response = ZarinpalRequestPaymentResponse::fromJson(
                $response->json('data')
            );

            $listener && $listener->zarinpal(
                $response
            );

            return $response;
        }

        throw (new ZarinpalException(
            $response->json('errors.message'),
            $response->json('errors.code')
        ))->validations($response->json('errors.validations'));
    }

    public function verify(ZarinpalData $data, ?ZarinpalListener $listener = null)
    {
        /** @var ZarinpalVerifyPaymentData $data */
        $data = $data->zarinpal();

        $response = Http::post($this->verifyEndpoint(), $data->json([
            'merchant_id' => $this->merchantId()
        ]));

        if ($response->successful() && ($response->json('data.code') === 100 || $response->json('data.code') === 101)) {
            $response = ZarinpalVerifyPaymentResponse::fromJson(
                $response->json('data')
            );

            $listener && $listener->zarinpal(
                $response
            );

            return $response;
        }

        throw (new ZarinpalException(
            $response->json('errors.message'),
            $response->json('errors.code')
        ))->validations($response->json('errors.validations'));
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

        return $this->baseUrl . Str::start($url, '/');
    }
}
