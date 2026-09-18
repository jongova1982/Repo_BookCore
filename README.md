# Biblioteca Cloud - PHP + MySQL

Aplicación web de gestión de biblioteca lista para AlwaysData.

## Archivos importantes

- `config.php` → **Aquí van las credenciales de MySQL** (lo piden en la sustentación)
- `database.sql` → Script para crear las tablas e insertar datos de ejemplo
- `index.php` → Dashboard
- `usuarios.php` → CRUD de usuarios
- `libros.php` → CRUD de libros
- `prestamos.php` → Gestión de préstamos

## Pasos para subir a AlwaysData

### 1. Crear la base de datos
1. Entra a AlwaysData → **Databases** → **MySQL**
2. Crea una base de datos nueva (ejemplo: `tuusuario_biblioteca`)
3. Anota:
   - Host (ej: `mysql-tuusuario.alwaysdata.net`)
   - Nombre de la base de datos
   - Usuario
   - Contraseña

### 2. Ejecutar el script SQL
1. Entra a **phpMyAdmin** desde AlwaysData
2. Selecciona tu base de datos
3. Ve a la pestaña **SQL**
4. Copia y pega todo el contenido de `database.sql`
5. Ejecuta

### 3. Configurar las credenciales
Abre el archivo `config.php` y cambia estos valores:

```php
define('DB_HOST', 'mysql-XXXX.alwaysdata.net');
define('DB_NAME', 'tuusuario_biblioteca');
define('DB_USER', 'tuusuario');
define('DB_PASS', 'tu_contraseña');
```

### 4. Subir los archivos
1. Sube **todos** los archivos a la carpeta `www` de AlwaysData
   (puedes usar el File Manager o FTP)
2. La estructura debe quedar así:

```
www/
├── config.php
├── database.sql
├── index.php
├── usuarios.php
├── libros.php
├── prestamos.php
├── includes/
│   ├── header.php
│   └── footer.php
└── README.md
```

### 5. Probar
Abre tu dominio: `https://tuusuario.alwaysdata.net`

---

## Credenciales visibles (para la sustentación)

En el archivo `config.php` se ven claramente:

```php
define('DB_HOST', '...');
define('DB_NAME', '...');
define('DB_USER', '...');
define('DB_PASS', '...');
```

Esto cumple con el punto 3 de las preguntas de sustentación.
