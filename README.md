# Biblioteca Cloud - Aplicación Web de Gestión de Biblioteca

Aplicación web sencilla y profesional para gestionar usuarios, libros y préstamos de una biblioteca.

## Características

- **Dashboard** con estadísticas en tiempo real
- **CRUD completo** de Usuarios (nombre, cédula, teléfono)
- **CRUD completo** de Libros (código, título, autor, unidades)
- **Gestión de Préstamos** (relación usuario + libro, control de stock)
- Diseño moderno con colores azul/teal
- Datos guardados en el navegador (localStorage) – no necesita servidor

## Cómo usar

1. Abre la carpeta en Visual Studio Code
2. Abre el archivo `index.html` con Live Server (recomendado) o directamente en el navegador
3. ¡Listo! Ya puedes usar la aplicación

### Extensión recomendada en VS Code

- Live Server (de Ritwick Dey)

## Estructura

```
biblioteca-cloud/
├── index.html          → Dashboard
├── usuarios.html       → Gestión de usuarios
├── libros.html         → Gestión de libros
├── prestamos.html      → Gestión de préstamos
├── js/
│   └── app.js          → Lógica de la aplicación
└── README.md
```

## Datos de ejemplo

La aplicación viene con datos de ejemplo precargados. Puedes agregarlos, editarlos o eliminarlos libremente.

## Notas para la presentación

- Todo funciona 100% en el navegador (no necesita base de datos real ni backend)
- Perfecto para demostrar el funcionamiento
- Diseño profesional listo para mostrar
