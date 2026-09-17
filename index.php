<?php
require_once __DIR__ . '/bootstrap.php';
$page_title = 'Dashboard';
$pdo = db();
$stats = [
    'usuarios' => (int)$pdo->query("SELECT COUNT(*) FROM usuarios WHERE activo=1")->fetchColumn(),
    'libros' => (int)$pdo->query("SELECT COALESCE(SUM(unidades),0) FROM libros WHERE activo=1")->fetchColumn(),
    'prestamos' => (int)$pdo->query("SELECT COUNT(*) FROM prestamos WHERE estado IN ('ACTIVO','VENCIDO')")->fetchColumn(),
    'vencidos' => (int)$pdo->query("SELECT COUNT(*) FROM prestamos WHERE estado='VENCIDO'")->fetchColumn(),
];
$recent = $pdo->query("SELECT p.id,p.fecha_prestamo,p.fecha_vencimiento,p.estado,u.nombre usuario,
  GROUP_CONCAT(CONCAT(l.titulo,' x',d.cantidad) ORDER BY l.titulo SEPARATOR ', ') libros
  FROM prestamos p JOIN usuarios u ON u.id=p.usuario_id
  JOIN prestamo_detalle d ON d.prestamo_id=p.id JOIN libros l ON l.id=d.libro_id
  GROUP BY p.id ORDER BY p.id DESC LIMIT 8")->fetchAll();
include __DIR__ . '/partials/header.php';
?>
<section class="stats-grid">
  <article class="stat-card"><span>Usuarios activos</span><strong><?= $stats['usuarios'] ?></strong><small>Personas registradas</small></article>
  <article class="stat-card"><span>Unidades disponibles</span><strong><?= $stats['libros'] ?></strong><small>Ejemplares en inventario</small></article>
  <article class="stat-card"><span>Préstamos abiertos</span><strong><?= $stats['prestamos'] ?></strong><small>Incluye vencidos</small></article>
  <article class="stat-card danger"><span>Vencidos</span><strong><?= $stats['vencidos'] ?></strong><small>Requieren seguimiento</small></article>
</section>
<section class="card">
  <div class="card-head"><div><h2>Últimos préstamos</h2><p>Actividad reciente de la biblioteca</p></div><a class="btn" href="prestamos.php">Gestionar préstamos</a></div>
  <div class="table-wrap"><table><thead><tr><th>#</th><th>Usuario</th><th>Libros</th><th>Préstamo</th><th>Vencimiento</th><th>Estado</th></tr></thead><tbody>
  <?php foreach ($recent as $row): ?><tr><td>#<?= (int)$row['id'] ?></td><td><?= e($row['usuario']) ?></td><td><?= e($row['libros']) ?></td><td><?= e($row['fecha_prestamo']) ?></td><td><?= e($row['fecha_vencimiento']) ?></td><td><span class="badge <?= strtolower($row['estado']) ?>"><?= e($row['estado']) ?></span></td></tr><?php endforeach; ?>
  <?php if (!$recent): ?><tr><td colspan="6" class="empty">Todavía no hay préstamos registrados.</td></tr><?php endif; ?>
  </tbody></table></div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
