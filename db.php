<?php
$host = 'mysql-jaloji.alwaysdata.net';
$dbname = 'jaloji_repobookcorejacke';
$user = 'jaloji';
$password = 'clase1234';

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

try {
    $conexion = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $error) {
    die('Error de conexion a la base de datos: ' . $error->getMessage());
}

function getConnection(): PDO
{
    global $conexion;
    return $conexion;
}
