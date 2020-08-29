<?php


namespace Alish\PaymentGateway\Drivers;


use Alish\PaymentGateway\Contracts\PaymentGateway;
use Alish\PaymentGateway\Exception\PaymentGatewayCreateException;
use Alish\PaymentGateway\Exception\PaymentVerifyException;
use Alish\PaymentGateway\PaymentLink;
use Alish\PaymentGateway\SuccessfulPayment;

class Parsian implements PaymentGateway
{

    protected $client;

    protected $config;

    protected $url = "https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL";

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new \SoapClient($this->url);
    }

    public function create(int $amount, string $description): PaymentLink
    {
        $body = [
            "LoginAccount" => $this->pin(),
            "Amount" => $amount,
            "OrderId" => $description,
            "CallBackUrl" => $this->callback()
        ];

        $result = $this->client->SalePaymentRequest(["requestData" => $body]);

        if ($result->SalePaymentRequestResult->Token && $result->SalePaymentRequestResult->Status === 0) {
            $token = $result->SalePaymentRequestResult->Token;
            return PaymentLink::build($this->gateway(), $token, $this->gateLink($token));
        }

        throw new \Exception($result->SalePaymentRequestResult->Message);
    }

    public function verify(int $amount, string $authority): SuccessfulPayment
    {
        $body = [
            "LoginAccount" => $this->pin(),
            "Token" => $authority
        ];

        $result = $this->client->ConfirmPayment(["requestData" => $body]);

        if ($result->ConfirmPaymentResult->Status == '0') {
            return SuccessfulPayment::make('unknown');
        }

        throw new PaymentVerifyException($result->ConfirmPaymentResult->Message);
    }

    public function gateway(): string
    {
        return 'parsian';
    }

    protected function callback(): string
    {
        return $this->config['callback'];
    }

    protected function pin(): string
    {
        return $this->config['pin'];
    }

    protected function gateLink(string $token): string
    {
        return "https://pec.shaparak.ir/NewIPG/?Token=" . $token;
    }
}