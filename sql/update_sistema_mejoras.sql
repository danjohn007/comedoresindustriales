-- ====================================================================
-- SQL UPDATE SCRIPT - Sistema de Comedores Industriales
-- Versión final generada para la base de datos actual (MySQL 5.7)
-- Fecha: 2025-11-15
-- Descripción:
--  - Compatible con MySQL 5.7.x
--  - Evita IF ... THEN en nivel de script (no usa procedimientos)
--  - Usa sentencias PREPARE/EXECUTE para DDL condicionales
--  - Normaliza/elimina tipo 'ajuste' en transacciones_financieras
--  - Añade columna categoria_id en transacciones_financieras si falta
--  - Migra valores texto de categoria hacia categorias_financieras y rellena categoria_id
--  - Añade columna proveedor_id en ingredientes si falta, migra y crea FK
--  - Crea tablas faltantes: proveedores, categorias_financieras, logs_exportacion
-- NOTA IMPORTANTE:
--  - Ejecuta este script desde la consola mysql (mysql CLI) con un usuario que tenga permisos
--    CREATE, ALTER, INDEX, INSERT, UPDATE y REFERENCES. Algunas GUIs (phpMyAdmin) pueden
--    bloquear PREPARE/EXECUTE o producir advertencias; si usas phpMyAdmin, pega y ejecuta
--    por secciones según los comentarios.
-- ====================================================================

/* =========================
   1) Tablas nuevas si faltan
   ========================= */

-- 1.1 Crear tabla proveedores
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

-- 1.2 Crear tabla categorias_financieras
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

-- 1.3 Crear tabla logs_exportacion
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
-- 2) Población de categorías por defecto
-- =========================
INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Subsidio Gubernamental','ingreso','Ingresos por subsidios del gobierno'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Subsidio Gubernamental');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Venta de Servicios','ingreso','Ingresos por venta de comidas'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Venta de Servicios');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Donaciones','ingreso','Donaciones recibidas'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Donaciones');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Otros Ingresos','ingreso','Otros tipos de ingresos'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Otros Ingresos');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Compra de Ingredientes','egreso','Gastos en compra de ingredientes y alimentos'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Compra de Ingredientes');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Salarios','egreso','Pago de salarios al personal'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Salarios');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Servicios Públicos','egreso','Pago de agua, luz, gas, etc.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Servicios Públicos');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Mantenimiento','egreso','Gastos de mantenimiento de instalaciones'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Mantenimiento');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Equipo y Utensilios','egreso','Compra de equipo y utensilios de cocina'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Equipo y Utensilios');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Transporte','egreso','Gastos de transporte y distribución'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Transporte');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Otros Gastos','egreso','Otros tipos de egresos'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre='Otros Gastos');

-- =========================
-- 3) Asegurar y ajustar transacciones_financieras
-- =========================

-- 3.0 Comprobar existencia de la tabla
SET @tbl_exists = (SELECT COUNT(1) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'transacciones_financieras');

-- 3.1 Si la tabla no existe, crearla (versión con categoria_id incluida)
SET @create_stmt = IF(@tbl_exists = 0,
  'CREATE TABLE transacciones_financieras (
      id INT AUTO_INCREMENT PRIMARY KEY,
      comedor_id INT,
      tipo ENUM(''ingreso'',''egreso'') NOT NULL,
      concepto VARCHAR(200) NOT NULL,
      monto DECIMAL(12,2) NOT NULL,
      categoria_id INT DEFAULT NULL,
      categoria VARCHAR(100),
      fecha_transaccion DATE NOT NULL,
      descripcion TEXT,
      referencia VARCHAR(100),
      orden_produccion_id INT DEFAULT NULL,
      creado_por INT,
      fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (comedor_id) REFERENCES comedores(id) ON DELETE SET NULL,
      FOREIGN KEY (categoria_id) REFERENCES categorias_financieras(id) ON DELETE SET NULL,
      FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
      INDEX idx_fecha (fecha_transaccion),
      INDEX idx_tipo (tipo),
      INDEX idx_comedor (comedor_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
  'SELECT \"tabla existe\"'
);
PREPARE stmt FROM @create_stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.2 Normalizar/eliminar tipo 'ajuste' si la columna existe
SET @col_tipo_exists = (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'transacciones_financieras' AND column_name = 'tipo');
SET @rows_ajuste = 0;
SET @s = IF(@col_tipo_exists = 1,
    'SELECT COUNT(1) INTO @rows_ajuste FROM transacciones_financieras WHERE tipo = ''ajuste''',
    'SELECT 0 INTO @rows_ajuste');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Si había filas con 'ajuste', convertirlas a 'egreso'
SET @s = IF(@rows_ajuste > 0,
    'UPDATE transacciones_financieras SET tipo = ''egreso'' WHERE tipo = ''ajuste''',
    'SELECT ''no hay ajustes que actualizar''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.3 Modificar ENUM para eliminar 'ajuste' (si la columna existe)
SET @s = IF(@col_tipo_exists = 1,
    'ALTER TABLE transacciones_financieras MODIFY COLUMN tipo ENUM(''ingreso'',''egreso'') NOT NULL',
    'SELECT ''skip alter tipo''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.4 Añadir columna categoria_id si no existe
SET @col_cat_exists = (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'transacciones_financieras' AND column_name = 'categoria_id');
SET @s = IF(@col_cat_exists = 0,
    'ALTER TABLE transacciones_financieras ADD COLUMN categoria_id INT NULL',
    'SELECT ''categoria_id ya existe''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.5 Migrar valores texto de 'categoria' hacia categorias_financieras y poblar categoria_id (si columna categoria existe)
SET @col_cat_text = (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'transacciones_financieras' AND column_name = 'categoria');
SET @col_cat_text = IFNULL(@col_cat_text, 0);

-- Insertar categorías únicas desde transacciones_financieras (si existe la columna)
SET @s = IF(@col_cat_text = 1,
  'INSERT INTO categorias_financieras (nombre, tipo, descripcion)
   SELECT t.nombre, ''egreso'', CONCAT(''Importada desde transacciones_financieras ('', DATE_FORMAT(NOW(), ''%Y-%m-%d''), '')'')
   FROM (SELECT DISTINCT TRIM(categoria) AS nombre FROM transacciones_financieras WHERE categoria IS NOT NULL AND TRIM(categoria) <> '''') AS t
   WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras c WHERE c.nombre = t.nombre)',
  'SELECT ''skip insert categorias desde transacciones'''
);
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Actualizar categoria_id uniendo por nombre (si ambas columnas existen)
SET @col_cat_id_exists = (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'transacciones_financieras' AND column_name = 'categoria_id');
SET @s = IF(@col_cat_text = 1 AND @col_cat_id_exists = 1,
    'UPDATE transacciones_financieras tf
     LEFT JOIN categorias_financieras cf ON cf.nombre = TRIM(tf.categoria)
     SET tf.categoria_id = cf.id
     WHERE tf.categoria IS NOT NULL AND TRIM(tf.categoria) <> ''''',
    'SELECT ''skip update categoria_id''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.6 Añadir FK sobre categoria_id si no existe y si categorias_financieras.id existe
SET @fk_exists = (SELECT COUNT(1) FROM information_schema.table_constraints tc
                  JOIN information_schema.key_column_usage kcu USING(constraint_name,constraint_schema)
                  WHERE tc.constraint_schema = DATABASE()
                    AND tc.table_name = 'transacciones_financieras'
                    AND tc.constraint_type = 'FOREIGN KEY'
                    AND kcu.column_name = 'categoria_id');
SET @cat_id_col = (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'categorias_financieras' AND column_name = 'id');

SET @s = IF(@fk_exists = 0 AND @cat_id_col = 1,
    'ALTER TABLE transacciones_financieras ADD CONSTRAINT fk_transf_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_financieras(id) ON DELETE SET NULL',
    'SELECT ''skip fk categoria''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.7 Crear índice idx_categoria si no existe y la columna existe
SET @idx_cat_exists = (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'transacciones_financieras' AND index_name = 'idx_categoria');
SET @s = IF(@idx_cat_exists = 0 AND @col_cat_id_exists = 1,
    'CREATE INDEX idx_categoria ON transacciones_financieras (categoria_id)',
    'SELECT ''skip create idx_categoria''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.8 Crear índice idx_fecha_tipo si no existe y ambas columnas existen
SET @idx_ft_exists = (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'transacciones_financieras' AND index_name = 'idx_fecha_tipo');
SET @col_fecha_exists = (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'transacciones_financieras' AND column_name = 'fecha_transaccion');
SET @col_tipo_exists2 = (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'transacciones_financieras' AND column_name = 'tipo');
SET @s = IF(@idx_ft_exists = 0 AND @col_fecha_exists = 1 AND @col_tipo_exists2 = 1,
    'CREATE INDEX idx_fecha_tipo ON transacciones_financieras (fecha_transaccion, tipo)',
    'SELECT ''skip create idx_fecha_tipo''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =========================
-- 4) Ajustes en ingredientes y proveedores
-- =========================

-- 4.0 Comprobar si tabla ingredientes existe
SET @tbl_ing_exists = (SELECT COUNT(1) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'ingredientes');

-- 4.1 Añadir columna proveedor_id si no existe
SET @col_prov_id = (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'ingredientes' AND column_name = 'proveedor_id');
SET @s = IF(@tbl_ing_exists = 1 AND @col_prov_id = 0,
    'ALTER TABLE ingredientes ADD COLUMN proveedor_id INT DEFAULT NULL',
    'SELECT ''skip add proveedor_id''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4.2 Insertar proveedores desde ingredientes.proveedor (si existe la columna proveedor)
SET @col_prov_text = (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'ingredientes' AND column_name = 'proveedor');
SET @s = IF(@tbl_ing_exists = 1 AND @col_prov_text = 1,
    'INSERT INTO proveedores (nombre, ciudad, activo, fecha_creacion)
     SELECT DISTINCT TRIM(proveedor), ''Querétaro'', 1, NOW()
     FROM ingredientes
     WHERE proveedor IS NOT NULL AND TRIM(proveedor) <> ''''
       AND NOT EXISTS (SELECT 1 FROM proveedores p WHERE p.nombre = TRIM(ingredientes.proveedor))',
    'SELECT ''skip insert proveedores desde ingredientes''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4.3 Actualizar proveedor_id en ingredientes haciendo join por nombre (si procede)
SET @s = IF(@tbl_ing_exists = 1 AND @col_prov_text = 1,
    'UPDATE ingredientes ig
     LEFT JOIN proveedores p ON p.nombre = TRIM(ig.proveedor)
     SET ig.proveedor_id = p.id
     WHERE ig.proveedor IS NOT NULL AND TRIM(ig.proveedor) <> ''''',
    'SELECT ''skip update proveedor_id''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4.4 Añadir FK fk_ingrediente_proveedor si no existe
SET @fk_ing_exists = (SELECT COUNT(1) FROM information_schema.table_constraints tc
                      JOIN information_schema.key_column_usage kcu USING(constraint_name,constraint_schema)
                      WHERE tc.constraint_schema = DATABASE()
                        AND tc.table_name = 'ingredientes'
                        AND tc.constraint_type = 'FOREIGN KEY'
                        AND kcu.column_name = 'proveedor_id');
SET @prov_id_col = (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'proveedores' AND column_name = 'id');
SET @s = IF(@fk_ing_exists = 0 AND @prov_id_col = 1 AND @col_prov_id = 1,
    'ALTER TABLE ingredientes ADD CONSTRAINT fk_ingrediente_proveedor FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL',
    'SELECT ''skip add fk_ingrediente_proveedor''');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =========================
-- 5) Mensaje final
-- =========================
SELECT 'Script ejecutado. Revisa arriba los mensajes/resultados de cada sentencia. Si tu cliente SQL bloquea PREPARE/EXECUTE, ejecuta las secciones relevantes por separado en la consola mysql.' AS mensaje;
