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

    public $paymentLink;

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
        $authority = random_int(0, 1000);
        return $this->paymentLink = PaymentLink::build($this->driver, $authority, $this->link($authority));
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

    public function gateway(): string
    {
        return 'fake';
    }

    protected function link(string $authority): string
    {
        return 'https://'.$this->driver.'.com/pay/'.$authority;
    }
}