<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Biblioteca Cloud' ?> - Gestión Digital</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1e3a5f',
            secondary: '#0d9488',
            accent: '#3b82f6'
          }
        }
      }
    }
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .sidebar-link.active { background-color: #0d9488; color: white; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15); }
    .transition-all { transition: all 0.3s ease; }
  </style>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-primary text-white flex flex-col fixed h-full z-20">
      <div class="p-6 border-b border-blue-900/40">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-secondary rounded-lg flex items-center justify-center">
            <i class="fas fa-book-open text-xl"></i>
          </div>
          <div>
            <h1 class="font-bold text-lg leading-tight">Biblioteca</h1>
            <p class="text-teal-300 text-xs font-medium">Cloud</p>
          </div>
        </div>
      </div>
      <nav class="flex-1 p-4 space-y-1">
        <a href="index.php" class="sidebar-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-900/50 transition-all">
          <i class="fas fa-home w-5"></i> <span>Dashboard</span>
        </a>
        <a href="usuarios.php" class="sidebar-link <?= ($currentPage ?? '') === 'usuarios' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-900/50 transition-all">
          <i class="fas fa-users w-5"></i> <span>Usuarios</span>
        </a>
        <a href="libros.php" class="sidebar-link <?= ($currentPage ?? '') === 'libros' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-900/50 transition-all">
          <i class="fas fa-book w-5"></i> <span>Libros</span>
        </a>
        <a href="prestamos.php" class="sidebar-link <?= ($currentPage ?? '') === 'prestamos' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-900/50 transition-all">
          <i class="fas fa-handshake w-5"></i> <span>Préstamos</span>
        </a>
      </nav>
      <div class="p-4 border-t border-blue-900/40 text-xs text-blue-200">
        <p>Gestión Digital para</p>
        <p class="font-medium text-white">Bibliotecas Modernas</p>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64">
      <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
        <div>
          <h2 class="text-2xl font-bold text-primary"><?= $pageTitle ?? 'Dashboard' ?></h2>
          <p class="text-slate-500 text-sm"><?= $pageSubtitle ?? '' ?></p>
        </div>
        <div class="flex items-center gap-4">
          <div class="text-right">
            <p class="font-medium text-sm">Administrador</p>
            <p class="text-xs text-slate-500">admin@bibliotecacloud.com</p>
          </div>
          <div class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center text-white font-bold">A</div>
        </div>
      </header>
      <div class="p-8">
