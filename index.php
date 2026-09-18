<?php
require_once __DIR__ . '/bootstrap.php';
$page_title = 'Inicio';
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
include __DIR__ . '/header.php';
?>
<section class="hero">
  <div class="hero-content">
    <span class="hero-kicker">📚 Sistema bibliotecario</span>
    <h2>Bienvenido al Sistema Bibliotecario</h2>
    <p>Gestiona usuarios, libros y préstamos de manera fácil, organizada y segura desde un solo lugar.</p>
    <div class="hero-quote">“La lectura es el viaje de los que no pueden tomar el tren.”</div>
    <div class="hero-actions">
      <a class="btn-hero" href="libros.php">📚 Ver catálogo</a>
      <a class="btn-hero secondary" href="prestamos.php">↔️ Gestionar préstamos</a>
    </div>
  </div>
</section>

<section class="stats-grid">
  <article class="stat-card blue"><span>Usuarios registrados</span><strong><?= $stats['usuarios'] ?></strong><small>Personas activas en la biblioteca</small></article>
  <article class="stat-card green"><span>Libros disponibles</span><strong><?= $stats['libros'] ?></strong><small>Ejemplares en inventario</small></article>
  <article class="stat-card gold"><span>Préstamos abiertos</span><strong><?= $stats['prestamos'] ?></strong><small>Préstamos activos y vencidos</small></article>
  <article class="stat-card danger"><span>Préstamos vencidos</span><strong><?= $stats['vencidos'] ?></strong><small>Requieren seguimiento</small></article>
</section>

<section class="quick-grid">
  <article class="feature-card"><div class="feature-image feature-users"></div><div class="feature-body"><div class="feature-head"><div class="feature-icon">👤</div><h3>Usuarios</h3></div><p>Registra y administra las personas vinculadas a la biblioteca.</p><a class="feature-link" href="usuarios.php">Ver usuarios →</a></div></article>
  <article class="feature-card"><div class="feature-image feature-books"></div><div class="feature-body"><div class="feature-head"><div class="feature-icon">📖</div><h3>Libros</h3></div><p>Gestiona el catálogo, autores, códigos y ejemplares disponibles.</p><a class="feature-link" href="libros.php">Ver libros →</a></div></article>
  <article class="feature-card"><div class="feature-image feature-loans"></div><div class="feature-body"><div class="feature-head"><div class="feature-icon">↔️</div><h3>Préstamos</h3></div><p>Controla préstamos, fechas de vencimiento y devoluciones.</p><a class="feature-link" href="prestamos.php">Ver préstamos →</a></div></article>
  <article class="feature-card"><div class="feature-image feature-reports"></div><div class="feature-body"><div class="feature-head"><div class="feature-icon">📊</div><h3>Reportes</h3></div><p>Consulta y exporta la información de la biblioteca en CSV.</p><a class="feature-link" href="exportar.php?tipo=prestamos">Ver reportes →</a></div></article>
</section>

<section class="card">
  <div class="card-head"><div><h2>Últimos préstamos</h2><p>Actividad reciente de la biblioteca</p></div><a class="btn" href="prestamos.php">Gestionar préstamos</a></div>
  <div class="table-wrap"><table><thead><tr><th>#</th><th>Usuario</th><th>Libros</th><th>Préstamo</th><th>Vencimiento</th><th>Estado</th></tr></thead><tbody>
  <?php foreach ($recent as $row): ?><tr><td>#<?= (int)$row['id'] ?></td><td><?= e($row['usuario']) ?></td><td><?= e($row['libros']) ?></td><td><?= e($row['fecha_prestamo']) ?></td><td><?= e($row['fecha_vencimiento']) ?></td><td><span class="badge <?= strtolower($row['estado']) ?>"><?= e($row['estado']) ?></span></td></tr><?php endforeach; ?>
  <?php if (!$recent): ?><tr><td colspan="6" class="empty">Todavía no hay préstamos registrados.</td></tr><?php endif; ?>
  </tbody></table></div>
</section>

<section class="quote-band"><blockquote>“Un libro es un sueño que tienes en tus manos.”<cite>— Neil Gaiman</cite></blockquote></section>
<?php include __DIR__ . '/footer.php'; ?>

