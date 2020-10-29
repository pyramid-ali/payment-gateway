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

    protected $saleService = "https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL";

    protected $confirmService = "https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?WSDL";

    protected $multiplexedSale = "https://pec.shaparak.ir/NewIPGServices/MultiplexedSale/OnlineMultiplexedSalePaymentService.asmx?wsdl";

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function saleClient()
    {
        return new \SoapClient($this->saleService);
    }

    protected function confirmClient()
    {
        return new \SoapClient($this->confirmService);
    }

    protected function multiplexSaleClient()
    {
        return new \SoapClient($this->multiplexedSale);
    }

    public function create(int $amount): PaymentLink
    {
        if ($this->getPayload('accounts')) {
            return $this->multiplexSale($amount);
        }

        $body = [
            "LoginAccount" => $this->pin(),
            "Amount" => $amount * 10, // convert toman to rials
            "OrderId" => $this->getPayload('order_id'),
            "CallBackUrl" => $this->callback()
        ];

        $result = $this->saleClient()->SalePaymentRequest(["requestData" => $body]);

        if (
            $result->SalePaymentRequestResult->Token &&
            $result->SalePaymentRequestResult->Status === 0
        ) {
            $token = $result->SalePaymentRequestResult->Token;
            return PaymentLink::build($this->gateway(), $token, $this->gateLink($token));
        }

        throw new PaymentGatewayCreateException($result->SalePaymentRequestResult->Message, $result->SalePaymentRequestResult->Status);
    }

    public function multiplexSale(int $amount)
    {
        $body = [
            "LoginAccount" => $this->pin(),
            "Amount" => $amount * 10, // convert toman to rials
            "OrderId" => $this->getPayload('order_id'),
            "CallBackUrl" => $this->callback(),
            "MultiplexedAccounts" => $this->getPayload('accounts')
        ];

        $result = $this->multiplexSaleClient()->MultiplexedSaleWithIBANPaymentRequest(["requestData" => $body]);

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

        $result = $this->confirmClient()->ConfirmPayment(["requestData" => $body]);

        if ($result->ConfirmPaymentResult->Status == '0') {
            return SuccessfulPayment::make();
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