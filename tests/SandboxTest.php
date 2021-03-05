<?php

namespace Alish\PaymentGateway\Tests;

use Alish\PaymentGateway\Drivers\Sandbox;
use Alish\PaymentGateway\Exceptions\Zarinpal\ZarinpalException;
use Alish\PaymentGateway\Facade\PaymentGateway;
use Alish\PaymentGateway\PaymentGatewayServiceProvider;
use Alish\PaymentGateway\Responses\Zarinpal\ZarinpalRequestPaymentResponse;
use Alish\PaymentGateway\Responses\Zarinpal\ZarinpalVerifyPaymentResponse;
use Alish\PaymentGateway\Tests\Zarinpal\RequestPaymentData;
use Alish\PaymentGateway\Tests\Zarinpal\VerifyPaymentData;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;

class SandboxTest extends TestCase
{

    protected array $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'simulate' => 'zarinpal'
        ];
    }

    protected function getPackageProviders($app)
    {
        return [PaymentGatewayServiceProvider::class];
    }

    protected function getConfig(array $overrides = [])
    {
        return array_merge($this->config, $overrides);
    }

    protected function sandbox()
    {
        return new Sandbox($this->getConfig());
    }

    public function test_request_payment_work_as_expected_for_zarinpal_simulate()
    {
        $sandbox = $this->sandbox();

        /** @var ZarinpalRequestPaymentResponse $response */
        $response = $sandbox->request(new RequestPaymentData);

        $this->assertNotNull($response->authority);
        $this->assertEquals(100, $response->code);
        $this->assertEquals('successful', $response->message);
        $this->assertEquals('merchant', $response->feeType);
        $this->assertEquals(0, $response->fee);
    }

    public function test_verify_work_as_expected_for_zarinpal_simulate()
    {
        $sandbox = $this->sandbox();

        /** @var ZarinpalVerifyPaymentResponse $response */
        $response = $sandbox->verify(new VerifyPaymentData());

        $this->assertNotNull($response->refId);
        $this->assertEquals(100, $response->code);
        $this->assertEquals('successful', $response->message);
        $this->assertEquals('merchant', $response->feeType);
        $this->assertEquals(0, $response->fee);
    }

    public function test_request_simulation()
    {
        PaymentGateway::simulate('zarinpal', [
            'request' => $simulatedResponse = ZarinpalRequestPaymentResponse::fromJson([
                'code' => 100,
                'message' => 'successful',
                'authority' => '123456',
                'fee_type' => 'merchant',
                'fee' => 0
            ])
        ]);

        /** @var ZarinpalRequestPaymentResponse $response */
        $response = PaymentGateway::request(new RequestPaymentData);

        $this->assertSame($simulatedResponse, $response);
    }

    public function test_verify_simulation()
    {
        PaymentGateway::simulate('zarinpal', [
            'verify' => $simulatedResponse = ZarinpalVerifyPaymentResponse::fromJson([
                'code' => 100,
                'message' => 'successful',
                'ref_id' => 123456,
                'fee_type' => 'merchant',
                'fee' => 0
            ])
        ]);

        /** @var ZarinpalVerifyPaymentResponse $response */
        $response = PaymentGateway::verify(new RequestPaymentData);

        $this->assertSame($simulatedResponse, $response);
    }
}
