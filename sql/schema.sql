-- Database Schema for Sistema de Gestión para Comedores Industriales
-- Estado: Querétaro, México

-- Create Database
CREATE DATABASE IF NOT EXISTS comedores_industriales CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE comedores_industriales;

-- Table: usuarios (Users)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(150) NOT NULL,
    rol ENUM('admin', 'coordinador', 'chef', 'operativo') DEFAULT 'operativo',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME NULL,
    INDEX idx_username (username),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: comedores (Dining Halls)
CREATE TABLE IF NOT EXISTS comedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(200) NOT NULL,
    ciudad VARCHAR(100) DEFAULT 'Querétaro',
    estado VARCHAR(50) DEFAULT 'Querétaro',
    capacidad_total INT NOT NULL,
    turnos_activos VARCHAR(100) DEFAULT 'matutino,vespertino,nocturno',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ciudad (ciudad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: turnos (Shifts)
CREATE TABLE IF NOT EXISTS turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    descripcion VARCHAR(200),
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lineas_servicio (Service Lines)
CREATE TABLE IF NOT EXISTS lineas_servicio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(200),
    orden_visualizacion INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: asistencia_diaria (Daily Attendance - OPAD-034)
CREATE TABLE IF NOT EXISTS asistencia_diaria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comedor_id INT NOT NULL,
    turno_id INT NOT NULL,
    fecha DATE NOT NULL,
    comensales_proyectados INT NOT NULL,
    comensales_reales INT DEFAULT 0,
    porcentaje_asistencia DECIMAL(5,2),
    observaciones TEXT,
    registrado_por INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comedor_id) REFERENCES comedores(id) ON DELETE CASCADE,
    FOREIGN KEY (turno_id) REFERENCES turnos(id) ON DELETE CASCADE,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    UNIQUE KEY unique_attendance (comedor_id, turno_id, fecha),
    INDEX idx_fecha (fecha),
    INDEX idx_comedor_fecha (comedor_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: situaciones_atipicas (Atypical Situations)
CREATE TABLE IF NOT EXISTS situaciones_atipicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comedor_id INT NOT NULL,
    tipo ENUM('contratacion', 'despido', 'incapacidad', 'evento_especial', 'dia_festivo', 'otro') NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    impacto_comensales INT NOT NULL COMMENT 'Positive or negative number',
    descripcion TEXT NOT NULL,
    turnos_afectados VARCHAR(100),
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comedor_id) REFERENCES comedores(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha_inicio, fecha_fin),
    INDEX idx_comedor (comedor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: recetas (Recipes)
CREATE TABLE IF NOT EXISTS recetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    linea_servicio_id INT NOT NULL,
    descripcion TEXT,
    porciones_base INT DEFAULT 100,
    tiempo_preparacion INT COMMENT 'Minutes',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (linea_servicio_id) REFERENCES lineas_servicio(id) ON DELETE CASCADE,
    INDEX idx_linea (linea_servicio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ingredientes (Ingredients)
CREATE TABLE IF NOT EXISTS ingredientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    unidad_medida VARCHAR(20) NOT NULL COMMENT 'kg, g, l, ml, pzas',
    costo_unitario DECIMAL(10,2),
    proveedor VARCHAR(150),
    activo TINYINT(1) DEFAULT 1,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: receta_ingredientes (Recipe Ingredients - OPAD-025 GRAMAJES)
CREATE TABLE IF NOT EXISTS receta_ingredientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receta_id INT NOT NULL,
    ingrediente_id INT NOT NULL,
    cantidad DECIMAL(10,3) NOT NULL,
    unidad VARCHAR(20) NOT NULL,
    notas VARCHAR(200),
    FOREIGN KEY (receta_id) REFERENCES recetas(id) ON DELETE CASCADE,
    FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_receta_ingrediente (receta_id, ingrediente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ordenes_produccion (Production Orders - OPAD-007)
CREATE TABLE IF NOT EXISTS ordenes_produccion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_orden VARCHAR(50) UNIQUE NOT NULL,
    comedor_id INT NOT NULL,
    turno_id INT NOT NULL,
    fecha_servicio DATE NOT NULL,
    receta_id INT NOT NULL,
    comensales_proyectados INT NOT NULL,
    porciones_calcular INT NOT NULL,
    estado ENUM('pendiente', 'en_proceso', 'completado', 'cancelado') DEFAULT 'pendiente',
    observaciones TEXT,
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (comedor_id) REFERENCES comedores(id) ON DELETE CASCADE,
    FOREIGN KEY (turno_id) REFERENCES turnos(id) ON DELETE CASCADE,
    FOREIGN KEY (receta_id) REFERENCES recetas(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha_servicio (fecha_servicio),
    INDEX idx_estado (estado),
    INDEX idx_comedor_fecha (comedor_id, fecha_servicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: orden_ingredientes (Production Order Ingredients Detail)
CREATE TABLE IF NOT EXISTS orden_ingredientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_produccion_id INT NOT NULL,
    ingrediente_id INT NOT NULL,
    cantidad_requerida DECIMAL(10,3) NOT NULL,
    unidad VARCHAR(20) NOT NULL,
    costo_estimado DECIMAL(10,2),
    FOREIGN KEY (orden_produccion_id) REFERENCES ordenes_produccion(id) ON DELETE CASCADE,
    FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: proyecciones (Projections)
CREATE TABLE IF NOT EXISTS proyecciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comedor_id INT NOT NULL,
    turno_id INT NOT NULL,
    fecha DATE NOT NULL,
    comensales_proyectados INT NOT NULL,
    metodo_calculo ENUM('historico', 'ajuste_manual', 'estacional') DEFAULT 'historico',
    margen_error DECIMAL(5,2) DEFAULT 5.00,
    ajuste_aplicado INT DEFAULT 0,
    justificacion_ajuste TEXT,
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comedor_id) REFERENCES comedores(id) ON DELETE CASCADE,
    FOREIGN KEY (turno_id) REFERENCES turnos(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    UNIQUE KEY unique_projection (comedor_id, turno_id, fecha),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: configuracion_sistema (System Configuration - REQ-CONFIG-001)
CREATE TABLE IF NOT EXISTS configuracion_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT NOT NULL,
    tipo_dato ENUM('string', 'integer', 'decimal', 'boolean', 'json') DEFAULT 'string',
    descripcion VARCHAR(255),
    categoria VARCHAR(50),
    modificado_por INT,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (modificado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: logs_sistema (System Logs)
CREATE TABLE IF NOT EXISTS logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    descripcion TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha_hora),
    INDEX idx_usuario (usuario_id),
    INDEX idx_modulo (modulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: api_tokens (API Tokens for External Integration - REQ-API-001)
CREATE TABLE IF NOT EXISTS api_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(64) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    permisos TEXT COMMENT 'JSON array of permissions',
    activo TINYINT(1) DEFAULT 1,
    fecha_expiracion DATE,
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_uso DATETIME NULL,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_token (token),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
