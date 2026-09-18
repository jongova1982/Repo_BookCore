<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

$configuredSessionPath = session_save_path();
if ($configuredSessionPath === '' || !is_dir($configuredSessionPath) || !is_writable($configuredSessionPath)) {
    $fallbackSessionPath = __DIR__ . '/storage/sessions';
    if (!is_dir($fallbackSessionPath)) {
        mkdir($fallbackSessionPath, 0700, true);
    }
    session_save_path($fallbackSessionPath);
}

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();

