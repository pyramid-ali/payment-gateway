<?php

namespace Alish\PaymentGateway\Http\Requests;

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
}
