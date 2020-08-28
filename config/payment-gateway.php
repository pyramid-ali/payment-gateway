<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default
    |--------------------------------------------------------------------------
    |
    | default driver to use for payment gateway
    |
    */

    'default' => env('PAYMENT_GATEWAY_DRIVER', 'zarinpal'),

    'zarinpal' => [
        'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
        'sandbox' => env('ZARINPAL_SANDBOX', true),
        'callback' => env('ZARINPAL_CALLBACK', 'gateway/zarinpal'),
        'zaringate' => env('ZARINPAL_ZARINGATE', null),
    ],

    'parsian' => [
        'pin' => env('PARSIAN_PIN'),
        'callback' => env('PARSIAN_CALLBACK', 'gateway/parsian'),
    ]

];
