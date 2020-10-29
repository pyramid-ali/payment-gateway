<?php


namespace Alish\PaymentGateway\Helper;


class Parsian
{

    /**
     * @param int $amount amount unit is toman
     * @param string $payId
     * @param string $iban
     * @return array
     */
    public static function multiplexAccount(int $amount, string $payId, string $iban): array
    {
        return [
            'Amount' => $amount * 10,
            'PayId' => $payId,
            'IBAN' => $iban,
        ];
    }

}