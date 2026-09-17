# Biblioteca Pro — PHP + MySQL + HTML + CSS

Aplicación web para gestionar usuarios, libros y préstamos en un servidor PHP/MySQL como AlwaysData.

## Incluye
- Panel de indicadores.
- CRUD de usuarios: nombre, cédula y teléfono.
- CRUD de libros: código, título, autor y unidades.
- Registro de préstamos con varios libros por préstamo.
- Control automático de inventario: descuenta unidades al prestar y las reintegra al devolver.
- Estados ACTIVO, VENCIDO y DEVUELTO.
- Actualización automática de préstamos vencidos.
- Filtros y búsqueda.
- Exportación CSV de usuarios, libros y préstamos.
- Sesión administrativa, contraseñas con `password_hash`, CSRF y consultas PDO preparadas.
- Diseño responsive para computador y móvil.

## Instalación en AlwaysData
1. Sube todo el contenido de este ZIP al directorio público de tu sitio.
2. Abre `config.php` y cambia `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS` por las credenciales reales de AlwaysData.
3. Asegúrate de que PHP tenga habilitada la extensión PDO MySQL.
4. Entra a `login.php`. Las tablas se crean automáticamente al primer acceso.
5. Acceso inicial: `admin@biblioteca.local` / `Admin123*`. Cambia esta contraseña antes de usar el sistema en producción.

## Importante
El paquete NO contiene `.github/`, `workflows/`, `deployAlways.yml` ni `.gitattributes`, porque esos archivos ya existen en tu repositorio.

La aplicación no intenta crear la base de datos MySQL; solo crea las tablas dentro de la base de datos indicada en `config.php`, que es lo habitual en hosting compartido.
