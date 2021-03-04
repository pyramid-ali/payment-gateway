<?php


namespace Alish\PaymentGateway\Tests;


use Alish\PaymentGateway\Data\Data;
use Orchestra\Testbench\TestCase;

class ExampleTest extends TestCase
{

    public function testTest()
    {
        $data = new Data();

        $data->get();
    }

}