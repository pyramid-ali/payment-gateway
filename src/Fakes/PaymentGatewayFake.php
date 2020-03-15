<?php


namespace Alish\PaymentGateway\Fakes;


use Alish\PaymentGateway\Contracts\PaymentGateway;
use Alish\PaymentGateway\Exception\PaymentGatewayCreateException;
use Alish\PaymentGateway\Exception\PaymentVerifyException;
use Alish\PaymentGateway\PaymentLink;
use Alish\PaymentGateway\SuccessfulPayment;
use Illuminate\Support\Facades\Http;

class PaymentGatewayFake implements PaymentGateway
{

    /**
     * @var string
     */
    protected $driver;

    /**
     * @var bool
     */
    protected $shouldThrow;

    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param  int  $amount
     * @param  string  $description
     * @return PaymentLink
     * @throws PaymentGatewayCreateException
     */
    public function create(int $amount, string $description): PaymentLink
    {
        if ($this->shouldThrow) {
            throw new PaymentGatewayCreateException(Http::response('failed', 400));
        }
        return PaymentLink::build($this->driver, random_int(0, 1000), 'https://'.$this->driver.'.com/pay');
    }

    /**
     * @param  int  $amount
     * @param  string  $authority
     * @return SuccessfulPayment
     * @throws PaymentVerifyException
     */
    public function verify(int $amount, string $authority): SuccessfulPayment
    {
        if ($this->shouldThrow) {
            throw new PaymentVerifyException(Http::response('failed', 400));
        }
        return SuccessfulPayment::make(random_int(0, 1000));
    }

    public function shouldThrow()
    {
        $this->shouldThrow = true;
        return $this;
    }
}