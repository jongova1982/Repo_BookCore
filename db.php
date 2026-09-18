<?php
require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    return $pdo;
}

/**
 * Crea las tablas y, si la instalación está completamente vacía,
 * carga datos de demostración para usuarios, libros y préstamos.
 */
function initializeDatabase(): void
{
    $pdo = db();

    $queries = [
        "CREATE TABLE IF NOT EXISTS usuarios (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            cedula VARCHAR(30) NOT NULL UNIQUE,
            telefono VARCHAR(30) NOT NULL,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_usuarios_nombre (nombre),
            INDEX idx_usuarios_activo (activo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS libros (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            titulo VARCHAR(200) NOT NULL,
            autor VARCHAR(180) NOT NULL,
            unidades INT UNSIGNED NOT NULL DEFAULT 0,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_libros_titulo (titulo),
            INDEX idx_libros_autor (autor),
            INDEX idx_libros_activo (activo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS prestamos (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT UNSIGNED NOT NULL,
            fecha_prestamo DATE NOT NULL,
            fecha_vencimiento DATE NOT NULL,
            fecha_devolucion DATE NULL,
            estado ENUM('ACTIVO','DEVUELTO','VENCIDO') NOT NULL DEFAULT 'ACTIVO',
            observaciones VARCHAR(500) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_prestamos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
            INDEX idx_prestamos_usuario (usuario_id),
            INDEX idx_prestamos_estado (estado),
            INDEX idx_prestamos_vencimiento (fecha_vencimiento)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS prestamo_detalle (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            prestamo_id INT UNSIGNED NOT NULL,
            libro_id INT UNSIGNED NOT NULL,
            cantidad INT UNSIGNED NOT NULL DEFAULT 1,
            CONSTRAINT fk_detalle_prestamo FOREIGN KEY (prestamo_id) REFERENCES prestamos(id) ON DELETE CASCADE,
            CONSTRAINT fk_detalle_libro FOREIGN KEY (libro_id) REFERENCES libros(id),
            UNIQUE KEY uk_prestamo_libro (prestamo_id, libro_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS administradores (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(160) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            nombre VARCHAR(120) NOT NULL,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }

    // Crea el administrador inicial si todavía no existe.
    $stmt = $pdo->prepare('SELECT id FROM administradores WHERE email = ? LIMIT 1');
    $stmt->execute([ADMIN_EMAIL]);

    if (!$stmt->fetch()) {
        $insert = $pdo->prepare(
            'INSERT INTO administradores (email, password_hash, nombre) VALUES (?, ?, ?)'
        );
        $insert->execute([
            ADMIN_EMAIL,
            password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT),
            'Administrador',
        ]);
    }

    // -------------------------------------------------------------
    // DATOS DE PRUEBA
    // -------------------------------------------------------------
    // Solo se cargan una vez, cuando usuarios, libros y préstamos
    // están completamente vacíos. Así no se duplican en cada visita.
    $counts = [
        'usuarios'  => (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn(),
        'libros'    => (int) $pdo->query('SELECT COUNT(*) FROM libros')->fetchColumn(),
        'prestamos' => (int) $pdo->query('SELECT COUNT(*) FROM prestamos')->fetchColumn(),
    ];

    if ($counts['usuarios'] > 0 || $counts['libros'] > 0 || $counts['prestamos'] > 0) {
        return;
    }

    $usuarios = [
        ['Juan Carlos Pérez', '1001001001', '3001001001'],
        ['María Fernanda López', '1001001002', '3001001002'],
        ['Andrés Felipe Gómez', '1001001003', '3001001003'],
        ['Laura Marcela Torres', '1001001004', '3001001004'],
        ['Carlos Alberto Rodríguez', '1001001005', '3001001005'],
        ['Diana Carolina Martínez', '1001001006', '3001001006'],
        ['Sebastián Ramírez', '1001001007', '3001001007'],
        ['Valentina Castro', '1001001008', '3001001008'],
        ['Miguel Ángel Herrera', '1001001009', '3001001009'],
        ['Natalia Andrea Moreno', '1001001010', '3001001010'],
    ];

    $libros = [
        ['LIB001', 'Cien años de soledad', 'Gabriel García Márquez', 6],
        ['LIB002', 'El amor en los tiempos del cólera', 'Gabriel García Márquez', 5],
        ['LIB003', 'La vorágine', 'José Eustasio Rivera', 4],
        ['LIB004', 'Don Quijote de la Mancha', 'Miguel de Cervantes', 7],
        ['LIB005', '1984', 'George Orwell', 6],
        ['LIB006', 'Orgullo y prejuicio', 'Jane Austen', 5],
        ['LIB007', 'El principito', 'Antoine de Saint-Exupéry', 8],
        ['LIB008', 'Crónica de una muerte anunciada', 'Gabriel García Márquez', 5],
        ['LIB009', 'Rayuela', 'Julio Cortázar', 4],
        ['LIB010', 'La sombra del viento', 'Carlos Ruiz Zafón', 6],
        ['LIB011', 'El alquimista', 'Paulo Coelho', 7],
        ['LIB012', 'Sapiens', 'Yuval Noah Harari', 5],
        ['LIB013', 'El Hobbit', 'J. R. R. Tolkien', 6],
        ['LIB014', 'Fahrenheit 451', 'Ray Bradbury', 4],
        ['LIB015', 'Ensayo sobre la ceguera', 'José Saramago', 5],
    ];

    $pdo->beginTransaction();

    try {
        // 10 usuarios.
        $insertUsuario = $pdo->prepare(
            'INSERT INTO usuarios (nombre, cedula, telefono, activo) VALUES (?, ?, ?, 1)'
        );

        foreach ($usuarios as $usuario) {
            $insertUsuario->execute($usuario);
        }

        // 15 libros.
        $insertLibro = $pdo->prepare(
            'INSERT INTO libros (codigo, titulo, autor, unidades, activo) VALUES (?, ?, ?, ?, 1)'
        );

        foreach ($libros as $libro) {
            $insertLibro->execute($libro);
        }

        // Mapa de IDs por cédula/código para construir préstamos fácilmente.
        $userIds = [];
        $bookIds = [];

        $userStmt = $pdo->prepare('SELECT id FROM usuarios WHERE cedula = ? LIMIT 1');
        foreach ($usuarios as $usuario) {
            $userStmt->execute([$usuario[1]]);
            $userIds[$usuario[1]] = (int) $userStmt->fetchColumn();
        }

        $bookStmt = $pdo->prepare('SELECT id FROM libros WHERE codigo = ? LIMIT 1');
        foreach ($libros as $libro) {
            $bookStmt->execute([$libro[0]]);
            $bookIds[$libro[0]] = (int) $bookStmt->fetchColumn();
        }

        // 5 préstamos de demostración: activos, vencidos y devuelto.
        $prestamos = [
            [
                'cedula'       => '1001001001',
                'fecha'        => 'CURDATE()',
                'vencimiento'  => 'DATE_ADD(CURDATE(), INTERVAL 7 DAY)',
                'devolucion'   => null,
                'estado'       => 'ACTIVO',
                'observaciones'=> 'Préstamo de demostración vigente.',
                'items'        => [['LIB001', 1], ['LIB005', 1]],
            ],
            [
                'cedula'       => '1001001002',
                'fecha'        => 'DATE_SUB(CURDATE(), INTERVAL 12 DAY)',
                'vencimiento'  => 'DATE_SUB(CURDATE(), INTERVAL 5 DAY)',
                'devolucion'   => null,
                'estado'       => 'VENCIDO',
                'observaciones'=> 'Préstamo de demostración vencido.',
                'items'        => [['LIB002', 1], ['LIB007', 1]],
            ],
            [
                'cedula'       => '1001001003',
                'fecha'        => 'DATE_SUB(CURDATE(), INTERVAL 10 DAY)',
                'vencimiento'  => 'DATE_SUB(CURDATE(), INTERVAL 3 DAY)',
                'devolucion'   => 'DATE_SUB(CURDATE(), INTERVAL 1 DAY)',
                'estado'       => 'DEVUELTO',
                'observaciones'=> 'Préstamo devuelto correctamente.',
                'items'        => [['LIB003', 1], ['LIB008', 1]],
            ],
            [
                'cedula'       => '1001001004',
                'fecha'        => 'DATE_SUB(CURDATE(), INTERVAL 2 DAY)',
                'vencimiento'  => 'DATE_ADD(CURDATE(), INTERVAL 5 DAY)',
                'devolucion'   => null,
                'estado'       => 'ACTIVO',
                'observaciones'=> 'Préstamo de demostración activo.',
                'items'        => [['LIB004', 1], ['LIB010', 2]],
            ],
            [
                'cedula'       => '1001001005',
                'fecha'        => 'DATE_SUB(CURDATE(), INTERVAL 8 DAY)',
                'vencimiento'  => 'DATE_SUB(CURDATE(), INTERVAL 1 DAY)',
                'devolucion'   => null,
                'estado'       => 'VENCIDO',
                'observaciones'=> 'Préstamo vencido de demostración.',
                'items'        => [['LIB011', 1], ['LIB013', 1]],
            ],
        ];

        $insertPrestamo = $pdo->prepare(
            'INSERT INTO prestamos
                (usuario_id, fecha_prestamo, fecha_vencimiento, fecha_devolucion, estado, observaciones)
             VALUES (?, ' . '%DATE1%' . ', ' . '%DATE2%' . ', ' . '%DATE3%' . ', ?, ?)'
        );

        $insertDetalle = $pdo->prepare(
            'INSERT INTO prestamo_detalle (prestamo_id, libro_id, cantidad) VALUES (?, ?, ?)'
        );

        $updateStock = $pdo->prepare(
            'UPDATE libros SET unidades = unidades - ? WHERE id = ?'
        );

        foreach ($prestamos as $prestamo) {
            $usuarioId = $userIds[$prestamo['cedula']] ?? 0;

            if (!$usuarioId) {
                throw new RuntimeException('No fue posible identificar el usuario de prueba.');
            }

            // Se construye la fecha directamente en SQL para que funcione
            // aunque el aplicativo sea instalado en una fecha diferente.
            $sqlPrestamo = str_replace(
                ['%DATE1%', '%DATE2%', '%DATE3%'],
                [
                    $prestamo['fecha'],
                    $prestamo['vencimiento'],
                    $prestamo['devolucion'] === null ? 'NULL' : $prestamo['devolucion'],
                ],
                $insertPrestamo->queryString
            );

            $stmtLoan = $pdo->prepare($sqlPrestamo);
            $stmtLoan->execute([
                $usuarioId,
                $prestamo['estado'],
                $prestamo['observaciones'],
            ]);

            $prestamoId = (int) $pdo->lastInsertId();

            foreach ($prestamo['items'] as [$codigo, $cantidad]) {
                $libroId = $bookIds[$codigo] ?? 0;

                if (!$libroId) {
                    throw new RuntimeException('No fue posible identificar el libro de prueba: ' . $codigo);
                }

                $insertDetalle->execute([$prestamoId, $libroId, $cantidad]);

                // Solo los préstamos pendientes descuentan inventario.
                if ($prestamo['estado'] !== 'DEVUELTO') {
                    $updateStock->execute([$cantidad, $libroId]);
                }
            }
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
