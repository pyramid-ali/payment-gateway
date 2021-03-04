<?php

namespace Alish\PaymentGateway\Tests;

use Alish\PaymentGateway\Drivers\Zarinpal;
use Alish\PaymentGateway\Exceptions\Zarinpal\ZarinpalException;
use Alish\PaymentGateway\PaymentGatewayServiceProvider;
use Alish\PaymentGateway\Responses\Zarinpal\ZarinpalRequestPaymentResponse;
use Alish\PaymentGateway\Responses\Zarinpal\ZarinpalVerifyPaymentResponse;
use Alish\PaymentGateway\Tests\Zarinpal\RequestPaymentData;
use Alish\PaymentGateway\Tests\Zarinpal\VerifyPaymentData;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;

class ZarinpalTest extends TestCase
{

    protected array $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'merchant_id' => Str::random(36),
            'sandbox' => false,
            'callback_url' => 'callback'
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

    protected function zarinpal()
    {
        return new Zarinpal($this->getConfig());
    }

    public function test_request_payment_work_as_expected()
    {
        $zarinpal = $this->zarinpal();

        Http::fake(function (Request $request) {
            $this->assertEquals("https://api.zarinpal.com/pg/v4/payment/request.json", $request->url());

            $this->assertEquals(
                $this->config['merchant_id'], $request->data()['merchant_id']
            );

            $this->assertEquals(
                1000, $request->data()['amount']
            );

            $this->assertEquals(
                'test description', $request->data()['description']
            );

            $this->assertEquals(
                "http://localhost/callback", $request->data()['callback_url']
            );

            $this->assertEquals(
                '09123456789', $request->data()['metadata']['mobile']
            );

            $this->assertEquals(
                'test@test.com', $request->data()['metadata']['email']
            );

            return Http::response([
                'data' => [
                    'code' => 100,
                    'authority' => '0000001',
                    'message' => 'successful',
                    'fee_type' => 'merchant',
                    'fee' => 0
                ],
                "errors" => []
            ], 200);
        });

        /** @var ZarinpalRequestPaymentResponse $response */
        $response = $zarinpal->request(new RequestPaymentData);

        $this->assertEquals('0000001', $response->authority);
        $this->assertEquals(100, $response->code);
        $this->assertEquals('successful', $response->message);
        $this->assertEquals('merchant', $response->feeType);
    }

    public function test_verify_work_as_expected()
    {
        $zarinpal = $this->zarinpal();

        Http::fake(function (Request $request) {
            $this->assertEquals("https://api.zarinpal.com/pg/v4/payment/verify.json", $request->url());

            $this->assertEquals(
                $this->config['merchant_id'], $request->data()['merchant_id']
            );

            $this->assertEquals(
                1000, $request->data()['amount']
            );

            $this->assertEquals(
                '000001', $request->data()['authority']
            );


            return Http::response([
                'data' => [
                    'code' => 100,
                    'ref_id' => 1,
                    'message' => 'successful',
                    'fee_type' => 'merchant',
                    'fee' => 0
                ],
                "errors" => []
            ], 200);
        });

        /** @var ZarinpalVerifyPaymentResponse $response */
        $response = $zarinpal->verify(new VerifyPaymentData());

        $this->assertEquals(1, $response->refId);
        $this->assertEquals(100, $response->code);
        $this->assertEquals('successful', $response->message);
        $this->assertEquals('merchant', $response->feeType);
    }

    public function test_request_payment_failed()
    {
        $zarinpal = $this->zarinpal();

        $this->expectException(ZarinpalException::class);

        Http::fake(function (Request $request) {
            return Http::response([
                'data' => [
                    'code' => 102,
                    'message' => 'failed',
                ],
                "errors" => [
                    "validations" => []
                ]
            ], 403);
        });

        /** @var ZarinpalRequestPaymentResponse $response */
        $response = $zarinpal->request(new RequestPaymentData);
    }
}
