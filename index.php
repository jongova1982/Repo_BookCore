<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#03152f">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
    <title>BookCore | Nucleo de gestion bibliotecaria</title>
    <link rel="icon" href="assets/img/bookcore_sinfondo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/styles.css?v=20260917-2">
</head>
<body>
    <div class="background-overlay" aria-hidden="true"></div>

    <aside class="sidebar" id="sidebar">
        <a class="brand" href="#inicio" aria-label="BookCore, ir al inicio">
            <img src="assets/img/bookcore_sinfondo.png" alt="Logo BookCore" width="110" height="84" style="width:110px;height:84px;object-fit:contain;display:block;margin:auto;">
        </a>
        <nav class="navigation" aria-label="Navegacion principal">
            <button class="nav-item active" type="button" data-view="inicio">
                <span class="nav-icon">⌂</span><span>Inicio</span>
            </button>
            <button class="nav-item" type="button" data-view="usuarios">
                <span class="nav-icon">♙</span><span>Usuarios</span>
            </button>
            <button class="nav-item" type="button" data-view="libros">
                <span class="nav-icon">▤</span><span>Libros</span>
            </button>
            <button class="nav-item" type="button" data-view="prestamos">
                <span class="nav-icon">⇄</span><span>Prestamos</span>
            </button>
        </nav>
        <div class="sidebar-footer">
            <span class="status-dot"></span>
            <div><strong>Sistema activo</strong><small>Gestion en la nube</small></div>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <button class="menu-button" id="menuButton" type="button" aria-label="Abrir menu">☰</button>
            <div>
                <p class="eyebrow">Panel administrativo</p>
                <h1 id="pageTitle">Centro de control</h1>
            </div>
            <div class="admin-chip"><span>AD</span><div><strong>Administrador</strong><small>BookCore</small></div></div>
        </header>

        <section class="view active" id="view-inicio">
            <div class="hero-card">
                <div class="hero-copy">
                    <span class="hero-badge">BIBLIOTECA INTELIGENTE</span>
                    <h2>El conocimiento,<br><em>siempre en movimiento.</em></h2>
                    <p>Administra tu catalogo, tus lectores y cada prestamo desde un unico nucleo digital.</p>
                    <button class="primary-button" type="button" data-go="prestamos">Registrar prestamo <span>→</span></button>
                </div>
                <div class="hero-visual" aria-hidden="true">
                    <img src="assets/img/bookcore_sinfondo.png" alt="" class="hero-logo" width="190" height="190" style="width:190px;height:190px;max-width:100%;object-fit:contain;display:block;margin:auto;">
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card"><div class="stat-icon cyan">♙</div><div><span>Usuarios activos</span><strong id="statUsuarios">—</strong></div></article>
                <article class="stat-card"><div class="stat-icon blue">▤</div><div><span>Titulos registrados</span><strong id="statLibros">—</strong></div></article>
                <article class="stat-card"><div class="stat-icon violet">⇄</div><div><span>Prestamos activos</span><strong id="statPrestamos">—</strong></div></article>
                <article class="stat-card"><div class="stat-icon green">✓</div><div><span>Unidades disponibles</span><strong id="statUnidades">—</strong></div></article>
            </div>

            <div class="content-card recent-card">
                <div class="section-heading"><div><span class="eyebrow">Actividad</span><h3>Prestamos recientes</h3></div><button class="text-button" type="button" data-go="prestamos">Ver todos →</button></div>
                <div class="table-wrap"><table><thead><tr><th>Libro</th><th>Usuario</th><th>Fecha</th><th>Estado</th></tr></thead><tbody id="recentLoansBody"></tbody></table></div>
                <div class="empty-state hidden" id="recentEmpty">Aun no hay prestamos registrados.</div>
            </div>
        </section>

        <section class="view" id="view-usuarios">
            <div class="section-toolbar">
                <div><p>Administra las personas registradas en la biblioteca.</p></div>
                <button class="primary-button" type="button" data-open="usuario">+ Nuevo usuario</button>
            </div>
            <div class="content-card">
                <div class="filter-row"><label class="search-box">⌕<input id="searchUsuarios" type="search" placeholder="Buscar por nombre o cedula..."></label><span class="record-count" id="countUsuarios">0 registros</span></div>
                <div class="table-wrap"><table><thead><tr><th>Nombre</th><th>Cedula</th><th>Telefono</th><th class="actions-column">Acciones</th></tr></thead><tbody id="usuariosBody"></tbody></table></div>
                <div class="empty-state hidden" id="usuariosEmpty">No se encontraron usuarios.</div>
            </div>
        </section>

        <section class="view" id="view-libros">
            <div class="section-toolbar">
                <div><p>Controla el catalogo y las existencias disponibles.</p></div>
                <button class="primary-button" type="button" data-open="libro">+ Nuevo libro</button>
            </div>
            <div class="content-card">
                <div class="filter-row"><label class="search-box">⌕<input id="searchLibros" type="search" placeholder="Buscar por titulo, autor o codigo..."></label><span class="record-count" id="countLibros">0 registros</span></div>
                <div class="table-wrap"><table><thead><tr><th>Codigo</th><th>Titulo</th><th>Autor</th><th>Unidades</th><th class="actions-column">Acciones</th></tr></thead><tbody id="librosBody"></tbody></table></div>
                <div class="empty-state hidden" id="librosEmpty">No se encontraron libros.</div>
            </div>
        </section>

        <section class="view" id="view-prestamos">
            <div class="section-toolbar">
                <div><p>Registra salidas y devoluciones con control automatico de inventario.</p></div>
                <button class="primary-button" type="button" data-open="prestamo">+ Nuevo prestamo</button>
            </div>
            <div class="content-card">
                <div class="filter-row"><label class="search-box">⌕<input id="searchPrestamos" type="search" placeholder="Buscar libro o usuario..."></label><select id="filterPrestamos" class="select-filter"><option value="">Todos los estados</option><option value="activo">Activos</option><option value="devuelto">Devueltos</option></select></div>
                <div class="table-wrap"><table><thead><tr><th>ID</th><th>Libro</th><th>Usuario</th><th>Prestado</th><th>Estado</th><th class="actions-column">Acciones</th></tr></thead><tbody id="prestamosBody"></tbody></table></div>
                <div class="empty-state hidden" id="prestamosEmpty">No se encontraron prestamos.</div>
            </div>
        </section>
    </main>

    <div class="modal-backdrop hidden" id="modalBackdrop" role="presentation">
        <section class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <button class="modal-close" type="button" id="modalClose" aria-label="Cerrar">×</button>
            <span class="eyebrow" id="modalEyebrow">NUEVO REGISTRO</span>
            <h2 id="modalTitle">Agregar</h2>
            <form id="entityForm" autocomplete="off"></form>
        </section>
    </div>

    <div class="toast-container" id="toastContainer" aria-live="polite"></div>
    <script src="assets/js/app.js" defer></script>
</body>
</html>


