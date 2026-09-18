'use strict';

const state = { usuarios: [], libros: [], prestamos: [], editing: null };
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const titles = { inicio: 'Centro de control', usuarios: 'Gestion de usuarios', libros: 'Catalogo de libros', prestamos: 'Control de prestamos' };

const $ = (selector, parent = document) => parent.querySelector(selector);
const $$ = (selector, parent = document) => [...parent.querySelectorAll(selector)];

function escapeHtml(value) {
    const element = document.createElement('div');
    element.textContent = String(value ?? '');
    return element.innerHTML;
}

function formatDate(value) {
    if (!value) return '—';
    const date = new Date(String(value).replace(' ', 'T'));
    return Number.isNaN(date.getTime()) ? value : new Intl.DateTimeFormat('es-CO', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
}

async function api(resource, options = {}) {
    const response = await fetch(`api.php?resource=${encodeURIComponent(resource)}${options.action ? `&action=${encodeURIComponent(options.action)}` : ''}`, {
        method: options.method || 'GET',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
        body: options.data ? JSON.stringify(options.data) : undefined,
    });
    let result;
    try { result = await response.json(); } catch { result = { ok: false, message: 'El servidor no devolvio una respuesta valida.' }; }
    if (!response.ok || !result.ok) throw new Error(result.message || 'No fue posible completar la operacion.');
    return result;
}

function toast(message, error = false) {
    const item = document.createElement('div');
    item.className = `toast${error ? ' error' : ''}`;
    item.innerHTML = `<strong>${error ? '!' : '✓'}</strong><span>${escapeHtml(message)}</span>`;
    $('#toastContainer').append(item);
    setTimeout(() => item.remove(), 4200);
}

function showView(name) {
    $$('.view').forEach(view => view.classList.toggle('active', view.id === `view-${name}`));
    $$('.nav-item').forEach(item => item.classList.toggle('active', item.dataset.view === name));
    $('#pageTitle').textContent = titles[name];
    $('#sidebar').classList.remove('open');
    history.replaceState(null, '', `#${name}`);
    loadView(name);
}

async function loadView(name) {
    try {
        if (name === 'inicio') await loadDashboard();
        if (name === 'usuarios') await loadUsuarios();
        if (name === 'libros') await loadLibros();
        if (name === 'prestamos') await loadPrestamos();
    } catch (error) { toast(error.message, true); }
}

async function loadDashboard() {
    const result = await api('dashboard');
    $('#statUsuarios').textContent = result.stats.usuarios;
    $('#statLibros').textContent = result.stats.libros;
    $('#statPrestamos').textContent = result.stats.prestamos;
    $('#statUnidades').textContent = result.stats.unidades;
    $('#recentLoansBody').innerHTML = result.recent.map(row => `<tr><td class="primary-cell">${escapeHtml(row.titulo)}</td><td>${escapeHtml(row.usuario)}</td><td class="secondary-cell">${formatDate(row.fecha_prestamo)}</td><td><span class="badge ${row.estado}">${escapeHtml(row.estado)}</span></td></tr>`).join('');
    $('#recentEmpty').classList.toggle('hidden', result.recent.length > 0);
}

async function loadUsuarios() {
    const result = await api('usuarios');
    state.usuarios = result.data;
    renderUsuarios();
}

function renderUsuarios() {
    const query = $('#searchUsuarios').value.trim().toLowerCase();
    const rows = state.usuarios.filter(item => `${item.Nombre} ${item.cedula} ${item.telefono}`.toLowerCase().includes(query));
    $('#usuariosBody').innerHTML = rows.map(item => `<tr><td class="primary-cell">${escapeHtml(item.Nombre)}</td><td>${escapeHtml(item.cedula)}</td><td class="secondary-cell">${escapeHtml(item.telefono)}</td><td><div class="actions"><button class="action-button" data-edit-user="${escapeHtml(item.cedula)}">Editar</button><button class="action-button danger" data-delete-user="${escapeHtml(item.cedula)}">Eliminar</button></div></td></tr>`).join('');
    $('#countUsuarios').textContent = `${rows.length} ${rows.length === 1 ? 'registro' : 'registros'}`;
    $('#usuariosEmpty').classList.toggle('hidden', rows.length > 0);
}

async function loadLibros() {
    const result = await api('libros');
    state.libros = result.data;
    renderLibros();
}

function renderLibros() {
    const query = $('#searchLibros').value.trim().toLowerCase();
    const rows = state.libros.filter(item => `${item.codigo} ${item.titulo} ${item.autor}`.toLowerCase().includes(query));
    $('#librosBody').innerHTML = rows.map(item => `<tr><td class="secondary-cell">${escapeHtml(item.codigo)}</td><td class="primary-cell">${escapeHtml(item.titulo)}</td><td>${escapeHtml(item.autor)}</td><td><span class="stock ${Number(item.unidades) === 0 ? 'empty' : ''}">${escapeHtml(item.unidades)}</span></td><td><div class="actions"><button class="action-button" data-edit-book="${escapeHtml(item.codigo)}">Editar</button><button class="action-button danger" data-delete-book="${escapeHtml(item.codigo)}">Eliminar</button></div></td></tr>`).join('');
    $('#countLibros').textContent = `${rows.length} ${rows.length === 1 ? 'registro' : 'registros'}`;
    $('#librosEmpty').classList.toggle('hidden', rows.length > 0);
}

async function loadPrestamos() {
    const [loans, users, books] = await Promise.all([api('prestamos'), api('usuarios'), api('libros')]);
    state.prestamos = loans.data;
    state.usuarios = users.data;
    state.libros = books.data;
    renderPrestamos();
}

function renderPrestamos() {
    const query = $('#searchPrestamos').value.trim().toLowerCase();
    const status = $('#filterPrestamos').value;
    const rows = state.prestamos.filter(item => `${item.titulo} ${item.usuario} ${item.id_prestamos}`.toLowerCase().includes(query) && (!status || item.estado === status));
    $('#prestamosBody').innerHTML = rows.map(item => `<tr><td class="secondary-cell">#${escapeHtml(item.id_prestamos)}</td><td class="primary-cell">${escapeHtml(item.titulo)}</td><td>${escapeHtml(item.usuario)}</td><td class="secondary-cell">${formatDate(item.fecha_prestamo)}</td><td><span class="badge ${item.estado}">${escapeHtml(item.estado)}</span></td><td><div class="actions">${item.estado === 'activo' ? `<button class="action-button success" data-return-loan="${escapeHtml(item.id_prestamos)}">Devolver</button>` : `<span class="secondary-cell">${formatDate(item.fecha_devolucion)}</span>`}</div></td></tr>`).join('');
    $('#prestamosEmpty').classList.toggle('hidden', rows.length > 0);
}

function field(name, label, type = 'text', value = '', full = false, attributes = '') {
    return `<div class="form-field${full ? ' full' : ''}"><label for="field-${name}">${label}</label><input id="field-${name}" name="${name}" type="${type}" value="${escapeHtml(value)}" ${attributes} required></div>`;
}

function formActions(label) {
    return `<div class="form-actions"><button class="secondary-button" type="button" data-close-modal>Cancelar</button><button class="primary-button" type="submit">${label}</button></div>`;
}

function openModal(type, record = null) {
    state.editing = record;
    const form = $('#entityForm');
    $('#modalEyebrow').textContent = record ? 'EDITAR REGISTRO' : 'NUEVO REGISTRO';
    form.dataset.type = type;

    if (type === 'usuario') {
        $('#modalTitle').textContent = record ? 'Editar usuario' : 'Agregar usuario';
        form.innerHTML = `<div class="form-grid">${field('Nombre', 'Nombre completo', 'text', record?.Nombre || '', true, 'maxlength="120"')}${field('cedula', 'Cedula', 'text', record?.cedula || '', false, 'maxlength="24"')}${field('telefono', 'Telefono', 'tel', record?.telefono || '', false, 'maxlength="24"')}${formActions(record ? 'Guardar cambios' : 'Crear usuario')}</div>`;
    }

    if (type === 'libro') {
        $('#modalTitle').textContent = record ? 'Editar libro' : 'Agregar libro';
        form.innerHTML = `<div class="form-grid">${field('codigo', 'Codigo', 'text', record?.codigo || '', false, 'maxlength="32"')}${field('unidades', 'Unidades', 'number', record?.unidades ?? 1, false, 'min="0" step="1"')}${field('titulo', 'Titulo', 'text', record?.titulo || '', true, 'maxlength="180"')}${field('autor', 'Autor', 'text', record?.autor || '', true, 'maxlength="120"')}${formActions(record ? 'Guardar cambios' : 'Crear libro')}</div>`;
    }

    if (type === 'prestamo') {
        $('#modalTitle').textContent = 'Registrar prestamo';
        const books = state.libros.filter(book => Number(book.unidades) > 0).map(book => `<option value="${escapeHtml(book.codigo)}">${escapeHtml(book.titulo)} — ${book.unidades} disponibles</option>`).join('');
        const users = state.usuarios.map(user => `<option value="${escapeHtml(user.cedula)}">${escapeHtml(user.Nombre)} — ${escapeHtml(user.cedula)}</option>`).join('');
        form.innerHTML = `<div class="form-grid"><div class="form-field full"><label for="field-id_codigo">Libro disponible</label><select id="field-id_codigo" name="id_codigo" required><option value="">Seleccionar libro...</option>${books}</select></div><div class="form-field full"><label for="field-id_usuario">Usuario</label><select id="field-id_usuario" name="id_usuario" required><option value="">Seleccionar usuario...</option>${users}</select></div>${formActions('Confirmar prestamo')}</div>`;
    }

    $('#modalBackdrop').classList.remove('hidden');
    setTimeout(() => $('input, select', form)?.focus(), 50);
}

function closeModal() { $('#modalBackdrop').classList.add('hidden'); state.editing = null; }

async function submitForm(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const type = form.dataset.type;
    const resource = `${type}s`;
    const data = Object.fromEntries(new FormData(form).entries());
    if (state.editing) data.original = type === 'usuario' ? state.editing.cedula : state.editing.codigo;
    const submit = $('button[type="submit"]', form);
    submit.disabled = true;
    try {
        const result = await api(resource, { method: state.editing ? 'PUT' : 'POST', data });
        closeModal();
        toast(result.message);
        await loadView(type === 'prestamo' ? 'prestamos' : resource);
    } catch (error) { toast(error.message, true); }
    finally { submit.disabled = false; }
}

async function removeEntity(resource, identifier, key, description) {
    if (!window.confirm(`¿Eliminar ${description}? Esta accion lo ocultara del sistema.`)) return;
    try {
        const result = await api(resource, { method: 'DELETE', data: { [key]: identifier } });
        toast(result.message);
        await loadView(resource);
    } catch (error) { toast(error.message, true); }
}

async function returnLoan(id) {
    if (!window.confirm('¿Confirmar la devolucion de este libro?')) return;
    try {
        const result = await api('prestamos', { method: 'PUT', action: 'devolver', data: { id_prestamos: id } });
        toast(result.message);
        await loadPrestamos();
    } catch (error) { toast(error.message, true); }
}

document.addEventListener('click', event => {
    const nav = event.target.closest('[data-view]');
    const go = event.target.closest('[data-go]');
    const open = event.target.closest('[data-open]');
    if (nav) showView(nav.dataset.view);
    if (go) showView(go.dataset.go);
    if (open) openModal(open.dataset.open);
    if (event.target.closest('#menuButton')) $('#sidebar').classList.toggle('open');
    if (event.target.closest('#modalClose, [data-close-modal]') || event.target === $('#modalBackdrop')) closeModal();

    const editUser = event.target.closest('[data-edit-user]');
    if (editUser) openModal('usuario', state.usuarios.find(item => item.cedula === editUser.dataset.editUser));
    const deleteUser = event.target.closest('[data-delete-user]');
    if (deleteUser) removeEntity('usuarios', deleteUser.dataset.deleteUser, 'cedula', 'este usuario');
    const editBook = event.target.closest('[data-edit-book]');
    if (editBook) openModal('libro', state.libros.find(item => item.codigo === editBook.dataset.editBook));
    const deleteBook = event.target.closest('[data-delete-book]');
    if (deleteBook) removeEntity('libros', deleteBook.dataset.deleteBook, 'codigo', 'este libro');
    const loan = event.target.closest('[data-return-loan]');
    if (loan) returnLoan(loan.dataset.returnLoan);
});

$('#entityForm').addEventListener('submit', submitForm);
$('#searchUsuarios').addEventListener('input', renderUsuarios);
$('#searchLibros').addEventListener('input', renderLibros);
$('#searchPrestamos').addEventListener('input', renderPrestamos);
$('#filterPrestamos').addEventListener('change', renderPrestamos);
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });

showView(titles[location.hash.slice(1)] ? location.hash.slice(1) : 'inicio');

