<?php

return [
    'pdf' => [
        'enabled' => true,
        'binary' => env('WKHTMLTOPDF_BINARY', '/usr/local/bin/wkhtmltopdf'),
        'timeout' => false,
        'options' => [
            'encoding' => 'utf-8',
            'enable-local-file-access' => true,
        ],
        'env' => [],
    ],
    'image' => [
        'enabled' => true,
        'binary' => env('WKHTMLTOIMAGE_BINARY', '/usr/bin/wkhtmltoimage'),
        'timeout' => false,
        'options' => [],
        'env' => [],
    ],
];
