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
            accent: '#3b82f6',
            soft: '#f0f9ff'
          }
        }
      }
    }
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .sidebar-link.active { background-color: #0d9488; color: white; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3); }
    .card-hover { transition: all 0.3s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.12); }
    .transition-all { transition: all 0.3s ease; }
    .logo-gradient { background: linear-gradient(135deg, #1e3a5f 0%, #0d9488 100%); }
  </style>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-primary text-white flex flex-col fixed h-full z-20 shadow-xl">
      <!-- Logo -->
      <div class="p-5 border-b border-blue-900/40">
        <div class="flex items-center gap-3">
          <!-- SVG Logo inspirado en el diseño generado -->
          <div class="w-12 h-12 rounded-xl logo-gradient flex items-center justify-center shadow-lg relative overflow-hidden">
            <svg width="28" height="28" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Libro abierto -->
              <path d="M8 14C8 12 10 10 14 10H22V34H14C10 34 8 32 8 30V14Z" fill="white" fill-opacity="0.9"/>
              <path d="M40 14C40 12 38 10 34 10H26V34H34C38 34 40 32 40 30V14Z" fill="white" fill-opacity="0.7"/>
              <!-- Nube / red -->
              <circle cx="24" cy="18" r="2.5" fill="#5eead4"/>
              <circle cx="18" cy="22" r="1.8" fill="#5eead4"/>
              <circle cx="30" cy="22" r="1.8" fill="#5eead4"/>
              <line x1="24" y1="18" x2="18" y2="22" stroke="#5eead4" stroke-width="1.2"/>
              <line x1="24" y1="18" x2="30" y2="22" stroke="#5eead4" stroke-width="1.2"/>
            </svg>
          </div>
          <div>
            <h1 class="font-bold text-lg leading-tight tracking-tight">Biblioteca</h1>
            <p class="text-teal-300 text-xs font-semibold tracking-wider">CLOUD</p>
          </div>
        </div>
        <p class="text-[10px] text-blue-300/80 mt-2 tracking-wide">GESTIÓN DIGITAL PARA BIBLIOTECAS</p>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-1.5">
        <a href="index.php" class="sidebar-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-blue-900/40 transition-all text-sm font-medium">
          <i class="fas fa-home w-5 text-center"></i> <span>Dashboard</span>
        </a>
        <a href="usuarios.php" class="sidebar-link <?= ($currentPage ?? '') === 'usuarios' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-blue-900/40 transition-all text-sm font-medium">
          <i class="fas fa-users w-5 text-center"></i> <span>Usuarios</span>
        </a>
        <a href="libros.php" class="sidebar-link <?= ($currentPage ?? '') === 'libros' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-blue-900/40 transition-all text-sm font-medium">
          <i class="fas fa-book w-5 text-center"></i> <span>Libros</span>
        </a>
        <a href="prestamos.php" class="sidebar-link <?= ($currentPage ?? '') === 'prestamos' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-blue-900/40 transition-all text-sm font-medium">
          <i class="fas fa-handshake w-5 text-center"></i> <span>Préstamos</span>
        </a>
      </nav>

      <!-- Footer sidebar -->
      <div class="p-4 border-t border-blue-900/40">
        <div class="bg-blue-900/30 rounded-xl p-3">
          <p class="text-xs text-blue-200">Sistema de gestión</p>
          <p class="font-semibold text-sm text-white">Bibliotecas Modernas</p>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64">
      <!-- Top Header -->
      <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
        <div>
          <h2 class="text-2xl font-bold text-primary tracking-tight"><?= $pageTitle ?? 'Dashboard' ?></h2>
          <p class="text-slate-500 text-sm mt-0.5"><?= $pageSubtitle ?? '' ?></p>
        </div>
        <div class="flex items-center gap-4">
          <div class="hidden sm:block text-right">
            <p class="font-semibold text-sm text-slate-700">Administrador</p>
            <p class="text-xs text-slate-400">admin@bibliotecacloud.com</p>
          </div>
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-secondary to-teal-600 flex items-center justify-center text-white font-bold shadow-md">
            A
          </div>
        </div>
      </header>

      <div class="p-8">
