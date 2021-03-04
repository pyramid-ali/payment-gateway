<?php


namespace Alish\PaymentGateway\Exceptions;


use Throwable;

class ArgumentIsRequired extends \Exception
{

    protected string $argument;

    public function __construct(string $argument)
    {
        parent::__construct("$argument argument is required, please provide appropriate value");
        $this->argument = $argument;
    }
}