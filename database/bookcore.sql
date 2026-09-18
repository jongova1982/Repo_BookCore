-- BookCore - estructura MySQL para Alwaysdata
-- Selecciona la base jongox_bookcore_db antes de ejecutar este archivo.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS BKprestamos_db;
DROP TABLE IF EXISTS BKadministrador_db;
DROP TABLE IF EXISTS BKlibros_db;
DROP TABLE IF EXISTS BKusuarios_db;

CREATE TABLE BKusuarios_db (
    Nombre VARCHAR(120) NOT NULL,
    cedula VARCHAR(24) NOT NULL,
    telefono VARCHAR(24) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (cedula),
    INDEX idx_usuarios_nombre (Nombre),
    INDEX idx_usuarios_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE BKlibros_db (
    codigo VARCHAR(32) NOT NULL,
    titulo VARCHAR(180) NOT NULL,
    autor VARCHAR(120) NOT NULL,
    unidades INT UNSIGNED NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (codigo),
    INDEX idx_libros_titulo (titulo),
    INDEX idx_libros_autor (autor),
    INDEX idx_libros_activo (activo),
    CONSTRAINT chk_libros_unidades CHECK (unidades >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE BKadministrador_db (
    cedula VARCHAR(24) NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    PRIMARY KEY (cedula)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE BKprestamos_db (
    id_prestamos BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_codigo VARCHAR(32) NOT NULL,
    id_usuario VARCHAR(24) NOT NULL,
    fecha_prestamo DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_devolucion DATETIME NULL,
    estado ENUM('activo', 'devuelto') NOT NULL DEFAULT 'activo',
    PRIMARY KEY (id_prestamos),
    INDEX idx_prestamos_codigo (id_codigo),
    INDEX idx_prestamos_usuario (id_usuario),
    INDEX idx_prestamos_estado (estado),
    CONSTRAINT fk_prestamo_libro FOREIGN KEY (id_codigo)
        REFERENCES BKlibros_db (codigo) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_prestamo_usuario FOREIGN KEY (id_usuario)
        REFERENCES BKusuarios_db (cedula) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO BKadministrador_db (cedula, nombre)
VALUES ('0000000000', 'Administrador BookCore');

SET FOREIGN_KEY_CHECKS = 1;

