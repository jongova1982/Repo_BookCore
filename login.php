<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

try { initializeDatabase(); } catch (Throwable $e) {
    exit('Error conectando con MySQL: ' . e($e->getMessage()));
}
if (!empty($_SESSION['admin_id'])) redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM administradores WHERE email=? AND activo=1 LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin'] = ['id'=>(int)$admin['id'], 'nombre'=>$admin['nombre'], 'email'=>$admin['email']];
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        redirect('index.php');
    }
    $error = 'Correo o contraseña incorrectos.';
}
?>

<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#102a43"><title>Acceso · Biblioteca Pro</title><link rel="stylesheet" href="style.css"></head>
<body class="login-page">
  <div class="login-shell">
    <section class="login-visual">
      <div>
        <span class="login-badge">📚 Biblioteca Pro</span>
        <h2>Sistema de Gestión Bibliotecaria</h2>
        <p>Un espacio moderno para administrar usuarios, libros, préstamos y devoluciones de tu biblioteca.</p>
      </div>
      <div class="login-quote">“Los libros son puertas a mundos nuevos.”</div>
    </section>
    <section class="login-panel">
      <div class="login-card">
        <div class="login-brand"><div class="brand-mark">📖</div><div><strong>Biblioteca Pro</strong><small>Gestión de Biblioteca</small></div></div>
        <h1>Bienvenido</h1><p class="muted">Ingresa al panel administrativo.</p>
        <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form method="post" autocomplete="off">
          <label>Correo<input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
          <label>Contraseña<input type="password" name="password" required></label>
          <button class="btn primary full" type="submit">Ingresar al sistema</button>
        </form>
        <p class="login-note">Acceso inicial: admin@biblioteca.local / Admin123*</p>
      </div>
    </section>
  </div>
</body></html>