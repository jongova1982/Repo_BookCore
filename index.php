<?php
require_once 'config.php';

$pageTitle = 'Dashboard';
$pageSubtitle = 'Resumen general del sistema';
$currentPage = 'dashboard';

// Estadísticas
$totalLibros = $pdo->query("SELECT COUNT(*) FROM libros")->fetchColumn();
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalPrestamosActivos = $pdo->query("SELECT COUNT(*) FROM prestamos WHERE estado = 'ACTIVO'")->fetchColumn();
$totalUnidades = $pdo->query("SELECT COALESCE(SUM(unidades), 0) FROM libros")->fetchColumn();

// Libros recientes
$librosRecientes = $pdo->query("SELECT * FROM libros ORDER BY id DESC LIMIT 3")->fetchAll();

// Préstamos recientes (adaptado a tu estructura con prestamo_detalle)
$prestamosRecientes = $pdo->query("
    SELECT p.*, u.nombre as usuario_nombre, l.titulo as libro_titulo 
    FROM prestamos p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN prestamo_detalle pd ON pd.prestamo_id = p.id
    JOIN libros l ON pd.libro_id = l.id
    ORDER BY p.id DESC 
    LIMIT 5
")->fetchAll();

require_once 'includes/header.php';
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 card-hover transition-all">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-slate-500 text-sm font-medium">Total Libros</p>
        <p class="text-3xl font-bold text-primary mt-1"><?= $totalLibros ?></p>
      </div>
      <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-accent">
        <i class="fas fa-book text-xl"></i>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 card-hover transition-all">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-slate-500 text-sm font-medium">Total Usuarios</p>
        <p class="text-3xl font-bold text-primary mt-1"><?= $totalUsuarios ?></p>
      </div>
      <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center text-secondary">
        <i class="fas fa-users text-xl"></i>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 card-hover transition-all">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-slate-500 text-sm font-medium">Préstamos Activos</p>
        <p class="text-3xl font-bold text-primary mt-1"><?= $totalPrestamosActivos ?></p>
      </div>
      <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
        <i class="fas fa-handshake text-xl"></i>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 card-hover transition-all">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-slate-500 text-sm font-medium">Unidades Disponibles</p>
        <p class="text-3xl font-bold text-primary mt-1"><?= $totalUnidades ?></p>
      </div>
      <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
        <i class="fas fa-boxes text-xl"></i>
      </div>
    </div>
  </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-primary">Libros Recientes</h3>
      <a href="libros.php" class="text-sm text-secondary hover:underline">Ver todos</a>
    </div>
    <div class="p-4">
      <?php if (empty($librosRecientes)): ?>
        <p class="text-slate-400 text-sm p-3">Sin libros</p>
      <?php else: ?>
        <?php foreach ($librosRecientes as $l): ?>
          <div class="flex items-center gap-3 p-3 hover:bg-slate-50 rounded-lg">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-accent">
              <i class="fas fa-book"></i>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-sm truncate"><?= htmlspecialchars($l['titulo']) ?></p>
              <p class="text-xs text-slate-500"><?= htmlspecialchars($l['autor']) ?> · <?= $l['unidades'] ?> unidades</p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-primary">Préstamos Recientes</h3>
      <a href="prestamos.php" class="text-sm text-secondary hover:underline">Ver todos</a>
    </div>
    <div class="p-4">
      <?php if (empty($prestamosRecientes)): ?>
        <p class="text-slate-400 text-sm p-3">Sin préstamos</p>
      <?php else: ?>
        <?php foreach ($prestamosRecientes as $p): ?>
          <div class="flex items-center gap-3 p-3 hover:bg-slate-50 rounded-lg">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center text-secondary">
              <i class="fas fa-handshake"></i>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-sm truncate"><?= htmlspecialchars($p['usuario_nombre']) ?> → <?= htmlspecialchars($p['libro_titulo']) ?></p>
              <p class="text-xs text-slate-500">
                <?= date('d M Y', strtotime($p['fecha_prestamo'])) ?> · 
                <span class="<?= $p['estado'] === 'ACTIVO' ? 'text-emerald-600' : 'text-slate-400' ?>"><?= $p['estado'] ?></span>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="mt-8 bg-gradient-to-r from-primary to-secondary rounded-xl p-6 text-white">
  <h3 class="text-xl font-bold mb-2">Acciones Rápidas</h3>
  <p class="text-blue-100 mb-4">Gestiona tu biblioteca de forma eficiente</p>
  <div class="flex flex-wrap gap-3">
    <a href="usuarios.php?action=nuevo" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm font-medium transition-all">
      <i class="fas fa-user-plus mr-2"></i>Nuevo Usuario
    </a>
    <a href="libros.php?action=nuevo" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm font-medium transition-all">
      <i class="fas fa-book-medical mr-2"></i>Nuevo Libro
    </a>
    <a href="prestamos.php?action=nuevo" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm font-medium transition-all">
      <i class="fas fa-plus-circle mr-2"></i>Nuevo Préstamo
    </a>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
