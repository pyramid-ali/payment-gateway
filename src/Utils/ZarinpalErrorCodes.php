<?php


namespace Alish\PaymentGateway\Utils;


class ZarinpalErrorCodes
{

    public static function message(?int $code)
    {
        if (!$code) {
            return 'Unknown Error';
        }

        switch ($code) {
            case -1:
                return  "Imperfect data";
            case -2:
                return  "Wrong Ip or merchant code";
            case -3:
                return  "Shaparak limit";
            case -4:
                return  "Level of verification under silver";
            case -11:
                return  "Not found request";
            case -12:
                return  "Editing is not possible";
            case -21:
                return  "Not found finance Transaction";
            case -22:
                return  "Failed Transaction";
            case -33:
                return  "Conflict in amount of paid and verification";
            case -34:
                return  "Limit of Transaction count";
            case -40:
                return  "Can't access to this method";
            case -41:
                return  "Invalid data sent to AdditionalData";
            case -42:
                return  "Valid time Of authority passed";
            case -54:
                return  "Request has been archived";
            case 101:
                return  "Repeated check of verification";
            default:
                return "Unknown Error";
        }
    }

}