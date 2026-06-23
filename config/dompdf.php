<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DomPDF settings (PDF_HOSTS=dompdf only)
    |--------------------------------------------------------------------------
    |
    | Browsershot is configured in config/browsershot.php — these settings do not
    | affect the Browsershot driver.
    |
    */

    'show_warnings' => env('DOMPDF_SHOW_WARNINGS', false),

    'public_path' => public_path(),

    'convert_entities' => true,

    'options' => [
        'font_dir' => storage_path('fonts'),
        'font_cache' => storage_path('fonts'),
        'temp_dir' => storage_path('app/dompdf'),
        'chroot' => realpath(base_path()) ?: base_path(),
        'allowed_protocols' => [
            'data://' => ['rules' => []],
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],
        'log_output_file' => null,
        'enable_font_subsetting' => true,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'print',
        'default_paper_size' => 'a4',
        'default_paper_orientation' => 'portrait',
        'default_font' => env('DOMPDF_DEFAULT_FONT', 'DejaVu Sans'),
        'dpi' => (int) env('DOMPDF_DPI', 96),
        'enable_php' => false,
        'enable_javascript' => false,
        'enable_remote' => true,
        'font_height_ratio' => 1.1,
        'enable_html5_parser' => true,
    ],

    'default_font' => env('DOMPDF_DEFAULT_FONT', 'DejaVu Sans'),

    'chroot' => realpath(base_path()) ?: base_path(),

];
