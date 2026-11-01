<?php
/**
 * Loaded at the top of every page: config, error handling, session, helpers.
 */
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Missing includes/config.php. Copy includes/config.example.php to includes/config.php and edit it.');
}
$config = require $configFile;

define('APP_ENV', $config['env'] ?? 'production');
define('BASE_URL', rtrim($config['base_url'] ?? '', '/'));
define('UPLOAD_MAX_BYTES', (int) ($config['upload_max_bytes'] ?? 2 * 1024 * 1024));

// Errors: show them only while developing, always log them.
error_reporting(E_ALL);
ini_set('display_errors', APP_ENV === 'development' ? '1' : '0');
ini_set('log_errors', '1');

// Basic security headers.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Session cookie: not readable from JavaScript, not sent on cross-site POSTs.
session_name('srw_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => BASE_URL === '' ? '/' : BASE_URL . '/',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();

require __DIR__ . '/db.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/csrf.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/cart.php';

db_init($config['db']);
unset($config);
