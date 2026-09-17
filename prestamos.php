<?php
require_once 'config.php';

$pageTitle = 'Gestión de Préstamos';
$pageSubtitle = 'Registra y controla los préstamos de libros';
$currentPage = 'prestamos';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'guardar') {
        $usuario_id = (int)($_POST['usuario_id'] ?? 0);
        $libro_id = (int)($_POST['libro_id'] ?? 0);

        if ($usuario_id && $libro_id) {
            try {
                // Verificar unidades
                $stmt = $pdo->prepare("SELECT unidades FROM libros WHERE id=?");
                $stmt->execute([$libro_id]);
                $libro = $stmt->fetch();

                if (!$libro || $libro['unidades'] <= 0) {
                    $error = 'No hay unidades disponibles de este libro';
                } else {
                    $pdo->beginTransaction();

                    // Crear préstamo
                    $stmt = $pdo->prepare("INSERT INTO prestamos (usuario_id, libro_id, fecha, estado) VALUES (?, ?, CURDATE(), 'activo')");
                    $stmt->execute([$usuario_id, $libro_id]);

                    // Restar unidad
                    $stmt = $pdo->prepare("UPDATE libros SET unidades = unidades - 1 WHERE id=?");
                    $stmt->execute([$libro_id]);

                    $pdo->commit();
                    $mensaje = 'Préstamo registrado correctamente';
                }
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $error = 'Error: ' . $e->getMessage();
            }
        } else {
            $error = 'Selecciona usuario y libro';
        }
    }

    if ($action === 'devolver') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT libro_id, estado FROM prestamos WHERE id=?");
            $stmt->execute([$id]);
            $prestamo = $stmt->fetch();

            if ($prestamo && $prestamo['estado'] === 'activo') {
                $stmt = $pdo->prepare("UPDATE prestamos SET estado='devuelto' WHERE id=?");
                $stmt->execute([$id]);

                $stmt = $pdo->prepare("UPDATE libros SET unidades = unidades + 1 WHERE id=?");
                $stmt->execute([$prestamo['libro_id']]);

                $pdo->commit();
                $mensaje = 'Préstamo marcado como devuelto';
            } else {
                $error = 'El préstamo ya está devuelto o no existe';
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = 'Error al devolver';
        }
    }

    if ($action === 'eliminar') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM prestamos WHERE id=?");
            $stmt->execute([$id]);
            $mensaje = 'Registro eliminado';
        } catch (PDOException $e) {
            $error = 'No se pudo eliminar';
        }
    }
}

$search = $_GET['q'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("
        SELECT p.*, u.nombre as usuario_nombre, l.titulo as libro_titulo 
        FROM prestamos p
        JOIN usuarios u ON p.usuario_id = u.id
        JOIN libros l ON p.libro_id = l.id
        WHERE u.nombre LIKE ? OR l.titulo LIKE ?
        ORDER BY p.id DESC
    ");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, u.nombre as usuario_nombre, l.titulo as libro_titulo 
        FROM prestamos p
        JOIN usuarios u ON p.usuario_id = u.id
        JOIN libros l ON p.libro_id = l.id
        ORDER BY p.id DESC
    ");
}
$prestamos = $stmt->fetchAll();

// Para el modal
$usuarios = $pdo->query("SELECT id, nombre, cedula FROM usuarios ORDER BY nombre")->fetchAll();
$librosDisponibles = $pdo->query("SELECT id, codigo, titulo, unidades FROM libros WHERE unidades > 0 ORDER BY titulo")->fetchAll();

$mostrarModal = isset($_GET['action']) && $_GET['action'] === 'nuevo';

require_once 'includes/header.php';
?>

<?php if ($mensaje): ?>
  <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-2">
    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensaje) ?>
  </div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<div class="flex flex-col sm:flex-row gap-4 mb-6 items-start sm:items-center justify-between">
  <form method="GET" class="relative max-w-md w-full">
    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por usuario o libro..." 
           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
  </form>
  <a href="prestamos.php?action=nuevo" class="bg-secondary hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition-all whitespace-nowrap">
    <i class="fas fa-plus-circle"></i> Nuevo Préstamo
  </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
  <table class="w-full">
    <thead class="bg-slate-50 border-b border-slate-100">
      <tr>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Usuario</th>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Libro</th>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha</th>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
        <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <?php if (empty($prestamos)): ?>
        <tr>
          <td colspan="5" class="px-6 py-12 text-center text-slate-400">
            <i class="fas fa-handshake text-4xl mb-3 block"></i>
            No hay préstamos registrados
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($prestamos as $p): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-4 font-medium"><?= htmlspecialchars($p['usuario_nombre']) ?></td>
            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($p['libro_titulo']) ?></td>
            <td class="px-6 py-4 text-slate-600"><?= date('d M Y', strtotime($p['fecha'])) ?></td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $p['estado'] === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                <?= $p['estado'] === 'activo' ? 'Activo' : 'Devuelto' ?>
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <?php if ($p['estado'] === 'activo'): ?>
                <form method="POST" class="inline" onsubmit="return confirm('¿Marcar como devuelto?')">
                  <input type="hidden" name="action" value="devolver">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" class="text-secondary hover:text-teal-700 mr-3" title="Devolver">
                    <i class="fas fa-check-circle"></i> Devolver
                  </button>
                </form>
              <?php endif; ?>
              <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar este registro?')">
                <input type="hidden" name="action" value="eliminar">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="text-red-500 hover:text-red-700" title="Eliminar">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if ($mostrarModal): ?>
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl w-full max-w-md mx-4 shadow-2xl">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-bold text-lg text-primary">Nuevo Préstamo</h3>
      <a href="prestamos.php" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" class="p-6 space-y-4">
      <input type="hidden" name="action" value="guardar">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Usuario</label>
        <select name="usuario_id" required class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
          <option value="">Seleccionar usuario...</option>
          <?php foreach ($usuarios as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['cedula']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Libro</label>
        <select name="libro_id" required class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
          <option value="">Seleccionar libro...</option>
          <?php foreach ($librosDisponibles as $l): ?>
            <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['titulo']) ?> (<?= htmlspecialchars($l['codigo']) ?>) - <?= $l['unidades'] ?> disp.</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex gap-3 pt-2">
        <a href="prestamos.php" class="flex-1 px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-center transition-all">Cancelar</a>
        <button type="submit" class="flex-1 px-4 py-2 bg-secondary hover:bg-teal-700 text-white rounded-lg font-medium transition-all">Registrar</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
