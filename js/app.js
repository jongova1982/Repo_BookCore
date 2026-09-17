// ==================== DATA STORAGE ====================
const DB = {
  get(key) {
    return JSON.parse(localStorage.getItem(key) || "[]");
  },
  set(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
  },
  init() {
    if (!localStorage.getItem("usuarios")) {
      this.set("usuarios", [
        {
          id: 1,
          nombre: "Ana María López",
          cedula: "1234567890",
          telefono: "3001234567",
        },
        {
          id: 2,
          nombre: "Carlos Andrés Ruiz",
          cedula: "0987654321",
          telefono: "3109876543",
        },
        {
          id: 3,
          nombre: "Laura Sofía Gómez",
          cedula: "1122334455",
          telefono: "3201122334",
        },
      ]);
    }
    if (!localStorage.getItem("libros")) {
      this.set("libros", [
        {
          id: 1,
          codigo: "LIB-001",
          titulo: "Cien años de soledad",
          autor: "Gabriel García Márquez",
          unidades: 5,
        },
        {
          id: 2,
          codigo: "LIB-002",
          titulo: "El amor en los tiempos del cólera",
          autor: "Gabriel García Márquez",
          unidades: 3,
        },
        {
          id: 3,
          codigo: "LIB-003",
          titulo: "La casa de los espíritus",
          autor: "Isabel Allende",
          unidades: 4,
        },
        {
          id: 4,
          codigo: "LIB-004",
          titulo: "Rayuela",
          autor: "Julio Cortázar",
          unidades: 2,
        },
      ]);
    }
    if (!localStorage.getItem("prestamos")) {
      this.set("prestamos", [
        {
          id: 1,
          usuarioId: 1,
          libroId: 1,
          fecha: "2026-09-10",
          estado: "activo",
        },
        {
          id: 2,
          usuarioId: 2,
          libroId: 3,
          fecha: "2026-09-12",
          estado: "activo",
        },
      ]);
    }
  },
};

DB.init();

// ==================== HELPERS ====================
function generateId(arr) {
  return arr.length ? Math.max(...arr.map((i) => i.id)) + 1 : 1;
}

function formatDate(dateStr) {
  const d = new Date(dateStr);
  return d.toLocaleDateString("es-CO", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
}

// ==================== DASHBOARD ====================
function updateDashboard() {
  const usuarios = DB.get("usuarios");
  const libros = DB.get("libros");
  const prestamos = DB.get("prestamos");

  const totalUnidades = libros.reduce((sum, l) => sum + Number(l.unidades), 0);
  const prestamosActivos = prestamos.filter(
    (p) => p.estado === "activo",
  ).length;

  document.getElementById("stat-libros").textContent = libros.length;
  document.getElementById("stat-usuarios").textContent = usuarios.length;
  document.getElementById("stat-prestamos").textContent = prestamosActivos;
  document.getElementById("stat-unidades").textContent = totalUnidades;

  // Recent libros
  const recentLibros = libros.slice(-3).reverse();
  const contLibros = document.getElementById("recent-libros");
  if (contLibros) {
    contLibros.innerHTML = recentLibros.length
      ? recentLibros
          .map(
            (l) => `
      <div class="flex items-center gap-3 p-3 hover:bg-slate-50 rounded-lg">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-accent">
          <i class="fas fa-book"></i>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-medium text-sm truncate">${l.titulo}</p>
          <p class="text-xs text-slate-500">${l.autor} · ${l.unidades} unidades</p>
        </div>
      </div>
    `,
          )
          .join("")
      : '<p class="text-slate-400 text-sm p-3">Sin libros</p>';
  }

  // Recent prestamos
  const recentPrestamos = prestamos.slice(-3).reverse();
  const contPrestamos = document.getElementById("recent-prestamos");
  if (contPrestamos) {
    contPrestamos.innerHTML = recentPrestamos.length
      ? recentPrestamos
          .map((p) => {
            const u = usuarios.find((x) => x.id === p.usuarioId);
            const l = libros.find((x) => x.id === p.libroId);
            return `
        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 rounded-lg">
          <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center text-secondary">
            <i class="fas fa-handshake"></i>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-medium text-sm truncate">${u ? u.nombre : "Usuario"} → ${l ? l.titulo : "Libro"}</p>
            <p class="text-xs text-slate-500">${formatDate(p.fecha)} · <span class="${p.estado === "activo" ? "text-emerald-600" : "text-slate-400"}">${p.estado}</span></p>
          </div>
        </div>
      `;
          })
          .join("")
      : '<p class="text-slate-400 text-sm p-3">Sin préstamos</p>';
  }
}

// ==================== USUARIOS ====================
function renderUsuarios() {
  const search = (document.getElementById("search")?.value || "").toLowerCase();
  let usuarios = DB.get("usuarios");
  if (search) {
    usuarios = usuarios.filter(
      (u) =>
        u.nombre.toLowerCase().includes(search) || u.cedula.includes(search),
    );
  }

  const tbody = document.getElementById("usuarios-table");
  const empty = document.getElementById("empty-usuarios");
  if (!tbody) return;

  if (usuarios.length === 0) {
    tbody.innerHTML = "";
    empty?.classList.remove("hidden");
    return;
  }
  empty?.classList.add("hidden");

  tbody.innerHTML = usuarios
    .map(
      (u) => `
    <tr class="hover:bg-slate-50">
      <td class="px-6 py-4">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-teal-100 rounded-full flex items-center justify-center text-secondary font-semibold text-sm">
            ${u.nombre.charAt(0)}
          </div>
          <span class="font-medium">${u.nombre}</span>
        </div>
      </td>
      <td class="px-6 py-4 text-slate-600">${u.cedula}</td>
      <td class="px-6 py-4 text-slate-600">${u.telefono}</td>
      <td class="px-6 py-4 text-right">
        <button onclick="editUsuario(${u.id})" class="text-accent hover:text-blue-700 mr-3" title="Editar">
          <i class="fas fa-edit"></i>
        </button>
        <button onclick="deleteUsuario(${u.id})" class="text-red-500 hover:text-red-700" title="Eliminar">
          <i class="fas fa-trash"></i>
        </button>
      </td>
    </tr>
  `,
    )
    .join("");
}

function openModal(id = null) {
  const modal = document.getElementById("modal");
  const title = document.getElementById("modal-title");
  document.getElementById("usuario-form").reset();
  document.getElementById("usuario-id").value = "";

  if (id) {
    const u = DB.get("usuarios").find((x) => x.id === id);
    if (u) {
      title.textContent = "Editar Usuario";
      document.getElementById("usuario-id").value = u.id;
      document.getElementById("nombre").value = u.nombre;
      document.getElementById("cedula").value = u.cedula;
      document.getElementById("telefono").value = u.telefono;
    }
  } else {
    title.textContent = "Nuevo Usuario";
  }
  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeModal() {
  const modal = document.getElementById("modal");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

function saveUsuario(e) {
  e.preventDefault();
  const id = document.getElementById("usuario-id").value;
  const nombre = document.getElementById("nombre").value.trim();
  const cedula = document.getElementById("cedula").value.trim();
  const telefono = document.getElementById("telefono").value.trim();

  let usuarios = DB.get("usuarios");
  if (id) {
    usuarios = usuarios.map((u) =>
      u.id == id ? { ...u, nombre, cedula, telefono } : u,
    );
  } else {
    usuarios.push({ id: generateId(usuarios), nombre, cedula, telefono });
  }
  DB.set("usuarios", usuarios);
  closeModal();
  renderUsuarios();
}

function editUsuario(id) {
  openModal(id);
}

function deleteUsuario(id) {
  if (!confirm("¿Eliminar este usuario?")) return;
  let usuarios = DB.get("usuarios").filter((u) => u.id !== id);
  DB.set("usuarios", usuarios);
  // Also remove related prestamos
  let prestamos = DB.get("prestamos").filter((p) => p.usuarioId !== id);
  DB.set("prestamos", prestamos);
  renderUsuarios();
}

// ==================== LIBROS ====================
function renderLibros() {
  const search = (
    document.getElementById("search-libro")?.value || ""
  ).toLowerCase();
  let libros = DB.get("libros");
  if (search) {
    libros = libros.filter(
      (l) =>
        l.titulo.toLowerCase().includes(search) ||
        l.autor.toLowerCase().includes(search) ||
        l.codigo.toLowerCase().includes(search),
    );
  }

  const tbody = document.getElementById("libros-table");
  const empty = document.getElementById("empty-libros");
  if (!tbody) return;

  if (libros.length === 0) {
    tbody.innerHTML = "";
    empty?.classList.remove("hidden");
    return;
  }
  empty?.classList.add("hidden");

  tbody.innerHTML = libros
    .map(
      (l) => `
    <tr class="hover:bg-slate-50">
      <td class="px-6 py-4 font-mono text-sm text-slate-600">${l.codigo}</td>
      <td class="px-6 py-4 font-medium">${l.titulo}</td>
      <td class="px-6 py-4 text-slate-600">${l.autor}</td>
      <td class="px-6 py-4">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${l.unidades > 0 ? "bg-emerald-100 text-emerald-700" : "bg-red-100 text-red-700"}">
          ${l.unidades}
        </span>
      </td>
      <td class="px-6 py-4 text-right">
        <button onclick="editLibro(${l.id})" class="text-accent hover:text-blue-700 mr-3" title="Editar">
          <i class="fas fa-edit"></i>
        </button>
        <button onclick="deleteLibro(${l.id})" class="text-red-500 hover:text-red-700" title="Eliminar">
          <i class="fas fa-trash"></i>
        </button>
      </td>
    </tr>
  `,
    )
    .join("");
}

function openModalLibro(id = null) {
  const modal = document.getElementById("modal-libro");
  const title = document.getElementById("modal-libro-title");
  document.getElementById("libro-form").reset();
  document.getElementById("libro-id").value = "";

  if (id) {
    const l = DB.get("libros").find((x) => x.id === id);
    if (l) {
      title.textContent = "Editar Libro";
      document.getElementById("libro-id").value = l.id;
      document.getElementById("codigo").value = l.codigo;
      document.getElementById("titulo").value = l.titulo;
      document.getElementById("autor").value = l.autor;
      document.getElementById("unidades").value = l.unidades;
    }
  } else {
    title.textContent = "Nuevo Libro";
  }
  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeModalLibro() {
  const modal = document.getElementById("modal-libro");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

function saveLibro(e) {
  e.preventDefault();
  const id = document.getElementById("libro-id").value;
  const codigo = document.getElementById("codigo").value.trim();
  const titulo = document.getElementById("titulo").value.trim();
  const autor = document.getElementById("autor").value.trim();
  const unidades = parseInt(document.getElementById("unidades").value);

  let libros = DB.get("libros");
  if (id) {
    libros = libros.map((l) =>
      l.id == id ? { ...l, codigo, titulo, autor, unidades } : l,
    );
  } else {
    libros.push({ id: generateId(libros), codigo, titulo, autor, unidades });
  }
  DB.set("libros", libros);
  closeModalLibro();
  renderLibros();
}

function editLibro(id) {
  openModalLibro(id);
}

function deleteLibro(id) {
  if (!confirm("¿Eliminar este libro?")) return;
  let libros = DB.get("libros").filter((l) => l.id !== id);
  DB.set("libros", libros);
  let prestamos = DB.get("prestamos").filter((p) => p.libroId !== id);
  DB.set("prestamos", prestamos);
  renderLibros();
}

// ==================== PRESTAMOS ====================
function loadSelects() {
  const usuarios = DB.get("usuarios");
  const libros = DB.get("libros").filter((l) => l.unidades > 0);

  const selUsuario = document.getElementById("prestamo-usuario");
  const selLibro = document.getElementById("prestamo-libro");
  if (!selUsuario || !selLibro) return;

  selUsuario.innerHTML =
    '<option value="">Seleccionar usuario...</option>' +
    usuarios
      .map((u) => `<option value="${u.id}">${u.nombre} (${u.cedula})</option>`)
      .join("");

  selLibro.innerHTML =
    '<option value="">Seleccionar libro...</option>' +
    libros
      .map(
        (l) =>
          `<option value="${l.id}">${l.titulo} (${l.codigo}) - ${l.unidades} disp.</option>`,
      )
      .join("");
}

function renderPrestamos() {
  const search = (
    document.getElementById("search-prestamo")?.value || ""
  ).toLowerCase();
  let prestamos = DB.get("prestamos");
  const usuarios = DB.get("usuarios");
  const libros = DB.get("libros");

  if (search) {
    prestamos = prestamos.filter((p) => {
      const u = usuarios.find((x) => x.id === p.usuarioId);
      const l = libros.find((x) => x.id === p.libroId);
      return (
        (u && u.nombre.toLowerCase().includes(search)) ||
        (l && l.titulo.toLowerCase().includes(search))
      );
    });
  }

  const tbody = document.getElementById("prestamos-table");
  const empty = document.getElementById("empty-prestamos");
  if (!tbody) return;

  if (prestamos.length === 0) {
    tbody.innerHTML = "";
    empty?.classList.remove("hidden");
    return;
  }
  empty?.classList.add("hidden");

  tbody.innerHTML = prestamos
    .map((p) => {
      const u = usuarios.find((x) => x.id === p.usuarioId);
      const l = libros.find((x) => x.id === p.libroId);
      return `
      <tr class="hover:bg-slate-50">
        <td class="px-6 py-4 font-medium">${u ? u.nombre : "—"}</td>
        <td class="px-6 py-4 text-slate-600">${l ? l.titulo : "—"}</td>
        <td class="px-6 py-4 text-slate-600">${formatDate(p.fecha)}</td>
        <td class="px-6 py-4">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${p.estado === "activo" ? "bg-emerald-100 text-emerald-700" : "bg-slate-100 text-slate-600"}">
            ${p.estado === "activo" ? "Activo" : "Devuelto"}
          </span>
        </td>
        <td class="px-6 py-4 text-right">
          ${
            p.estado === "activo"
              ? `
            <button onclick="devolverPrestamo(${p.id})" class="text-secondary hover:text-teal-700 mr-3" title="Marcar como devuelto">
              <i class="fas fa-check-circle"></i> Devolver
            </button>
          `
              : ""
          }
          <button onclick="deletePrestamo(${p.id})" class="text-red-500 hover:text-red-700" title="Eliminar">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
    `;
    })
    .join("");
}

function openModalPrestamo() {
  loadSelects();
  const modal = document.getElementById("modal-prestamo");
  document.getElementById("prestamo-form").reset();
  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeModalPrestamo() {
  const modal = document.getElementById("modal-prestamo");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

function savePrestamo(e) {
  e.preventDefault();
  const usuarioId = parseInt(document.getElementById("prestamo-usuario").value);
  const libroId = parseInt(document.getElementById("prestamo-libro").value);

  let libros = DB.get("libros");
  const libro = libros.find((l) => l.id === libroId);
  if (!libro || libro.unidades <= 0) {
    alert("No hay unidades disponibles de este libro");
    return;
  }

  // Decrease units
  libros = libros.map((l) =>
    l.id === libroId ? { ...l, unidades: l.unidades - 1 } : l,
  );
  DB.set("libros", libros);

  let prestamos = DB.get("prestamos");
  prestamos.push({
    id: generateId(prestamos),
    usuarioId,
    libroId,
    fecha: new Date().toISOString().split("T")[0],
    estado: "activo",
  });
  DB.set("prestamos", prestamos);

  closeModalPrestamo();
  renderPrestamos();
}

function devolverPrestamo(id) {
  if (!confirm("¿Marcar este préstamo como devuelto?")) return;

  let prestamos = DB.get("prestamos");
  const prestamo = prestamos.find((p) => p.id === id);
  if (!prestamo) return;

  prestamos = prestamos.map((p) =>
    p.id === id ? { ...p, estado: "devuelto" } : p,
  );
  DB.set("prestamos", prestamos);

  // Increase units
  let libros = DB.get("libros");
  libros = libros.map((l) =>
    l.id === prestamo.libroId ? { ...l, unidades: l.unidades + 1 } : l,
  );
  DB.set("libros", libros);

  renderPrestamos();
}

function deletePrestamo(id) {
  if (!confirm("¿Eliminar este registro de préstamo?")) return;
  let prestamos = DB.get("prestamos").filter((p) => p.id !== id);
  DB.set("prestamos", prestamos);
  renderPrestamos();
}
