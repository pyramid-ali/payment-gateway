<?php


namespace Alish\PaymentGateway\Responses;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\DataTransferObject\DataTransferObject;

class ZarinpalRequestPaymentResponse extends DataTransferObject
{
    protected static string $baseUrl = "zarinpal.com/pg/StartPay";

    public int $code;

    public string $message;

    public string $authority;

    public string $feeType;

    public int $fee;

    public string $redirectUrl;

    public static function fromJson(array $json, bool $sandbox)
    {
        return new self([
            'code' => $json['code'],
            'message' => $json['message'],
            'authority' => $json['authority'],
            'feeType' => $json['fee_type'],
            'fee' => $json['fee'],
            'redirectUrl' => self::redirectUrl($json, $sandbox)
        ]);
    }

    protected static function redirectUrl(array $json, bool $sandbox): string
    {
        $prefix = $sandbox ? "https://sandbox." : "https://www.";

        return $prefix . self::$baseUrl . Str::start($json['authority'], '/');
    }
}