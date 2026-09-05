-- =====================================================================
-- SISTEMA ADMINISTRATIVO WEB - SCRIPT DE BASE DE DATOS
-- Motor: MySQL / MariaDB
-- Módulos: Usuarios, Estudiantes, Cursos, Matrículas
-- =====================================================================

DROP DATABASE IF EXISTS sistema_administrativo;
CREATE DATABASE sistema_administrativo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_administrativo;

-- ---------------------------------------------------------------------
-- Tabla: usuarios
-- Usuarios que pueden autenticarse en el sistema (administradores/staff)
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,          -- almacenada con password_hash() (bcrypt)
    rol ENUM('admin','secretaria','docente') NOT NULL DEFAULT 'secretaria',
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabla: estudiantes
-- ---------------------------------------------------------------------
CREATE TABLE estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(15) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    fecha_nacimiento DATE DEFAULT NULL,
    direccion VARCHAR(200) DEFAULT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabla: cursos
-- ---------------------------------------------------------------------
CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT DEFAULT NULL,
    docente VARCHAR(100) DEFAULT NULL,
    creditos INT NOT NULL DEFAULT 0,
    horas INT NOT NULL DEFAULT 0,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabla: matriculas (tabla intermedia -> relación N:M entre estudiantes y cursos)
-- ---------------------------------------------------------------------
CREATE TABLE matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    curso_id INT NOT NULL,
    fecha_matricula DATE NOT NULL,
    estado ENUM('matriculado','retirado','culminado') NOT NULL DEFAULT 'matriculado',
    observaciones VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_matricula_estudiante FOREIGN KEY (estudiante_id)
        REFERENCES estudiantes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_matricula_curso FOREIGN KEY (curso_id)
        REFERENCES cursos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT uq_estudiante_curso UNIQUE (estudiante_id, curso_id) -- evita doble matrícula al mismo curso
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- VISTA: vista_matriculas
-- Une matriculas + estudiantes + cursos para reportes y listados rápidos
-- ---------------------------------------------------------------------
CREATE OR REPLACE VIEW vista_matriculas AS
SELECT
    m.id                    AS matricula_id,
    e.id                    AS estudiante_id,
    CONCAT(e.nombres, ' ', e.apellidos) AS estudiante,
    e.dni                   AS dni_estudiante,
    c.id                    AS curso_id,
    c.nombre                AS curso,
    c.codigo                AS codigo_curso,
    c.docente               AS docente,
    m.fecha_matricula,
    m.estado                AS estado_matricula
FROM matriculas m
INNER JOIN estudiantes e ON e.id = m.estudiante_id
INNER JOIN cursos c      ON c.id = m.curso_id;

-- ---------------------------------------------------------------------
-- Índices adicionales para optimizar búsquedas frecuentes
-- ---------------------------------------------------------------------
CREATE INDEX idx_estudiante_apellidos ON estudiantes(apellidos);
CREATE INDEX idx_curso_nombre ON cursos(nombre);
CREATE INDEX idx_matricula_estado ON matriculas(estado);

-- ---------------------------------------------------------------------
-- DATOS INICIALES
-- ---------------------------------------------------------------------

-- Usuario administrador por defecto
-- Usuario: admin@sistema.com   |   Contraseña: admin123
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Administrador', 'admin@sistema.com', '$2b$10$RAaGlYsue4fmxz7OhfiSceW9wf.fd.bmFkppPrQ5eDMKNWcPTA2Au', 'admin');

-- Estudiantes de ejemplo
INSERT INTO estudiantes (dni, nombres, apellidos, email, telefono, fecha_nacimiento, direccion) VALUES
('70011122', 'Ana Lucía', 'Ramírez Torres', 'ana.ramirez@correo.com', '987654321', '2001-05-14', 'Av. Los Álamos 123'),
('70022233', 'Carlos Andrés', 'Quispe Mamani', 'carlos.quispe@correo.com', '987654322', '2000-11-02', 'Jr. Las Rosas 456'),
('70033344', 'María Fernanda', 'Torres Loayza', 'maria.torres@correo.com', '987654323', '2002-02-20', 'Calle Sol 789');

-- Cursos de ejemplo
INSERT INTO cursos (codigo, nombre, descripcion, docente, creditos, horas) VALUES
('SIS101', 'Programación Web', 'Fundamentos de desarrollo web con PHP y MySQL', 'Ing. Jorge Salinas', 4, 64),
('SIS102', 'Base de Datos I', 'Modelado y normalización de bases de datos relacionales', 'Ing. Rosa Medina', 4, 64),
('SIS103', 'Estructura de Datos', 'Algoritmos y estructuras de datos fundamentales', 'Ing. Luis Fernández', 3, 48);

-- Matrículas de ejemplo
INSERT INTO matriculas (estudiante_id, curso_id, fecha_matricula, estado) VALUES
(1, 1, '2026-03-01', 'matriculado'),
(1, 2, '2026-03-01', 'matriculado'),
(2, 1, '2026-03-02', 'matriculado'),
(3, 3, '2026-03-03', 'culminado');
