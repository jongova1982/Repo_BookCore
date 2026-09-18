<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require __DIR__ . '/config/database.php';

function respond(array $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        respond(['ok' => false, 'message' => 'El contenido JSON no es valido.'], 400);
    }
    return $data;
}

function clean(mixed $value, int $max = 180): string
{
    return mb_substr(trim((string) $value), 0, $max);
}

function requireFields(array $data, array $fields): void
{
    foreach ($fields as $field => $label) {
        if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
            respond(['ok' => false, 'message' => "El campo {$label} es obligatorio."], 422);
        }
    }
}

function verifyCsrf(): void
{
    $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';
    if ($stored === '' || !hash_equals($stored, $sent)) {
        respond(['ok' => false, 'message' => 'La sesion vencio. Recarga la pagina.'], 419);
    }
}

function databaseError(PDOException $error): never
{
    $duplicate = (string) $error->getCode() === '23000';
    respond([
        'ok' => false,
        'message' => $duplicate
            ? 'El codigo o identificacion ya se encuentra registrado.'
            : 'No fue posible completar la operacion en la base de datos.',
    ], $duplicate ? 409 : 500);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$resource = strtolower(clean($_GET['resource'] ?? 'dashboard', 30));

try {
    $db = database();

    if ($method === 'GET' && $resource === 'dashboard') {
        $stats = $db->query("SELECT
            (SELECT COUNT(*) FROM BKusuarios_db WHERE activo = 1) AS usuarios,
            (SELECT COUNT(*) FROM BKlibros_db WHERE activo = 1) AS libros,
            (SELECT COUNT(*) FROM BKprestamos_db WHERE estado = 'activo') AS prestamos,
            (SELECT COALESCE(SUM(unidades), 0) FROM BKlibros_db WHERE activo = 1) AS unidades")->fetch();
        $recent = $db->query("SELECT p.id_prestamos, p.fecha_prestamo, p.estado,
                l.titulo, u.Nombre AS usuario
            FROM BKprestamos_db p
            INNER JOIN BKlibros_db l ON l.codigo = p.id_codigo
            INNER JOIN BKusuarios_db u ON u.cedula = p.id_usuario
            ORDER BY p.fecha_prestamo DESC LIMIT 5")->fetchAll();
        respond(['ok' => true, 'stats' => $stats, 'recent' => $recent]);
    }

    if ($method === 'GET' && $resource === 'usuarios') {
        $rows = $db->query("SELECT Nombre, cedula, telefono FROM BKusuarios_db WHERE activo = 1 ORDER BY Nombre")->fetchAll();
        respond(['ok' => true, 'data' => $rows]);
    }

    if ($method === 'GET' && $resource === 'libros') {
        $rows = $db->query("SELECT codigo, titulo, autor, unidades FROM BKlibros_db WHERE activo = 1 ORDER BY titulo")->fetchAll();
        respond(['ok' => true, 'data' => $rows]);
    }

    if ($method === 'GET' && $resource === 'prestamos') {
        $rows = $db->query("SELECT p.id_prestamos, p.id_codigo, p.id_usuario,
                p.fecha_prestamo, p.fecha_devolucion, p.estado,
                l.titulo, u.Nombre AS usuario
            FROM BKprestamos_db p
            INNER JOIN BKlibros_db l ON l.codigo = p.id_codigo
            INNER JOIN BKusuarios_db u ON u.cedula = p.id_usuario
            ORDER BY (p.estado = 'activo') DESC, p.fecha_prestamo DESC")->fetchAll();
        respond(['ok' => true, 'data' => $rows]);
    }

    if ($method !== 'GET') {
        verifyCsrf();
        $data = body();
    }

    if ($method === 'POST' && $resource === 'usuarios') {
        requireFields($data, ['Nombre' => 'nombre', 'cedula' => 'cedula', 'telefono' => 'telefono']);
        $stmt = $db->prepare("INSERT INTO BKusuarios_db (Nombre, cedula, telefono, activo)
            VALUES (:nombre, :cedula, :telefono, 1)
            ON DUPLICATE KEY UPDATE Nombre = VALUES(Nombre), telefono = VALUES(telefono), activo = 1");
        $stmt->execute(['nombre' => clean($data['Nombre'], 120), 'cedula' => clean($data['cedula'], 24), 'telefono' => clean($data['telefono'], 24)]);
        respond(['ok' => true, 'message' => 'Usuario guardado correctamente.'], 201);
    }

    if ($method === 'PUT' && $resource === 'usuarios') {
        requireFields($data, ['original' => 'cedula original', 'Nombre' => 'nombre', 'cedula' => 'cedula', 'telefono' => 'telefono']);
        $stmt = $db->prepare("UPDATE BKusuarios_db SET Nombre = :nombre, cedula = :cedula, telefono = :telefono WHERE cedula = :original AND activo = 1");
        $stmt->execute(['nombre' => clean($data['Nombre'], 120), 'cedula' => clean($data['cedula'], 24), 'telefono' => clean($data['telefono'], 24), 'original' => clean($data['original'], 24)]);
        respond(['ok' => true, 'message' => 'Usuario actualizado correctamente.']);
    }

    if ($method === 'DELETE' && $resource === 'usuarios') {
        requireFields($data, ['cedula' => 'cedula']);
        $active = $db->prepare("SELECT COUNT(*) FROM BKprestamos_db WHERE id_usuario = ? AND estado = 'activo'");
        $active->execute([clean($data['cedula'], 24)]);
        if ((int) $active->fetchColumn() > 0) {
            respond(['ok' => false, 'message' => 'No puedes eliminar un usuario con prestamos activos.'], 409);
        }
        $stmt = $db->prepare("UPDATE BKusuarios_db SET activo = 0 WHERE cedula = ?");
        $stmt->execute([clean($data['cedula'], 24)]);
        respond(['ok' => true, 'message' => 'Usuario eliminado del listado.']);
    }

    if ($method === 'POST' && $resource === 'libros') {
        requireFields($data, ['codigo' => 'codigo', 'titulo' => 'titulo', 'autor' => 'autor', 'unidades' => 'unidades']);
        $unidades = filter_var($data['unidades'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($unidades === false) respond(['ok' => false, 'message' => 'Las unidades deben ser un numero igual o mayor que cero.'], 422);
        $stmt = $db->prepare("INSERT INTO BKlibros_db (codigo, titulo, autor, unidades, activo)
            VALUES (:codigo, :titulo, :autor, :unidades, 1)
            ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), autor = VALUES(autor), unidades = VALUES(unidades), activo = 1");
        $stmt->execute(['codigo' => clean($data['codigo'], 32), 'titulo' => clean($data['titulo'], 180), 'autor' => clean($data['autor'], 120), 'unidades' => $unidades]);
        respond(['ok' => true, 'message' => 'Libro guardado correctamente.'], 201);
    }

    if ($method === 'PUT' && $resource === 'libros') {
        requireFields($data, ['original' => 'codigo original', 'codigo' => 'codigo', 'titulo' => 'titulo', 'autor' => 'autor', 'unidades' => 'unidades']);
        $unidades = filter_var($data['unidades'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($unidades === false) respond(['ok' => false, 'message' => 'Las unidades deben ser un numero igual o mayor que cero.'], 422);
        $stmt = $db->prepare("UPDATE BKlibros_db SET codigo = :codigo, titulo = :titulo, autor = :autor, unidades = :unidades WHERE codigo = :original AND activo = 1");
        $stmt->execute(['codigo' => clean($data['codigo'], 32), 'titulo' => clean($data['titulo'], 180), 'autor' => clean($data['autor'], 120), 'unidades' => $unidades, 'original' => clean($data['original'], 32)]);
        respond(['ok' => true, 'message' => 'Libro actualizado correctamente.']);
    }

    if ($method === 'DELETE' && $resource === 'libros') {
        requireFields($data, ['codigo' => 'codigo']);
        $active = $db->prepare("SELECT COUNT(*) FROM BKprestamos_db WHERE id_codigo = ? AND estado = 'activo'");
        $active->execute([clean($data['codigo'], 32)]);
        if ((int) $active->fetchColumn() > 0) {
            respond(['ok' => false, 'message' => 'No puedes eliminar un libro con prestamos activos.'], 409);
        }
        $stmt = $db->prepare("UPDATE BKlibros_db SET activo = 0 WHERE codigo = ?");
        $stmt->execute([clean($data['codigo'], 32)]);
        respond(['ok' => true, 'message' => 'Libro eliminado del catalogo.']);
    }

    if ($method === 'POST' && $resource === 'prestamos') {
        requireFields($data, ['id_codigo' => 'libro', 'id_usuario' => 'usuario']);
        $codigo = clean($data['id_codigo'], 32);
        $usuario = clean($data['id_usuario'], 24);
        $db->beginTransaction();
        $book = $db->prepare("SELECT unidades FROM BKlibros_db WHERE codigo = ? AND activo = 1 FOR UPDATE");
        $book->execute([$codigo]);
        $unidades = $book->fetchColumn();
        if ($unidades === false || (int) $unidades < 1) {
            $db->rollBack();
            respond(['ok' => false, 'message' => 'El libro seleccionado no tiene unidades disponibles.'], 409);
        }
        $user = $db->prepare("SELECT 1 FROM BKusuarios_db WHERE cedula = ? AND activo = 1");
        $user->execute([$usuario]);
        if (!$user->fetchColumn()) {
            $db->rollBack();
            respond(['ok' => false, 'message' => 'El usuario seleccionado no esta disponible.'], 404);
        }
        $duplicate = $db->prepare("SELECT 1 FROM BKprestamos_db WHERE id_codigo = ? AND id_usuario = ? AND estado = 'activo'");
        $duplicate->execute([$codigo, $usuario]);
        if ($duplicate->fetchColumn()) {
            $db->rollBack();
            respond(['ok' => false, 'message' => 'Este usuario ya tiene prestado el mismo libro.'], 409);
        }
        $stmt = $db->prepare("INSERT INTO BKprestamos_db (id_codigo, id_usuario) VALUES (?, ?)");
        $stmt->execute([$codigo, $usuario]);
        $db->prepare("UPDATE BKlibros_db SET unidades = unidades - 1 WHERE codigo = ?")->execute([$codigo]);
        $db->commit();
        respond(['ok' => true, 'message' => 'Prestamo registrado correctamente.'], 201);
    }

    if ($method === 'PUT' && $resource === 'prestamos' && ($_GET['action'] ?? '') === 'devolver') {
        requireFields($data, ['id_prestamos' => 'prestamo']);
        $id = filter_var($data['id_prestamos'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) respond(['ok' => false, 'message' => 'El prestamo no es valido.'], 422);
        $db->beginTransaction();
        $loan = $db->prepare("SELECT id_codigo, estado FROM BKprestamos_db WHERE id_prestamos = ? FOR UPDATE");
        $loan->execute([$id]);
        $row = $loan->fetch();
        if (!$row || $row['estado'] !== 'activo') {
            $db->rollBack();
            respond(['ok' => false, 'message' => 'El prestamo ya fue devuelto o no existe.'], 409);
        }
        $db->prepare("UPDATE BKprestamos_db SET estado = 'devuelto', fecha_devolucion = NOW() WHERE id_prestamos = ?")->execute([$id]);
        $db->prepare("UPDATE BKlibros_db SET unidades = unidades + 1 WHERE codigo = ?")->execute([$row['id_codigo']]);
        $db->commit();
        respond(['ok' => true, 'message' => 'Devolucion registrada e inventario actualizado.']);
    }

    respond(['ok' => false, 'message' => 'Ruta no encontrada.'], 404);
} catch (PDOException $error) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    databaseError($error);
} catch (Throwable $error) {
    respond(['ok' => false, 'message' => $error->getMessage()], 500);
}
