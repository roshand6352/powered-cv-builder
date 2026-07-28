<?php

return [

    'disk' => env('PDF_EXPORT_DISK', 'local'),

    'directory' => 'pdf-exports',

    'queue' => env('PDF_EXPORT_QUEUE', 'default'),

    'format' => 'A4',

    /*
    |--------------------------------------------------------------------------
    | PDF engine (PDF_HOSTS)
    |--------------------------------------------------------------------------
    |
    | Set to "browsershot" or "dompdf". Switching engines only requires changing
    | this value in .env — no code changes needed.
    |
    */
    'hosts' => env('PDF_HOSTS', 'browsershot'),

    'quality' => [
        'device_scale_factor' => (int) env('PDF_DEVICE_SCALE_FACTOR', 2),
        'print_background' => true,
    ],

    'margins' => [
        'top' => 0,
        'right' => 0,
        'bottom' => 0,
        'left' => 0,
    ],

    'header' => [
        'enabled' => true,
        'height_mm' => 10,
        'template' => 'web.pdf.partials.header',
    ],

    'footer' => [
        'enabled' => true,
        'height_mm' => 10,
        'template' => 'web.pdf.partials.footer',
    ],

    'watermark' => [
        'enabled_for_free_users' => true,
        'plans_without_watermark' => ['pro', 'premium', 'business'],
    ],

    'cover_letter' => [
        'defaults' => [
            'sender' => [
                'full_name' => '',
                'email' => '',
                'phone' => '',
                'location' => '',
            ],
            'recipient' => [
                'name' => '',
                'company' => '',
                'address' => '',
            ],
            'date' => '',
            'subject' => '',
            'salutation' => 'Dear Hiring Manager,',
            'body' => '',
            'closing' => 'Sincerely,',
            'signature' => '',
        ],
    ],

];
