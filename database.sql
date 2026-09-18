-- =====================================================
-- BASE DE DATOS: Biblioteca Cloud
-- Ejecuta este script en phpMyAdmin de AlwaysData
-- =====================================================

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(150) NOT NULL,
    unidades INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    libro_id INT NOT NULL,
    fecha DATE NOT NULL,
    estado ENUM('activo', 'devuelto') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo
INSERT INTO usuarios (nombre, cedula, telefono) VALUES
('Ana María López', '1234567890', '3001234567'),
('Carlos Andrés Ruiz', '0987654321', '3109876543'),
('Laura Sofía Gómez', '1122334455', '3201122334');

INSERT INTO libros (codigo, titulo, autor, unidades) VALUES
('LIB-001', 'Cien años de soledad', 'Gabriel García Márquez', 5),
('LIB-002', 'El amor en los tiempos del cólera', 'Gabriel García Márquez', 3),
('LIB-003', 'La casa de los espíritus', 'Isabel Allende', 4),
('LIB-004', 'Rayuela', 'Julio Cortázar', 2);

INSERT INTO prestamos (usuario_id, libro_id, fecha, estado) VALUES
(1, 1, '2026-09-10', 'activo'),
(2, 3, '2026-09-12', 'activo');
