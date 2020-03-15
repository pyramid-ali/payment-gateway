<?php

namespace App\Http\Requests;

use Alish\PaymentGateway\Exception\PaymentVerifyException;
use Alish\PaymentGateway\Facade\PaymentGateway;
use Alish\PaymentGateway\SuccessfulPayment;
use Illuminate\Foundation\Http\FormRequest;

class ZarinpalRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Status' => 'required',
            'Authority' => 'required',
        ];
    }

    public function isOk(): bool
    {
        return $this->get('Status') === 'OK';
    }

    public function authority()
    {
        return $this->get('Authority');
    }

    /**
     * @param  int  $amount
     * @return SuccessfulPayment
     * @throws PaymentVerifyException
     */
    public function verify(int $amount): SuccessfulPayment
    {
        return PaymentGateway::driver('zarinpal')->verify($amount);
    }
}
