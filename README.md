# BookCore

Aplicación web para gestionar una biblioteca. Permite administrar usuarios, libros y préstamos desde una interfaz en español.

## Contenido

- Usuarios: nombre, cédula y teléfono.
- Libros: código, título, autor y unidades.
- Préstamos: usuario, libro y fecha de devolución.
- Operaciones disponibles: agregar, editar y eliminar registros.
- Diseño responsive para computador y dispositivos móviles.

## Publicar en Alwaysdata

Esta versión funciona como una aplicación web estática. Solo necesita publicar el archivo `index.html`.

### 1. Crear la cuenta

1. Ingresa a [alwaysdata.com](https://www.alwaysdata.com/).
2. Crea una cuenta o inicia sesión.
3. Confirma el correo electrónico si Alwaysdata lo solicita.

### 2. Crear el sitio web

1. En el panel de Alwaysdata, abre **Web > Sites**.
2. Selecciona **Add a site**.
3. Elige el tipo **Static files**.
4. En **Path**, escribe la carpeta pública del proyecto, por ejemplo:

   ```text
   /home/USUARIO/www/bookcore
   ```

5. Guarda la configuración.

> Reemplaza `USUARIO` por el nombre de usuario real de Alwaysdata.

### 3. Subir los archivos

Puedes subir el proyecto mediante el administrador de archivos, SFTP o Git.

La carpeta pública debe contener como mínimo:

```text
bookcore/
└── index.html
```

Si usas Git:

```bash
git clone URL_DEL_REPOSITORIO /home/USUARIO/www/bookcore
```

Si subes los archivos por SFTP, coloca `index.html` directamente dentro de la carpeta configurada en **Path**. No lo dejes dentro de otra subcarpeta.

### 4. Configurar el dominio

1. En Alwaysdata, abre **Web > Sites** y entra al sitio creado.
2. Añade el dominio proporcionado por Alwaysdata o tu dominio personalizado.
3. Si usas un dominio propio, crea en tu proveedor DNS un registro `CNAME` o `A` siguiendo los valores indicados por Alwaysdata.
4. Activa HTTPS desde la configuración del dominio cuando esté disponible.

### 5. Verificar la publicación

Abre la dirección del sitio en el navegador. Debe mostrarse la pantalla de BookCore.

Comprueba lo siguiente:

- Se muestra la imagen de fondo.
- El menú permite cambiar entre Resumen, Usuarios, Libros y Préstamos.
- Los botones de agregar, editar y eliminar funcionan.
- El sitio se adapta a pantallas pequeñas.

## Importante sobre los datos

Actualmente los registros se manejan en el navegador mediante JavaScript y no se guardan en una base de datos. Al recargar la página, los cambios realizados pueden perderse.

Para guardar usuarios, libros y préstamos de forma permanente en la nube, será necesario conectar BookCore a una base de datos o API, por ejemplo Alwaysdata PostgreSQL/MySQL mediante un backend.

## Tecnologías

- HTML5
- CSS3
- JavaScript
- Google Fonts
- Imágenes remotas de Unsplash
