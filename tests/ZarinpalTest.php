<?php


namespace Alish\PaymentGateway\Tests;

use Alish\PaymentGateway\Drivers\Zarinpal;
use Alish\PaymentGateway\Exception\PaymentGatewayCreateException;
use Alish\PaymentGateway\Exception\PaymentVerifyException;
use Alish\PaymentGateway\PaymentLink;
use Alish\PaymentGateway\SuccessfulPayment;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Orchestra\Testbench\TestCase;

class ZarinpalTest extends TestCase
{

    /**
     * @test
     */
    public function we_can_create_payment_link()
    {
        $config = [
            'merchant_id' => 'merchanid',
            'sandbox' => false,
            'callback' => 'gateway/zarinpal',
            'zaringate' => null
        ];

        $authority = '00001';

        $zarinpal = new Zarinpal($config);

        Http::fake(function (Request $request) use ($authority, $config) {

            $data = $request->data();

            $this->assertArrayHasKey('MerchantID', $data);
            $this->assertArrayHasKey('Amount', $data);
            $this->assertArrayHasKey('CallbackURL', $data);
            $this->assertArrayHasKey('Description', $data);
            $this->assertArrayNotHasKey('Mobile', $data);
            $this->assertArrayNotHasKey('Email', $data);

            $this->assertEquals($data['MerchantID'], $config['merchant_id']);
            $this->assertEquals($data['CallbackURL'], URL::to($config['callback']));

            return Http::response(['Status' => 100, 'Authority' => $authority], 200);
        });

        $paymentLink = $zarinpal->create(10000, 'description');

        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
        $this->assertEquals($paymentLink->getAuthority(), $authority);
        $this->assertEquals($this->zarinpalRedirectLinkSchema($authority), $paymentLink->getLink());
    }

    /**
     * @test
     */
    public function we_can_create_zaringate_for_payment()
    {
        $zaringate = 'zaringate';

        $config = [
            'merchant_id' => 'merchanid',
            'sandbox' => false,
            'callback' => 'gateway/zarinpal',
            'zaringate' => $zaringate
        ];

        $authority = '00001';

        $zarinpal = new Zarinpal($config);

        Http::fake(function (Request $request) use ($authority) {
            return Http::response(['Status' => 100, 'Authority' => $authority], 200);
        });

        $paymentLink = $zarinpal->create(10000, 'description');

        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
        $this->assertEquals($paymentLink->getAuthority(), $authority);
        $this->assertEquals($this->zarinpalRedirectLinkSchema($authority, $zaringate), $paymentLink->getLink());
    }

    protected function zarinpalRedirectLinkSchema(string $authority, ?string $zaringate = null)
    {

        return 'https://www.zarinpal.com/pg/StartPay/'.$authority.($zaringate ? '/'.$zaringate : '');
    }

    /**
     * @test
     */
    public function we_can_user_zarinpal_sandbox()
    {
        $config = [
            'merchant_id' => 'merchanid',
            'sandbox' => true,
            'callback' => 'gateway/zarinpal',
            'zaringate' => null
        ];

        $authority = '00001';

        $zarinpal = new Zarinpal($config);

        Http::fake(function (Request $request) use ($authority) {
            return Http::response(['Status' => 100, 'Authority' => $authority], 200);
        });

        $paymentLink = $zarinpal->create(10000, 'description');

        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
        $this->assertEquals($paymentLink->getAuthority(), $authority);
        $this->assertStringContainsString($authority, $paymentLink->getLink());
        $this->assertStringContainsString('https://sandbox.', $paymentLink->getLink());
    }

    /**
     * @test
     */
    public function we_can_verify_payment_was_successful()
    {
        $config = [
            'merchant_id' => 'merchanid',
            'sandbox' => true,
            'callback' => 'gateway/zarinpal',
            'zaringate' => null
        ];

        $refID = '00001';

        $zarinpal = new Zarinpal($config);

        Http::fake(function (Request $request) use ($refID) {
            return Http::response(['Status' => 100, 'RefID' => $refID], 200);
        });

        $successful = $zarinpal->verify(10000, '01');

        $this->assertInstanceOf(SuccessfulPayment::class, $successful);
        $this->assertEquals($successful->getRefId(), $refID);
    }

    /**
     * @test
     */
    public function if_create_request_was_unsuccessful_we_get_error()
    {
        $zarinpal = new Zarinpal([
            'merchant_id' => 'merchanid',
            'callback' => 'gateway/zarinpal'
        ]);

        Http::fake(function (Request $request) {
            return Http::response('response', 500);
        });

        $this->expectException(PaymentGatewayCreateException::class);

        $zarinpal->create(10000, 'description');
    }

    /**
     * @test
     */
    public function if_verify_payment_was_unsuccessful_we_get_error()
    {
        $zarinpal = new Zarinpal([
            'merchant_id' => 123
        ]);

        Http::fake(function (Request $request) {
            return Http::response('response', 500);
        });

        $this->expectException(PaymentVerifyException::class);

        $zarinpal->verify(10000, 'authority');
    }
}