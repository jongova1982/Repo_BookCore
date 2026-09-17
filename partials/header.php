<?php
require_login();
$flash = consume_flash();
$current = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title ?? 'Biblioteca Pro') ?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">B</div>
      <div><strong>Biblioteca Pro</strong><small>Gestión en la nube</small></div>
    </div>
    <nav>
      <a class="<?= $current === 'index.php' ? 'active' : '' ?>" href="index.php">📊 Dashboard</a>
      <a class="<?= $current === 'usuarios.php' ? 'active' : '' ?>" href="usuarios.php">👥 Usuarios</a>
      <a class="<?= $current === 'libros.php' ? 'active' : '' ?>" href="libros.php">📚 Libros</a>
      <a class="<?= $current === 'prestamos.php' ? 'active' : '' ?>" href="prestamos.php">🔄 Préstamos</a>
      <a href="exportar.php?tipo=usuarios">⬇️ Exportar usuarios</a>
      <a href="exportar.php?tipo=libros">⬇️ Exportar libros</a>
      <a href="exportar.php?tipo=prestamos">⬇️ Exportar préstamos</a>
    </nav>
    <div class="sidebar-footer">
      <div class="admin-chip"><span class="dot"></span><?= e(current_admin()['nombre'] ?? 'Administrador') ?></div>
      <a class="logout" href="logout.php">Cerrar sesión</a>
    </div>
  </aside>
  <main class="main-content">
    <header class="topbar">
      <div><span class="eyebrow">Biblioteca</span><h1><?= e($page_title ?? 'Panel') ?></h1></div>
      <div class="top-actions"><span class="online">● Sistema activo</span></div>
    </header>
    <?php if ($flash): ?>
      <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
