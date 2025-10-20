<?php

return [
    'extensions' => [
        'required' => [
            'bcmath' => 'BCMath',
            'ctype' => 'Ctype',
            'curl' => 'cURL',
            'dom' => 'DOM',
            'fileinfo' => 'Fileinfo',
            'json' => 'JSON',
            'mbstring' => 'Mbstring',
            'openssl' => 'OpenSSL',
            'pdo' => 'PDO',
            'tokenizer' => 'Tokenizer',
            'xml' => 'XML',
        ],
        'optional' => [
            'pcntl' => 'PCNTL',
        ],
    ],

    'paths' => [
        'storage' => [
            'label' => 'Storage directory',
            'path' => storage_path(),
        ],
        'bootstrap_cache' => [
            'label' => 'Bootstrap cache directory',
            'path' => base_path('bootstrap/cache'),
        ],
    ],
];
