<?php

return [
    'marker_path' => storage_path('framework/.installed'),
    'min_php' => '8.1',
    'required_extensions' => [
        'bcmath',
        'ctype',
        'curl',
        'dom',
        'fileinfo',
        'json',
        'mbstring',
        'openssl',
        'pdo',
        'pdo_mysql',
        'tokenizer',
        'xml',
        'zip',
    ],
    'required_permissions' => [
        base_path('.env') => 'file-writable',
        storage_path() => 'dir-writable',
        base_path('bootstrap/cache') => 'dir-writable',
        database_path() => 'dir-writable',
    ],
];
