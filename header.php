<?php
require_login();
$flash = consume_flash();
$current = basename($_SERVER['PHP_SELF']);
$admin = current_admin();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#102a43">
  <title><?= e($page_title ?? 'Biblioteca Pro') ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">📖</div>
      <div><strong>Biblioteca Pro</strong><small>Gestión de Biblioteca</small></div>
    </div>
    <nav>
      <a class="<?= $current === 'index.php' ? 'active' : '' ?>" href="index.php">🏠 <span>Inicio</span></a>
      <a class="<?= $current === 'usuarios.php' ? 'active' : '' ?>" href="usuarios.php">👥 <span>Usuarios</span></a>
      <a class="<?= $current === 'libros.php' ? 'active' : '' ?>" href="libros.php">📚 <span>Libros</span></a>
      <a class="<?= $current === 'prestamos.php' ? 'active' : '' ?>" href="prestamos.php">↔️ <span>Préstamos</span></a>
      <a href="exportar.php?tipo=prestamos">📊 <span>Reportes</span></a>
    </nav>
    <div class="sidebar-footer">
      <div class="admin-chip"><span class="dot"></span><?= e($admin['nombre'] ?? 'Administrador') ?></div>
      <a class="logout" href="logout.php">↪️ <span>Cerrar sesión</span></a>
    </div>
  </aside>

  <main class="main-content">
    <div class="site-topbar">
      <div class="site-title"><button class="menu-btn" type="button" aria-label="Menú">☰</button><span>Sistema de Gestión Bibliotecaria</span></div>
      <div class="top-right">
        <div class="notification" title="Notificaciones">🔔<span class="notification-badge">3</span></div>
        <div class="top-user"><span class="avatar">👤</span><span class="name"><?= e($admin['nombre'] ?? 'Administrador') ?></span><span class="chev">⌄</span></div>
      </div>
    </div>

    <header class="topbar">
      <div><span class="eyebrow">Biblioteca</span><h1><?= e($page_title ?? 'Panel') ?></h1></div>
      <div class="top-actions"><span class="online">● Sistema activo</span></div>
    </header>

    <?php if ($flash): ?>
      <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
