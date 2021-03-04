<?php


namespace Alish\PaymentGateway\Responses\Zarinpal;


use Illuminate\Support\Arr;
use Spatie\DataTransferObject\DataTransferObject;

class ZarinpalVerifyPaymentResponse extends DataTransferObject
{

    public int $code;

    public string $message;

    public ?string $cardHash = null;

    public ?string $cardPan = null;

    public int $refId;

    public string $feeType;

    public int $fee;

    public static function fromJson(array $json)
    {
        return new self([
            'code' => Arr::get($json, 'code'),
            'message' => Arr::get($json, 'message'),
            'cardHash' => Arr::get($json, 'card_hash'),
            'cardPan' => Arr::get($json, 'card_pan'),
            'refId' => Arr::get($json, 'ref_id'),
            'feeType' => Arr::get($json, 'fee_type'),
            'fee' => Arr::get($json, 'fee'),
        ]);
    }

}