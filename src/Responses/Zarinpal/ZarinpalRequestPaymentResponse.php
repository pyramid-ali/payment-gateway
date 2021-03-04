<?php


namespace Alish\PaymentGateway\Responses\Zarinpal;


use Illuminate\Support\Str;
use Spatie\DataTransferObject\DataTransferObject;

class ZarinpalRequestPaymentResponse extends DataTransferObject
{
    protected static string $baseUrl = "https://zarinpal.com/pg/StartPay";

    public int $code;

    public string $message;

    public string $authority;

    public string $feeType;

    public int $fee;

    public string $redirectUrl;

    public static function fromJson(array $json)
    {
        return new self([
            'code' => $json['code'],
            'message' => $json['message'],
            'authority' => $json['authority'],
            'feeType' => $json['fee_type'],
            'fee' => $json['fee'],
            'redirectUrl' => self::redirectUrl($json)
        ]);
    }

    protected static function redirectUrl(array $json): string
    {
        return self::$baseUrl . Str::start($json['authority'], '/');
    }
}