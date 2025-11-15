-- ====================================================================
-- SQL UPDATE SCRIPT - Mejoras del Sistema de Comedores Industriales
-- Fecha: 2025-11-15
-- Descripción: Actualización para nuevas funcionalidades implementadas
-- ====================================================================

-- =========================
-- 1) Verificar y crear tablas necesarias
-- =========================

-- Crear tabla proveedores si no existe
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

-- Crear tabla categorias_financieras si no existe
CREATE TABLE IF NOT EXISTS categorias_financieras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('ingreso','egreso') NOT NULL,
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla logs_exportacion si no existe
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

-- =========================
-- 2) Ajustes en tabla ingredientes
-- =========================

-- Agregar columna proveedor_id si no existe
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'ingredientes' 
    AND COLUMN_NAME = 'proveedor_id');

SET @sql_add_col = IF(@col_exists = 0, 
    'ALTER TABLE ingredientes ADD COLUMN proveedor_id INT DEFAULT NULL AFTER proveedor',
    'SELECT "proveedor_id ya existe" AS message');
PREPARE stmt FROM @sql_add_col;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Migrar datos de proveedor (texto) a tabla proveedores y actualizar proveedor_id
SET @col_proveedor_text = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'ingredientes' 
    AND COLUMN_NAME = 'proveedor');

SET @sql_migrate = IF(@col_proveedor_text = 1,
    'INSERT INTO proveedores (nombre, ciudad, activo, fecha_creacion)
     SELECT DISTINCT TRIM(proveedor), "Querétaro", 1, NOW()
     FROM ingredientes
     WHERE proveedor IS NOT NULL AND TRIM(proveedor) != ""
       AND NOT EXISTS (SELECT 1 FROM proveedores p WHERE p.nombre = TRIM(ingredientes.proveedor))',
    'SELECT "columna proveedor no existe" AS message');
PREPARE stmt FROM @sql_migrate;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Actualizar proveedor_id basado en nombre
SET @sql_update_id = IF(@col_proveedor_text = 1,
    'UPDATE ingredientes ig
     LEFT JOIN proveedores p ON p.nombre = TRIM(ig.proveedor)
     SET ig.proveedor_id = p.id
     WHERE ig.proveedor IS NOT NULL AND TRIM(ig.proveedor) != ""',
    'SELECT "no se requiere actualización" AS message');
PREPARE stmt FROM @sql_update_id;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar foreign key de proveedor_id si no existe
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS tc
    JOIN information_schema.KEY_COLUMN_USAGE kcu USING(CONSTRAINT_NAME, CONSTRAINT_SCHEMA)
    WHERE tc.CONSTRAINT_SCHEMA = DATABASE()
      AND tc.TABLE_NAME = 'ingredientes'
      AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
      AND kcu.COLUMN_NAME = 'proveedor_id');

SET @sql_add_fk = IF(@fk_exists = 0 AND @col_exists = 1,
    'ALTER TABLE ingredientes ADD CONSTRAINT fk_ingrediente_proveedor 
     FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL',
    'SELECT "FK ya existe o columna no existe" AS message');
PREPARE stmt FROM @sql_add_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =========================
-- 3) Ajustes en tabla transacciones_financieras
-- =========================

-- Agregar columna categoria_id si no existe
SET @col_cat_id = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'transacciones_financieras' 
    AND COLUMN_NAME = 'categoria_id');

SET @sql_add_cat = IF(@col_cat_id = 0,
    'ALTER TABLE transacciones_financieras ADD COLUMN categoria_id INT DEFAULT NULL AFTER categoria',
    'SELECT "categoria_id ya existe" AS message');
PREPARE stmt FROM @sql_add_cat;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Migrar categorías existentes (texto) a tabla categorias_financieras
SET @col_cat_text = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'transacciones_financieras' 
    AND COLUMN_NAME = 'categoria');

SET @sql_migrate_cat = IF(@col_cat_text = 1,
    'INSERT INTO categorias_financieras (nombre, tipo, descripcion)
     SELECT DISTINCT TRIM(categoria), "egreso", CONCAT("Migrada automáticamente desde transacciones")
     FROM transacciones_financieras
     WHERE categoria IS NOT NULL AND TRIM(categoria) != ""
       AND NOT EXISTS (SELECT 1 FROM categorias_financieras c WHERE c.nombre = TRIM(transacciones_financieras.categoria))',
    'SELECT "columna categoria no existe" AS message');
PREPARE stmt FROM @sql_migrate_cat;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Actualizar categoria_id basado en nombre
SET @sql_update_cat = IF(@col_cat_text = 1 AND @col_cat_id = 1,
    'UPDATE transacciones_financieras tf
     LEFT JOIN categorias_financieras cf ON cf.nombre = TRIM(tf.categoria)
     SET tf.categoria_id = cf.id
     WHERE tf.categoria IS NOT NULL AND TRIM(tf.categoria) != ""',
    'SELECT "no se requiere actualización de categorias" AS message');
PREPARE stmt FROM @sql_update_cat;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar foreign key de categoria_id si no existe
SET @fk_cat_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS tc
    JOIN information_schema.KEY_COLUMN_USAGE kcu USING(CONSTRAINT_NAME, CONSTRAINT_SCHEMA)
    WHERE tc.CONSTRAINT_SCHEMA = DATABASE()
      AND tc.TABLE_NAME = 'transacciones_financieras'
      AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
      AND kcu.COLUMN_NAME = 'categoria_id');

SET @sql_add_fk_cat = IF(@fk_cat_exists = 0 AND @col_cat_id = 1,
    'ALTER TABLE transacciones_financieras ADD CONSTRAINT fk_transaccion_categoria 
     FOREIGN KEY (categoria_id) REFERENCES categorias_financieras(id) ON DELETE SET NULL',
    'SELECT "FK categoria ya existe o columna no existe" AS message');
PREPARE stmt FROM @sql_add_fk_cat;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =========================
-- 4) Población de categorías financieras por defecto
-- =========================

-- Categorías de Ingresos
INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Subsidio Gubernamental', 'ingreso', 'Ingresos por subsidios del gobierno'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Subsidio Gubernamental');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Venta de Servicios', 'ingreso', 'Ingresos por venta de comidas'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Venta de Servicios');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Donaciones', 'ingreso', 'Donaciones recibidas'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Donaciones');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Otros Ingresos', 'ingreso', 'Otros tipos de ingresos'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Otros Ingresos');

-- Categorías de Egresos
INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Compra de Ingredientes', 'egreso', 'Gastos en compra de ingredientes y alimentos'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Compra de Ingredientes');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Salarios', 'egreso', 'Pago de salarios al personal'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Salarios');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Servicios Públicos', 'egreso', 'Pago de agua, luz, gas, etc.'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Servicios Públicos');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Mantenimiento', 'egreso', 'Gastos de mantenimiento de instalaciones'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Mantenimiento');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Equipo y Utensilios', 'egreso', 'Compra de equipo y utensilios de cocina'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Equipo y Utensilios');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Transporte', 'egreso', 'Gastos de transporte y distribución'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Transporte');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Otros Gastos', 'egreso', 'Otros tipos de egresos'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Otros Gastos');

-- =========================
-- 5) Mensaje de finalización
-- =========================
SELECT 'Script de actualización ejecutado correctamente. Verifique los mensajes anteriores para confirmar que todas las tablas y columnas se crearon/actualizaron correctamente.' AS resultado;
