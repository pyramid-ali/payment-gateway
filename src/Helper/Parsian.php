<?php


namespace Alish\PaymentGateway\Helper;


class Parsian
{

    public static function multiplexAccount(int $amount, string $payId, string $iban): array
    {
        return [
            'Amount' => $amount,
            'PayId' => $payId,
            'IBAN' => $iban,
        ];
    }

}