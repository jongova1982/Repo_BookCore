<?php
require_once 'config.php';

$pageTitle = 'Gestión de Libros';
$pageSubtitle = 'Administra el catálogo de la biblioteca';
$currentPage = 'libros';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'guardar') {
        $id = $_POST['id'] ?? null;
        $codigo = trim($_POST['codigo'] ?? '');
        $titulo = trim($_POST['titulo'] ?? '');
        $autor = trim($_POST['autor'] ?? '');
        $unidades = (int)($_POST['unidades'] ?? 0);

        if ($codigo && $titulo && $autor) {
            try {
                if ($id) {
                    $stmt = $pdo->prepare("UPDATE libros SET codigo=?, titulo=?, autor=?, unidades=? WHERE id=?");
                    $stmt->execute([$codigo, $titulo, $autor, $unidades, $id]);
                    $mensaje = 'Libro actualizado correctamente';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO libros (codigo, titulo, autor, unidades) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$codigo, $titulo, $autor, $unidades]);
                    $mensaje = 'Libro creado correctamente';
                }
            } catch (PDOException $e) {
                $error = 'Error: ' . ($e->getCode() == 23000 ? 'El código ya existe' : $e->getMessage());
            }
        } else {
            $error = 'Todos los campos son obligatorios';
        }
    }

    if ($action === 'eliminar') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM libros WHERE id=?");
            $stmt->execute([$id]);
            $mensaje = 'Libro eliminado';
        } catch (PDOException $e) {
            $error = 'No se pudo eliminar';
        }
    }
}

$search = $_GET['q'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM libros WHERE titulo LIKE ? OR autor LIKE ? OR codigo LIKE ? ORDER BY titulo");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM libros ORDER BY titulo");
}
$libros = $stmt->fetchAll();

$editLibro = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM libros WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editLibro = $stmt->fetch();
}

$mostrarModal = isset($_GET['action']) && $_GET['action'] === 'nuevo' || $editLibro;

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
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por título, autor o código..." 
           class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
  </form>
  <a href="libros.php?action=nuevo" class="bg-secondary hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition-all whitespace-nowrap">
    <i class="fas fa-book-medical"></i> Nuevo Libro
  </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
  <table class="w-full">
    <thead class="bg-slate-50 border-b border-slate-100">
      <tr>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Código</th>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Título</th>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Autor</th>
        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Unidades</th>
        <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <?php if (empty($libros)): ?>
        <tr>
          <td colspan="5" class="px-6 py-12 text-center text-slate-400">
            <i class="fas fa-book text-4xl mb-3 block"></i>
            No hay libros registrados
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($libros as $l): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-4 font-mono text-sm text-slate-600"><?= htmlspecialchars($l['codigo']) ?></td>
            <td class="px-6 py-4 font-medium"><?= htmlspecialchars($l['titulo']) ?></td>
            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($l['autor']) ?></td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $l['unidades'] > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
                <?= $l['unidades'] ?>
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <a href="libros.php?edit=<?= $l['id'] ?>" class="text-accent hover:text-blue-700 mr-3" title="Editar">
                <i class="fas fa-edit"></i>
              </a>
              <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar este libro?')">
                <input type="hidden" name="action" value="eliminar">
                <input type="hidden" name="id" value="<?= $l['id'] ?>">
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
      <h3 class="font-bold text-lg text-primary"><?= $editLibro ? 'Editar Libro' : 'Nuevo Libro' ?></h3>
      <a href="libros.php" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" class="p-6 space-y-4">
      <input type="hidden" name="action" value="guardar">
      <?php if ($editLibro): ?>
        <input type="hidden" name="id" value="<?= $editLibro['id'] ?>">
      <?php endif; ?>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Código</label>
        <input type="text" name="codigo" required value="<?= htmlspecialchars($editLibro['codigo'] ?? '') ?>"
               class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Título</label>
        <input type="text" name="titulo" required value="<?= htmlspecialchars($editLibro['titulo'] ?? '') ?>"
               class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Autor</label>
        <input type="text" name="autor" required value="<?= htmlspecialchars($editLibro['autor'] ?? '') ?>"
               class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Unidades</label>
        <input type="number" name="unidades" min="0" required value="<?= htmlspecialchars($editLibro['unidades'] ?? '0') ?>"
               class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary/50">
      </div>
      <div class="flex gap-3 pt-2">
        <a href="libros.php" class="flex-1 px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-center transition-all">Cancelar</a>
        <button type="submit" class="flex-1 px-4 py-2 bg-secondary hover:bg-teal-700 text-white rounded-lg font-medium transition-all">Guardar</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
