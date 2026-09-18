# BookCore

Aplicacion web PHP + MySQL para gestionar usuarios, libros, prestamos y devoluciones de una biblioteca. Incluye panel responsive, busqueda, control de existencias y operaciones seguras con PDO.

## Instalacion en Alwaysdata

1. Abre phpMyAdmin en Alwaysdata, selecciona `jongox_bookcore_db` e importa [`database/bookcore.sql`](database/bookcore.sql).
2. Copia `config/local.php.example` como `config/local.php` en el servidor.
3. Escribe la clave real de MySQL dentro de `config/local.php`. Este archivo esta excluido de Git y debe permanecer privado.
4. Publica el contenido del repositorio dentro del directorio `www/` de Alwaysdata.
5. Comprueba que el sitio use PHP 8.1 o posterior y que la extension `pdo_mysql` este habilitada.

Para el despliegue automatico, crea en GitHub (`Settings > Secrets and variables > Actions`) los secretos `FTP_USERNAME` y `FTP_PASSWORD`. El workflow publica cada `push` realizado en la rama `jongox`.

Como alternativa al archivo privado, configura estas variables de entorno en Alwaysdata:

```text
BOOKCORE_DB_HOST=mysql-jongox.alwaysdata.net
BOOKCORE_DB_NAME=jongox_bookcore_db
BOOKCORE_DB_USER=jongox
BOOKCORE_DB_PASSWORD=tu_clave
```

## Estructura

- `index.php`: interfaz principal.
- `api.php`: API JSON y reglas de negocio.
- `config/database.php`: conexion PDO.
- `database/bookcore.sql`: esquema MySQL listo para importar.
- `assets/`: estilos, JavaScript e imagenes del proyecto.

Los usuarios y libros se eliminan de forma logica para conservar el historial. Los prestamos y las devoluciones actualizan las unidades dentro de transacciones MySQL.
