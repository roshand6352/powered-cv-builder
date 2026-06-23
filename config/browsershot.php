<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Node Binary
    |--------------------------------------------------------------------------
    |
    | Path to the Node.js executable. Leave null to auto-detect from PATH.
    | On Laragon/Windows, Node is usually available globally.
    |
    */

    'node_binary' => env('BROWSERSHOT_NODE_BINARY'),

    /*
    |--------------------------------------------------------------------------
    | NPM Binary
    |--------------------------------------------------------------------------
    */

    'npm_binary' => env('BROWSERSHOT_NPM_BINARY'),

    /*
    |--------------------------------------------------------------------------
    | Chrome / Chromium Paths
    |--------------------------------------------------------------------------
    |
    | Browsershot uses Puppeteer (installed via npm). Override Chrome path
    | if Puppeteer's bundled Chromium is not used.
    |
    */

    'chrome_path' => env('BROWSERSHOT_CHROME_PATH'),

    /*
    |--------------------------------------------------------------------------
    | PDF Defaults
    |--------------------------------------------------------------------------
    */

    'pdf' => [
        'format' => env('BROWSERSHOT_PDF_FORMAT', 'A4'),
        'margins' => [
            'top' => 10,
            'right' => 10,
            'bottom' => 10,
            'left' => 10,
        ],
        'print_background' => true,
        'wait_until' => env('BROWSERSHOT_PDF_WAIT_UNTIL', 'load'),
        'live_preview_wait_until' => env('BROWSERSHOT_LIVE_PREVIEW_WAIT_UNTIL', 'networkidle0'),
        'live_preview_timeout' => (int) env('BROWSERSHOT_LIVE_PREVIEW_TIMEOUT', 45),
    ],

    /*
    |--------------------------------------------------------------------------
    | Screenshot Defaults
    |--------------------------------------------------------------------------
    */

    'screenshot' => [
        'width' => (int) env('BROWSERSHOT_SCREENSHOT_WIDTH', 1200),
        'height' => (int) env('BROWSERSHOT_SCREENSHOT_HEIGHT', 800),
        'full_page' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeouts (seconds)
    |--------------------------------------------------------------------------
    */

    'timeout' => (int) env('BROWSERSHOT_TIMEOUT', 90),

    /*
    |--------------------------------------------------------------------------
    | Disable Chrome sandbox (required on some Linux servers)
    |--------------------------------------------------------------------------
    */

    'no_sandbox' => (bool) env('BROWSERSHOT_NO_SANDBOX', false),

];
