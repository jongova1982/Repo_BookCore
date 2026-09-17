<?php
require_once 'config.php';

$pageTitle = 'Gestión de Usuarios';
$pageSubtitle = 'Administra los usuarios de la biblioteca';
$currentPage = 'usuarios';

// Procesar acciones
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'guardar') {
        $id = $_POST['id'] ?? null;
        $nombre = trim($_POST['nombre'] ?? '');
        $cedula = trim($_POST['cedula'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');

        if ($nombre && $cedula && $telefono) {
            try {
                if ($id) {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, cedula=?, telefono=? WHERE id=?");
                    $stmt->execute([$nombre, $cedula, $telefono, $id]);
                    $mensaje = 'Usuario actualizado correctamente';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, cedula, telefono) VALUES (?, ?, ?)");
                    $stmt->execute([$nombre, $cedula, $telefono]);
                    $mensaje = 'Usuario creado correctamente';
                }
            } catch (PDOException $e) {
                $error = 'Error: ' . ($e->getCode() == 23000 ? 'La cédula ya existe' : $e->getMessage());
            }
        } else {
            $error = 'Todos los campos son obligatorios';
        }
    }

    if ($action === 'eliminar') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id=?");
            $stmt->execute([$id]);
            $mensaje = 'Usuario eliminado';
        } catch (PDOException $e) {
            $error = 'No se pudo eliminar';
        }
    }
}

// Buscar
$search = $_GET['q'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre LIKE ? OR cedula LIKE ? ORDER BY nombre");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY nombre");
}
$usuarios = $stmt->fetchAll();

// Editar
$editUsuario = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editUsuario = $stmt->fetch();
}

$mostrarModal = isset($_GET['action']) && $_GET['action'] === 'nuevo' || $editUsuario;

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
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por nombre o cédula..." 
           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
  </form>
  <a href="usuarios.php?action=nuevo" class="bg-secondary hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition-all whitespace-nowrap">
    <i class="fas fa-user-plus"></i> Nuevo Usuario
  </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
  <table class="w-full">
    <thead class="bg-slate-50 border-b border-slate-100">
      <tr>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nombre</th>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Cédula</th>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Teléfono</th>
        <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <?php if (empty($usuarios)): ?>
        <tr>
          <td colspan="4" class="px-6 py-12 text-center text-slate-400">
            <i class="fas fa-users text-4xl mb-3 block"></i>
            No hay usuarios registrados
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($usuarios as $u): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-teal-100 rounded-full flex items-center justify-center text-secondary font-semibold text-sm">
                  <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                </div>
                <span class="font-medium"><?= htmlspecialchars($u['nombre']) ?></span>
              </div>
            </td>
            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($u['cedula']) ?></td>
            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($u['telefono']) ?></td>
            <td class="px-6 py-4 text-right">
              <a href="usuarios.php?edit=<?= $u['id'] ?>" class="text-accent hover:text-blue-700 mr-3" title="Editar">
                <i class="fas fa-edit"></i>
              </a>
              <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                <input type="hidden" name="action" value="eliminar">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
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

<!-- Modal -->
<?php if ($mostrarModal): ?>
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl w-full max-w-md mx-4 shadow-2xl">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-bold text-lg text-primary"><?= $editUsuario ? 'Editar Usuario' : 'Nuevo Usuario' ?></h3>
      <a href="usuarios.php" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" class="p-6 space-y-4">
      <input type="hidden" name="action" value="guardar">
      <?php if ($editUsuario): ?>
        <input type="hidden" name="id" value="<?= $editUsuario['id'] ?>">
      <?php endif; ?>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nombre completo</label>
        <input type="text" name="nombre" required value="<?= htmlspecialchars($editUsuario['nombre'] ?? '') ?>"
               class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Cédula</label>
        <input type="text" name="cedula" required value="<?= htmlspecialchars($editUsuario['cedula'] ?? '') ?>"
               class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono</label>
        <input type="text" name="telefono" required value="<?= htmlspecialchars($editUsuario['telefono'] ?? '') ?>"
               class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
      </div>
      <div class="flex gap-3 pt-2">
        <a href="usuarios.php" class="flex-1 px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-center transition-all">Cancelar</a>
        <button type="submit" class="flex-1 px-4 py-2 bg-secondary hover:bg-teal-700 text-white rounded-lg font-medium transition-all">Guardar</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
