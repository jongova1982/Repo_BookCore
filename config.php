<?php
/**
 * CONFIGURACIÓN DE CONEXIÓN A LA BASE DE DATOS
 * 
 * Credenciales de AlwaysData - MySQL
 * (Cambia estos valores por los tuyos)
 */

define('DB_HOST', 'mysql-vinasco.alwaysdata.net');   // Ejemplo: mysql-tuusuario.alwaysdata.net
define('DB_NAME', 'vinasco_bookcore');        // Nombre de tu base de datos
define('DB_USER', 'vinasco');                   // Usuario de MySQL
define('DB_PASS', 'clase1234');               // Contraseña de MySQL

// Conexión con PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("<div style='font-family:sans-serif;padding:40px;text-align:center;'>
            <h2 style='color:#c00;'>Error de conexión a la base de datos</h2>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
            <p style='color:#666;margin-top:20px;'>Revisa el archivo <strong>config.php</strong> con tus credenciales de AlwaysData.</p>
         </div>");
}
?>
