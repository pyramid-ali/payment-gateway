<?php


namespace Alish\PaymentGateway\Tests;
use Alish\PaymentGateway\PaymentGatewayServiceProvider;
use Orchestra\Testbench\TestCase;

class ManagerTest extends TestCase
{

    protected function getEnvironmentSetUp($app)
    {
        $this->app['config']->set('payment-gateway', 'zarinpal');
    }

    /**
     * @test
     */
    public function manager_return_current_instance_of_driver()
    {
        dump($this->app['config']->get('payment-gateway'));
        $this->app['config']->set('');
    }

    protected function getPackageProviders($app)
    {
        return [PaymentGatewayServiceProvider::class];
    }

}