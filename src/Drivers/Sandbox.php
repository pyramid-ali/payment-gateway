<?php


namespace Alish\PaymentGateway\Drivers;

use Alish\PaymentGateway\Data\ZarinpalData;
use Alish\PaymentGateway\Listener\ZarinpalListener;
use Alish\PaymentGateway\Responses\Zarinpal\ZarinpalRequestPaymentResponse;
use Alish\PaymentGateway\Responses\Zarinpal\ZarinpalVerifyPaymentResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Sandbox
{

    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function getSimulateDriver()
    {
        return Arr::get($this->config, 'simulate', 'zarinpal');
    }

    protected function getResponse(string $method)
    {
        $simulatedResponse = Arr::get($this->getSimulatedResponses(), $method);

        if (!is_null($simulatedResponse)) {
            return $simulatedResponse;
        }

        $method = $this->getSimulateDriver() . Str::studly($method);
        return $this->$method();
    }

    public function getSimulatedResponses(): array
    {
        return Arr::get($this->config, 'responses', []);
    }

    protected function zarinpalRequest()
    {
        return ZarinpalRequestPaymentResponse::fromJson(
            [
                'code' => 100,
                'message' => 'successful',
                'authority' => Str::random(16),
                'fee_type' => 'merchant',
                'fee' => 0
            ]
        );
    }

    protected function zarinpalVerify()
    {
        return ZarinpalVerifyPaymentResponse::fromJson(
            [
                'code' => 100,
                'message' => 'successful',
                'ref_id' => random_int(0, 9999),
                'fee_type' => 'merchant',
                'fee' => 0
            ]
        );
    }

    protected function createResponseAndNotify($method, $listener = null)
    {
        $listenerMethod = $this->getSimulateDriver();
        $response = $this->getResponse($method);

        $listener && $listener->$listenerMethod(
            $response
        );

        return $response;
    }

    public function request($requestPaymentData, $listener = null)
    {
        return $this->createResponseAndNotify('request', $listener);
    }

    public function verify(ZarinpalData $data, ?ZarinpalListener $listener = null)
    {
        return $this->createResponseAndNotify('verify', $listener);
    }

}