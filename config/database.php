<?php
declare(strict_types=1);

/**
 * Configuracion de base de datos.
 *
 * En produccion, configure las variables BOOKCORE_DB_* en Alwaysdata o cree
 * config/local.php a partir de config/local.php.example. local.php esta
 * ignorado por Git para evitar publicar credenciales.
 */
$databaseConfig = [
    'host' => getenv('BOOKCORE_DB_HOST') ?: 'mysql-jongox.alwaysdata.net',
    'name' => getenv('BOOKCORE_DB_NAME') ?: 'jongox_bookcore_db',
    'user' => getenv('BOOKCORE_DB_USER') ?: 'jongox',
    'password' => getenv('BOOKCORE_DB_PASSWORD') ?: 'jongox@@@1234',
    'charset' => 'utf8mb4',
];

$localConfigFile = __DIR__ . '/local.php';
if (is_file($localConfigFile)) {
    $localConfig = require $localConfigFile;
    if (is_array($localConfig)) {
        $databaseConfig = array_replace($databaseConfig, $localConfig);
    }
}

function database(): PDO
{
    static $connection = null;
    global $databaseConfig;

    if ($connection instanceof PDO) {
        return $connection;
    }

    if ($databaseConfig['password'] === '') {
        throw new RuntimeException(
            'Falta configurar BOOKCORE_DB_PASSWORD o config/local.php.'
        );
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $databaseConfig['host'],
        $databaseConfig['name'],
        $databaseConfig['charset']
    );

    $connection = new PDO($dsn, $databaseConfig['user'], $databaseConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $connection;
}

