<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

try {
    initializeDatabase();
    refresh_overdue_loans(db());
} catch (Throwable $e) {
    http_response_code(500);
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit('<h1>Error de conexión</h1><p>No fue posible conectarse a MySQL. Revisa <strong>config.php</strong> y las credenciales de AlwaysData.</p><pre>' . $message . '</pre>');
}
