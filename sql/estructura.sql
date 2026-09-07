-- 1. Tabla USUARIOS
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'profesor') DEFAULT 'profesor',
    activo TINYINT(1) DEFAULT 1
);

-- 2. Tabla RECURSOS (Con control de stock)
CREATE TABLE recursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('sala', 'lab', 'equipo') NOT NULL,
    descripcion TEXT,
    stock_total INT NOT NULL DEFAULT 1, -- NUEVO
    activo TINYINT(1) DEFAULT 1
);

-- 3. Tabla RESERVAS (Con cantidad solicitada)
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_recurso INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,    -- NUEVO
    estado ENUM('pendiente', 'aprobada', 'rechazada') DEFAULT 'pendiente',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_recurso) REFERENCES recursos(id)
);
