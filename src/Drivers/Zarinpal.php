<?php


namespace Alish\PaymentGateway\Drivers;


use Alish\PaymentGateway\Contracts\PaymentGateway;
use Alish\PaymentGateway\Exception\PaymentGatewayCreateException;
use Alish\PaymentGateway\Exception\PaymentVerifyException;
use Alish\PaymentGateway\PaymentLink;
use Alish\PaymentGateway\SuccessfulPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Zarinpal implements PaymentGateway
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $mobile;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $url = 'zarinpal.com/pg';

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param  int  $amount
     * @param  string|null  $description
     * @return PaymentLink
     * @throws PaymentGatewayCreateException
     */
    public function create(int $amount, string $description): PaymentLink
    {
        $body = [
            'MerchantID' => $this->merchantId(),
            'Amount' => $amount,
            'CallbackURL' => URL::to($this->callback()),
            'Description' => $description,
        ];

        $this->mobile ?: $body['Mobile'] = $this->mobile;
        $this->email ?: $body['Email'] = $this->email;


        $response = Http::retry(3, 100)->post($this->endpoint('rest/WebGate/PaymentRequest.json'), $body);


        if ($response->successful() && $response['Status'] === 100) {
            $authority = $response['Authority'];
            return PaymentLink::build('zarinpal', $authority, $this->redirectUrl($authority));
        }


        throw new PaymentGatewayCreateException($response);

    }

    /**
     * @param  int  $amount
     * @param  string  $authority
     * @return SuccessfulPayment
     * @throws PaymentVerifyException
     */
    public function verify(int $amount, string $authority): SuccessfulPayment
    {
        $body = [
            'MerchantID' => $this->merchantId(),
            'Amount' => $amount,
            'Authority' => $authority,
        ];

        $response = Http::retry(3, 100)->post($this->endpoint('rest/WebGate/PaymentVerification.json'), $body);

        if ($response->successful() && $response['Status'] === 100) {
            return SuccessfulPayment::make($response['RefID']);
        }

        throw new PaymentVerifyException($response);
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    protected function callback(): string
    {
        return $this->config['callback'];
    }

    protected function merchantId(): string
    {
        return $this->config['merchant_id'];
    }

    protected function sandbox(): string
    {
        return $this->config['sandbox'];
    }

    protected function zaringate(): ?string
    {
        return $this->config['zaringate'];
    }

    protected function endpoint(string $url): string
    {
        $prefix = $this->sandbox() ? 'https://sandbox.' : 'https://www.';
        return $prefix . $this->url . Str::start($url, '/');
    }

    public function redirectUrl(string $authority): string
    {
        $suffix = '';

        if ($this->zaringate()) {
            $suffix = Str::start($this->zaringate(), '/');
        }

        return $this->endpoint('StartPay/' . $authority) . $suffix;
    }


}