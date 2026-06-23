<?php

return [

    'mode' => env('PAYFAST_MODE', 'sandbox'),

    'merchant_id' => env('PAYFAST_MERCHANT_ID'),
    'merchant_key' => env('PAYFAST_MERCHANT_KEY'),
    'passphrase' => env('PAYFAST_PASSPHRASE'),

    'currency' => env('PAYFAST_CURRENCY', 'ZAR'),

    'sandbox' => [
        'process_url' => 'https://sandbox.payfast.co.za/eng/process',
        'validate_url' => 'https://sandbox.payfast.co.za/eng/query/validate',
    ],

    'live' => [
        'process_url' => 'https://www.payfast.co.za/eng/process',
        'validate_url' => 'https://www.payfast.co.za/eng/query/validate',
    ],

];
