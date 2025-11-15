-- SQL Update Script for System Improvements
-- Fecha: 2025-11-15
-- Actualización del sistema para nuevas funcionalidades

USE comedores_industriales;

-- 1. Actualizar la tabla usuarios para incluir el rol "cliente"
ALTER TABLE usuarios 
MODIFY COLUMN rol ENUM('admin', 'coordinador', 'chef', 'operativo', 'cliente') DEFAULT 'operativo';

-- 2. Crear tabla para recuperación de contraseñas
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(100) UNIQUE NOT NULL,
    expira_en DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expira (expira_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Crear tabla para módulo financiero - transacciones
CREATE TABLE IF NOT EXISTS transacciones_financieras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comedor_id INT NOT NULL,
    tipo ENUM('ingreso', 'egreso', 'ajuste') NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    categoria VARCHAR(100),
    fecha_transaccion DATE NOT NULL,
    orden_produccion_id INT NULL,
    descripcion TEXT,
    comprobante_path VARCHAR(255),
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comedor_id) REFERENCES comedores(id) ON DELETE CASCADE,
    FOREIGN KEY (orden_produccion_id) REFERENCES ordenes_produccion(id) ON DELETE SET NULL,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha_transaccion),
    INDEX idx_comedor (comedor_id),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Crear tabla para presupuestos
CREATE TABLE IF NOT EXISTS presupuestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comedor_id INT NOT NULL,
    anio INT NOT NULL,
    mes INT NOT NULL,
    presupuesto_asignado DECIMAL(12,2) NOT NULL,
    presupuesto_gastado DECIMAL(12,2) DEFAULT 0,
    porcentaje_ejecutado DECIMAL(5,2),
    estado ENUM('activo', 'cerrado', 'excedido') DEFAULT 'activo',
    notas TEXT,
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (comedor_id) REFERENCES comedores(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    UNIQUE KEY unique_presupuesto (comedor_id, anio, mes),
    INDEX idx_periodo (anio, mes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Crear tabla para configuración de correo (almacenamiento seguro)
CREATE TABLE IF NOT EXISTS configuracion_correo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    smtp_host VARCHAR(255) NOT NULL,
    smtp_port INT NOT NULL,
    smtp_user VARCHAR(255) NOT NULL,
    smtp_password VARCHAR(255) NOT NULL,
    smtp_encryption VARCHAR(10) DEFAULT 'tls',
    from_email VARCHAR(255) NOT NULL,
    from_name VARCHAR(255) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Insertar configuración de correo
INSERT INTO configuracion_correo (smtp_host, smtp_port, smtp_user, smtp_password, smtp_encryption, from_email, from_name, activo) 
VALUES ('majorbot.digital', 465, 'comedores@majorbot.digital', 'Danjohn007', 'ssl', 'comedores@majorbot.digital', 'Sistema Comedores Industriales', 1);

-- 7. Actualizar usuarios existentes (opcional - agregar un usuario cliente de ejemplo)
-- INSERT INTO usuarios (username, email, password, nombre_completo, rol, activo) VALUES
-- ('cliente1', 'cliente1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuario Cliente de Prueba', 'cliente', 1);

-- 8. Agregar configuración para módulo financiero
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) VALUES
('modulo_financiero_activo', 'true', 'boolean', 'Activar módulo financiero', 'financiero'),
('moneda_sistema', 'MXN', 'string', 'Moneda del sistema', 'financiero'),
('simbolo_moneda', '$', 'string', 'Símbolo de la moneda', 'financiero'),
('iva_porcentaje', '16.0', 'decimal', 'Porcentaje de IVA', 'financiero')
ON DUPLICATE KEY UPDATE valor=valor;

-- Fin del script de actualización
-- NOTA: Este script es idempotente y puede ejecutarse múltiples veces sin causar errores
