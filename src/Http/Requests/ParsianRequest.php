<?php

namespace Alish\PaymentGateway\Http\Requests;

use Alish\PaymentGateway\Exception\PaymentVerifyException;
use Alish\PaymentGateway\Facade\PaymentGateway;
use Alish\PaymentGateway\SuccessfulPayment;
use Illuminate\Foundation\Http\FormRequest;

class ParsianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function isOk(): bool
    {
        return $this->get('status') == 0;
    }

    public function token(): ?string
    {
        return $this->get('Token');
    }

    public function amount(): ?string
    {
        return $this->get('Amount');
    }

    public function rrn(): ?string
    {
        return $this->get('RRN');
    }
}
