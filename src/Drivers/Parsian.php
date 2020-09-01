<?php


namespace Alish\PaymentGateway\Drivers;


use Alish\PaymentGateway\Contracts\PaymentGateway;
use Alish\PaymentGateway\Exception\PaymentGatewayCreateException;
use Alish\PaymentGateway\Utils\HasConfig;
use Illuminate\Support\Facades\URL;
use Alish\PaymentGateway\Exception\PaymentVerifyException;
use Alish\PaymentGateway\PaymentLink;
use Alish\PaymentGateway\SuccessfulPayment;

class Parsian extends \Alish\PaymentGateway\PaymentGateway
{
    use HasConfig;

    protected $client;

    protected $url = "https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL";

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new \SoapClient($this->url);
    }

    public function create(int $amount): PaymentLink
    {
        $body = [
            "LoginAccount" => $this->pin(),
            "Amount" => $amount,
            "OrderId" => $this->getPayload('order_id'),
            "CallBackUrl" => $this->callback()
        ];

        $result = $this->client->SalePaymentRequest(["requestData" => $body]);

        if (
            $result->SalePaymentRequestResult->Token &&
            $result->SalePaymentRequestResult->Status === 0
        ) {
            $token = $result->SalePaymentRequestResult->Token;
            return PaymentLink::build($this->gateway(), $token, $this->gateLink($token));
        }

        throw new PaymentGatewayCreateException($result->SalePaymentRequestResult->Message, $result->SalePaymentRequestResult->Status);
    }

    public function verify(): SuccessfulPayment
    {
        $body = [
            "LoginAccount" => $this->pin(),
            "Token" => $this->getPayload('token')
        ];

        $result = $this->client->ConfirmPayment(["requestData" => $body]);

        if ($result->ConfirmPaymentResult->Status == '0') {
            return SuccessfulPayment::make('unknown');
        }

        throw new PaymentVerifyException($result->ConfirmPaymentResult->Message, $result->ConfirmPaymentResult->Status);
    }

    protected function pin()
    {
        return $this->getConfig('pin');
    }

    public function gateway(): string
    {
        return 'parsian';
    }

    protected function callback(): string
    {
        return URL::to($this->getConfig('callback'));
    }

    protected function gateLink(string $token): string
    {
        return "https://pec.shaparak.ir/NewIPG/?Token=" . $token;
    }
}