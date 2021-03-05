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

    /*
    |--------------------------------------------------------------------------
    | Zarinpal
    |--------------------------------------------------------------------------
    |
    | zarinpal config
    |
    */

    'zarinpal' => [
        'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
        'callback_url' => env('ZARINPAL_CALLBACK', 'gateway/zarinpal')
    ],
];
