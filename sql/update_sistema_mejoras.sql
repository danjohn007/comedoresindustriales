-- ====================================================================
-- SQL UPDATE SCRIPT - Sistema de Comedores Industriales
-- Fecha: 2024-11-15
-- Descripción: Actualizaciones para nuevas funcionalidades del sistema
-- ====================================================================

-- 1. Crear tabla de proveedores
CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    contacto VARCHAR(150),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion VARCHAR(255),
    ciudad VARCHAR(100) DEFAULT 'Querétaro',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Crear tabla de categorías financieras
CREATE TABLE IF NOT EXISTS categorias_financieras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Crear tabla de transacciones financieras (si no existe)
CREATE TABLE IF NOT EXISTS transacciones_financieras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comedor_id INT,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    concepto VARCHAR(200) NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    categoria_id INT,
    categoria VARCHAR(100),
    fecha_transaccion DATE NOT NULL,
    descripcion TEXT,
    referencia VARCHAR(100),
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comedor_id) REFERENCES comedores(id) ON DELETE SET NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorias_financieras(id) ON DELETE SET NULL,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha_transaccion),
    INDEX idx_tipo (tipo),
    INDEX idx_comedor (comedor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Crear tabla de presupuestos (si no existe)
CREATE TABLE IF NOT EXISTS presupuestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comedor_id INT,
    anio INT NOT NULL,
    mes INT NOT NULL,
    presupuesto_asignado DECIMAL(12,2) NOT NULL,
    presupuesto_gastado DECIMAL(12,2) DEFAULT 0,
    notas TEXT,
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (comedor_id) REFERENCES comedores(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    UNIQUE KEY unique_presupuesto (comedor_id, anio, mes),
    INDEX idx_periodo (anio, mes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Insertar categorías financieras por defecto
INSERT INTO categorias_financieras (nombre, tipo, descripcion) VALUES
-- Categorías de Ingresos
('Subsidio Gubernamental', 'ingreso', 'Ingresos por subsidios del gobierno'),
('Venta de Servicios', 'ingreso', 'Ingresos por venta de comidas'),
('Donaciones', 'ingreso', 'Donaciones recibidas'),
('Otros Ingresos', 'ingreso', 'Otros tipos de ingresos'),

-- Categorías de Egresos
('Compra de Ingredientes', 'egreso', 'Gastos en compra de ingredientes y alimentos'),
('Salarios', 'egreso', 'Pago de salarios al personal'),
('Servicios Públicos', 'egreso', 'Pago de agua, luz, gas, etc.'),
('Mantenimiento', 'egreso', 'Gastos de mantenimiento de instalaciones'),
('Equipo y Utensilios', 'egreso', 'Compra de equipo y utensilios de cocina'),
('Transporte', 'egreso', 'Gastos de transporte y distribución'),
('Otros Gastos', 'egreso', 'Otros tipos de egresos')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- 6. Insertar configuraciones del sistema si no existen
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) VALUES
('color_primario', '#3B82F6', 'string', 'Color primario del sistema (hexadecimal)', 'apariencia'),
('color_secundario', '#10B981', 'string', 'Color secundario del sistema (hexadecimal)', 'apariencia'),
('logo_sistema', '', 'string', 'Ruta del logo del sistema', 'apariencia'),
('nombre_sistema', 'Sistema de Comedores Industriales', 'string', 'Nombre del sistema', 'general'),
('email_sistema', 'admin@comedores.com', 'string', 'Email de contacto del sistema', 'general'),
('formato_fecha', 'd/m/Y', 'string', 'Formato de fecha a mostrar', 'general'),
('zona_horaria', 'America/Mexico_City', 'string', 'Zona horaria del sistema', 'general'),
('registros_por_pagina', '20', 'integer', 'Cantidad de registros por página', 'general'),
('habilitar_notificaciones', '1', 'boolean', 'Habilitar notificaciones del sistema', 'notificaciones'),
('dias_proyeccion_default', '30', 'integer', 'Días por defecto para cálculo de proyecciones', 'operacion')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- 7. Modificar tabla de transacciones para eliminar tipo 'ajuste' (si existe)
-- Esta consulta no genera error si la tabla ya está correctamente configurada
ALTER TABLE transacciones_financieras 
MODIFY COLUMN tipo ENUM('ingreso', 'egreso') NOT NULL;

-- 8. Agregar índices adicionales para mejor rendimiento
CREATE INDEX IF NOT EXISTS idx_fecha_tipo ON transacciones_financieras(fecha_transaccion, tipo);
CREATE INDEX IF NOT EXISTS idx_categoria ON transacciones_financieras(categoria_id);

-- 9. Insertar proveedores de ejemplo (opcional, comentado por defecto)
-- INSERT INTO proveedores (nombre, contacto, telefono, ciudad) VALUES
-- ('Comercializadora de Alimentos del Bajío', 'Juan Pérez', '442-123-4567', 'Querétaro'),
-- ('Distribuidora de Carnes Premium', 'María González', '442-234-5678', 'Querétaro'),
-- ('Verduras y Hortalizas del Centro', 'Carlos Ramírez', '442-345-6789', 'Querétaro');

-- 10. Actualizar tabla de ingredientes para mejor integración con proveedores
-- Agregar columna proveedor_id si no existe
ALTER TABLE ingredientes 
ADD COLUMN IF NOT EXISTS proveedor_id INT DEFAULT NULL,
ADD CONSTRAINT fk_ingrediente_proveedor 
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) 
    ON DELETE SET NULL;

-- 11. Crear tabla para logs de exportación de datos
CREATE TABLE IF NOT EXISTS logs_exportacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    tipo_reporte VARCHAR(50) NOT NULL,
    formato VARCHAR(10) NOT NULL,
    parametros TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    registros_exportados INT DEFAULT 0,
    archivo_generado VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Actualizar permisos y roles si es necesario
-- Esta sección puede personalizarse según las necesidades específicas del sistema

-- ====================================================================
-- FIN DEL SCRIPT DE ACTUALIZACIÓN
-- ====================================================================

-- Verificación de integridad
SELECT 'Script de actualización completado exitosamente' as mensaje;

-- Mostrar resumen de cambios
SELECT 
    'Proveedores' as tabla,
    COUNT(*) as registros
FROM proveedores
UNION ALL
SELECT 
    'Categorías Financieras' as tabla,
    COUNT(*) as registros
FROM categorias_financieras
UNION ALL
SELECT 
    'Configuraciones del Sistema' as tabla,
    COUNT(*) as registros
FROM configuracion_sistema;
