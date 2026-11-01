<?php
/**
 * SR Worldwide configuration.
 *
 * Copy this file to includes/config.php and edit the values.
 * config.php is ignored by git, so your real credentials never get committed.
 */
return [
    // 'development' shows PHP errors in the browser. Use 'production' anywhere public.
    'env' => 'production',

    // URL path where the public/ folder is served, without a trailing slash.
    // MAMP with a symlink htdocs/sr-worldwide -> public  =>  '/sr-worldwide'
    // Document root pointing straight at public/        =>  ''
    'base_url' => '/sr-worldwide',

    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 8889,          // MAMP's default MySQL port
        'name'     => 'sr_worldwide',
        'user'     => 'root',
        'password' => 'root',
    ],

    // Product image uploads (admin)
    'upload_max_bytes' => 2 * 1024 * 1024, // 2 MB
];
