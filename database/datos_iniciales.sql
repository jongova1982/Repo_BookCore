-- BookCore - datos iniciales para Alwaysdata
-- Selecciona la base jongox_bookcore_db antes de ejecutar este archivo.
-- Requiere haber importado primero database/bookcore.sql.

SET NAMES utf8mb4;
START TRANSACTION;

-- 20 usuarios ficticios
INSERT INTO BKusuarios_db (Nombre, cedula, telefono, activo) VALUES
('Ana Maria Torres', '1000000001', '3000000001', 1),
('Carlos Andres Rojas', '1000000002', '3000000002', 1),
('Laura Sofia Martinez', '1000000003', '3000000003', 1),
('Juan David Herrera', '1000000004', '3000000004', 1),
('Valentina Gomez Ruiz', '1000000005', '3000000005', 1),
('Sebastian Castro', '1000000006', '3000000006', 1),
('Daniela Rodriguez', '1000000007', '3000000007', 1),
('Miguel Angel Lopez', '1000000008', '3000000008', 1),
('Camila Fernanda Diaz', '1000000009', '3000000009', 1),
('Santiago Ramirez', '1000000010', '3000000010', 1),
('Isabella Moreno', '1000000011', '3000000011', 1),
('Nicolas Hernandez', '1000000012', '3000000012', 1),
('Mariana Vargas', '1000000013', '3000000013', 1),
('Andres Felipe Silva', '1000000014', '3000000014', 1),
('Gabriela Sanchez', '1000000015', '3000000015', 1),
('Mateo Gutierrez', '1000000016', '3000000016', 1),
('Natalia Jimenez', '1000000017', '3000000017', 1),
('Alejandro Mendoza', '1000000018', '3000000018', 1),
('Paula Andrea Ortiz', '1000000019', '3000000019', 1),
('Daniel Esteban Cruz', '1000000020', '3000000020', 1)
ON DUPLICATE KEY UPDATE
    Nombre = VALUES(Nombre),
    telefono = VALUES(telefono),
    activo = 1;

-- 50 libros
INSERT INTO BKlibros_db (codigo, titulo, autor, unidades, activo) VALUES
('BK-0001', 'Cien años de soledad', 'Gabriel Garcia Marquez', 5, 1),
('BK-0002', 'El amor en los tiempos del colera', 'Gabriel Garcia Marquez', 4, 1),
('BK-0003', 'Cronica de una muerte anunciada', 'Gabriel Garcia Marquez', 3, 1),
('BK-0004', 'Don Quijote de la Mancha', 'Miguel de Cervantes', 6, 1),
('BK-0005', 'La sombra del viento', 'Carlos Ruiz Zafon', 4, 1),
('BK-0006', 'El principito', 'Antoine de Saint-Exupery', 8, 1),
('BK-0007', '1984', 'George Orwell', 5, 1),
('BK-0008', 'Rebelion en la granja', 'George Orwell', 4, 1),
('BK-0009', 'Orgullo y prejuicio', 'Jane Austen', 3, 1),
('BK-0010', 'Jane Eyre', 'Charlotte Bronte', 3, 1),
('BK-0011', 'Crimen y castigo', 'Fiodor Dostoievski', 4, 1),
('BK-0012', 'Los hermanos Karamazov', 'Fiodor Dostoievski', 2, 1),
('BK-0013', 'El extranjero', 'Albert Camus', 4, 1),
('BK-0014', 'La metamorfosis', 'Franz Kafka', 5, 1),
('BK-0015', 'Rayuela', 'Julio Cortazar', 3, 1),
('BK-0016', 'Ficciones', 'Jorge Luis Borges', 3, 1),
('BK-0017', 'Pedro Paramo', 'Juan Rulfo', 4, 1),
('BK-0018', 'La casa de los espiritus', 'Isabel Allende', 3, 1),
('BK-0019', 'Como agua para chocolate', 'Laura Esquivel', 4, 1),
('BK-0020', 'La ciudad y los perros', 'Mario Vargas Llosa', 3, 1),
('BK-0021', 'El nombre de la rosa', 'Umberto Eco', 2, 1),
('BK-0022', 'El retrato de Dorian Gray', 'Oscar Wilde', 4, 1),
('BK-0023', 'Dracula', 'Bram Stoker', 5, 1),
('BK-0024', 'Frankenstein', 'Mary Shelley', 5, 1),
('BK-0025', 'Moby Dick', 'Herman Melville', 2, 1),
('BK-0026', 'Las aventuras de Tom Sawyer', 'Mark Twain', 4, 1),
('BK-0027', 'Veinte mil leguas de viaje submarino', 'Julio Verne', 3, 1),
('BK-0028', 'Viaje al centro de la Tierra', 'Julio Verne', 4, 1),
('BK-0029', 'La isla del tesoro', 'Robert Louis Stevenson', 4, 1),
('BK-0030', 'Alicia en el pais de las maravillas', 'Lewis Carroll', 5, 1),
('BK-0031', 'El señor de los anillos', 'J. R. R. Tolkien', 3, 1),
('BK-0032', 'El hobbit', 'J. R. R. Tolkien', 5, 1),
('BK-0033', 'Harry Potter y la piedra filosofal', 'J. K. Rowling', 6, 1),
('BK-0034', 'Las cronicas de Narnia', 'C. S. Lewis', 4, 1),
('BK-0035', 'Dune', 'Frank Herbert', 3, 1),
('BK-0036', 'Fahrenheit 451', 'Ray Bradbury', 5, 1),
('BK-0037', 'Un mundo feliz', 'Aldous Huxley', 4, 1),
('BK-0038', 'Fundacion', 'Isaac Asimov', 3, 1),
('BK-0039', 'Yo, robot', 'Isaac Asimov', 4, 1),
('BK-0040', 'Sapiens: De animales a dioses', 'Yuval Noah Harari', 5, 1),
('BK-0041', 'Breve historia del tiempo', 'Stephen Hawking', 3, 1),
('BK-0042', 'Cosmos', 'Carl Sagan', 4, 1),
('BK-0043', 'El origen de las especies', 'Charles Darwin', 2, 1),
('BK-0044', 'El arte de la guerra', 'Sun Tzu', 5, 1),
('BK-0045', 'Meditaciones', 'Marco Aurelio', 4, 1),
('BK-0046', 'La republica', 'Platon', 3, 1),
('BK-0047', 'Etica para Amador', 'Fernando Savater', 4, 1),
('BK-0048', 'El hombre en busca de sentido', 'Viktor Frankl', 5, 1),
('BK-0049', 'Habitos atomicos', 'James Clear', 6, 1),
('BK-0050', 'El poder de los habitos', 'Charles Duhigg', 4, 1)
ON DUPLICATE KEY UPDATE
    titulo = VALUES(titulo),
    autor = VALUES(autor),
    unidades = VALUES(unidades),
    activo = 1;

COMMIT;

-- Verificacion
SELECT COUNT(*) AS usuarios_activos FROM BKusuarios_db WHERE activo = 1;
SELECT COUNT(*) AS libros_activos FROM BKlibros_db WHERE activo = 1;
