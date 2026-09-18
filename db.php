SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS prestamo_detalle;
DROP TABLE IF EXISTS prestamos;
DROP TABLE IF EXISTS libros;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS administradores;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- TABLA USUARIOS
-- =========================================================
CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    cedula VARCHAR(30) NOT NULL UNIQUE,
    telefono VARCHAR(30) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_usuarios_nombre (nombre),
    INDEX idx_usuarios_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- TABLA LIBROS
-- =========================================================
CREATE TABLE libros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(180) NOT NULL,
    unidades INT UNSIGNED NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_libros_titulo (titulo),
    INDEX idx_libros_autor (autor),
    INDEX idx_libros_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- TABLA PRESTAMOS
-- =========================================================
CREATE TABLE prestamos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    fecha_prestamo DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    fecha_devolucion DATE NULL,
    estado ENUM('ACTIVO','DEVUELTO','VENCIDO') NOT NULL DEFAULT 'ACTIVO',
    observaciones VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_prestamos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id),

    INDEX idx_prestamos_usuario (usuario_id),
    INDEX idx_prestamos_estado (estado),
    INDEX idx_prestamos_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- TABLA DETALLE DE PRESTAMOS
-- =========================================================
CREATE TABLE prestamo_detalle (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prestamo_id INT UNSIGNED NOT NULL,
    libro_id INT UNSIGNED NOT NULL,
    cantidad INT UNSIGNED NOT NULL DEFAULT 1,

    CONSTRAINT fk_detalle_prestamo
        FOREIGN KEY (prestamo_id) REFERENCES prestamos(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_detalle_libro
        FOREIGN KEY (libro_id) REFERENCES libros(id),

    UNIQUE KEY uk_prestamo_libro (prestamo_id, libro_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- TABLA ADMINISTRADORES
-- =========================================================
CREATE TABLE administradores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 10 USUARIOS
-- =========================================================
INSERT INTO usuarios (nombre, cedula, telefono, activo) VALUES
('Juan Carlos Pérez', '1001001001', '3001001001', 1),
('María Fernanda López', '1001001002', '3001001002', 1),
('Andrés Felipe Gómez', '1001001003', '3001001003', 1),
('Laura Marcela Torres', '1001001004', '3001001004', 1),
('Carlos Alberto Rodríguez', '1001001005', '3001001005', 1),
('Diana Carolina Martínez', '1001001006', '3001001006', 1),
('Sebastián Ramírez', '1001001007', '3001001007', 1),
('Valentina Castro', '1001001008', '3001001008', 1),
('Miguel Ángel Herrera', '1001001009', '3001001009', 1),
('Natalia Andrea Moreno', '1001001010', '3001001010', 1);

-- =========================================================
-- 15 LIBROS
-- Las unidades ya contemplan los préstamos activos/vencidos
-- =========================================================
INSERT INTO libros (codigo, titulo, autor, unidades, activo) VALUES
('LIB001', 'Cien años de soledad', 'Gabriel García Márquez', 5, 1),
('LIB002', 'El amor en los tiempos del cólera', 'Gabriel García Márquez', 4, 1),
('LIB003', 'La vorágine', 'José Eustasio Rivera', 4, 1),
('LIB004', 'Don Quijote de la Mancha', 'Miguel de Cervantes', 6, 1),
('LIB005', '1984', 'George Orwell', 5, 1),
('LIB006', 'Orgullo y prejuicio', 'Jane Austen', 5, 1),
('LIB007', 'El principito', 'Antoine de Saint-Exupéry', 7, 1),
('LIB008', 'Crónica de una muerte anunciada', 'Gabriel García Márquez', 5, 1),
('LIB009', 'Rayuela', 'Julio Cortázar', 4, 1),
('LIB010', 'La sombra del viento', 'Carlos Ruiz Zafón', 4, 1),
('LIB011', 'El alquimista', 'Paulo Coelho', 6, 1),
('LIB012', 'Sapiens', 'Yuval Noah Harari', 5, 1),
('LIB013', 'El Hobbit', 'J. R. R. Tolkien', 5, 1),
('LIB014', 'Fahrenheit 451', 'Ray Bradbury', 4, 1),
('LIB015', 'Ensayo sobre la ceguera', 'José Saramago', 5, 1);

-- =========================================================
-- 5 PRESTAMOS DE PRUEBA
-- =========================================================

-- Préstamo 1 - ACTIVO
INSERT INTO prestamos
(usuario_id, fecha_prestamo, fecha_vencimiento, fecha_devolucion, estado, observaciones)
VALUES
(1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), NULL,
 'ACTIVO', 'Préstamo de demostración vigente.');

INSERT INTO prestamo_detalle (prestamo_id, libro_id, cantidad) VALUES
(LAST_INSERT_ID(), 1, 1);

SET @prestamo1 = LAST_INSERT_ID();

-- corregir detalle del libro adicional del préstamo 1
INSERT INTO prestamo_detalle (prestamo_id, libro_id, cantidad)
VALUES (@prestamo1, 5, 1);

-- Préstamo 2 - VENCIDO
INSERT INTO prestamos
(usuario_id, fecha_prestamo, fecha_vencimiento, fecha_devolucion, estado, observaciones)
VALUES
(2,
 DATE_SUB(CURDATE(), INTERVAL 12 DAY),
 DATE_SUB(CURDATE(), INTERVAL 5 DAY),
 NULL,
 'VENCIDO',
 'Préstamo de demostración vencido.');

SET @prestamo2 = LAST_INSERT_ID();

INSERT INTO prestamo_detalle (prestamo_id, libro_id, cantidad) VALUES
(@prestamo2, 2, 1),
(@prestamo2, 7, 1);

-- Préstamo 3 - DEVUELTO
INSERT INTO prestamos
(usuario_id, fecha_prestamo, fecha_vencimiento, fecha_devolucion, estado, observaciones)
VALUES
(3,
 DATE_SUB(CURDATE(), INTERVAL 10 DAY),
 DATE_SUB(CURDATE(), INTERVAL 3 DAY),
 DATE_SUB(CURDATE(), INTERVAL 1 DAY),
 'DEVUELTO',
 'Préstamo devuelto correctamente.');

SET @prestamo3 = LAST_INSERT_ID();

INSERT INTO prestamo_detalle (prestamo_id, libro_id, cantidad) VALUES
(@prestamo3, 3, 1),
(@prestamo3, 8, 1);

-- Préstamo 4 - ACTIVO
INSERT INTO prestamos
(usuario_id, fecha_prestamo, fecha_vencimiento, fecha_devolucion, estado, observaciones)
VALUES
(4,
 DATE_SUB(CURDATE(), INTERVAL 2 DAY),
 DATE_ADD(CURDATE(), INTERVAL 5 DAY),
 NULL,
 'ACTIVO',
 'Préstamo de demostración activo.');

SET @prestamo4 = LAST_INSERT_ID();

INSERT INTO prestamo_detalle (prestamo_id, libro_id, cantidad) VALUES
(@prestamo4, 4, 1),
(@prestamo4, 10, 2);

-- Préstamo 5 - VENCIDO
INSERT INTO prestamos
(usuario_id, fecha_prestamo, fecha_vencimiento, fecha_devolucion, estado, observaciones)
VALUES
(5,
 DATE_SUB(CURDATE(), INTERVAL 8 DAY),
 DATE_SUB(CURDATE(), INTERVAL 1 DAY),
 NULL,
 'VENCIDO',
 'Préstamo vencido de demostración.');

SET @prestamo5 = LAST_INSERT_ID();

INSERT INTO prestamo_detalle (prestamo_id, libro_id, cantidad) VALUES
(@prestamo5, 11, 1),
(@prestamo5, 13, 1);

-- =========================================================
-- DESCUENTO DE INVENTARIO POR PRESTAMOS NO DEVUELTOS
-- =========================================================
UPDATE libros SET unidades = unidades - 1 WHERE id = 1;
UPDATE libros SET unidades = unidades - 1 WHERE id = 5;

UPDATE libros SET unidades = unidades - 1 WHERE id = 2;
UPDATE libros SET unidades = unidades - 1 WHERE id = 7;

UPDATE libros SET unidades = unidades - 1 WHERE id = 4;
UPDATE libros SET unidades = unidades - 2 WHERE id = 10;

UPDATE libros SET unidades = unidades - 1 WHERE id = 11;
UPDATE libros SET unidades = unidades - 1 WHERE id = 13;