-- ============================================
-- SQL UPDATE SCRIPT (safe import)
-- Sistema de Gestión para Comedores Industriales
-- Feature Updates and Improvements
-- ============================================
-- Este script intenta agregar la columna imagen_perfil y su índice.
-- Para evitar que la importación falle si ya existen, se usa un
-- procedimiento temporal que captura y continúa ante errores SQL.
-- IMPORTANTE: hacer backup antes de ejecutar.
-- Requiere: privilegio CREATE ROUTINE (para crear el procedimiento).
-- Si no tienes CREATE ROUTINE, utiliza las instrucciones manuales al final.
-- ============================================

DELIMITER $$

DROP PROCEDURE IF EXISTS safe_add_profile_column$$

CREATE PROCEDURE safe_add_profile_column()
BEGIN
  -- Se captura cualquier error SQL y se continúa; esto evita que la importación falle.
  -- Nota: esto también ocultará errores inesperados; revisar los logs si algo no funciona.
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;

  -- Intentar añadir la columna (si ya existe se ignora por el handler)
  ALTER TABLE usuarios
    ADD COLUMN imagen_perfil VARCHAR(255) NULL
    COMMENT 'Ruta de la imagen de perfil del usuario'
    AFTER ultimo_acceso;

  -- Intentar crear el índice (si ya existe se ignora por el handler)
  CREATE INDEX idx_imagen_perfil ON usuarios (imagen_perfil);
END$$

DELIMITER ;

-- Ejecutar el procedimiento seguro
CALL safe_add_profile_column();

-- Eliminar el procedimiento temporal
DROP PROCEDURE IF EXISTS safe_add_profile_column;

-- ============================================
-- Notas y consideraciones:
-- 1) Hacer copia de seguridad antes de ejecutar:
--    mysqldump -u usuario -p nombre_base_de_datos > backup.sql
--
-- 2) Ejecución:
--    - Desde consola: mysql -u usuario -p nombre_base_de_datos < sql/update_system_features.sql
--    - O importa vía phpMyAdmin / MySQL Workbench (ambos aceptan DELIMITER en la mayoría de configuraciones).
--
-- 3) Si tu usuario NO TIENE CREATE ROUTINE y no puedes pedirlo al DBA,
--    ejecuta manualmente estos pasos (sin el archivo):
--
--    a) Comprobar si existe la columna:
--       SHOW COLUMNS FROM usuarios LIKE 'imagen_perfil';
--    b) Si la respuesta está vacía, agrega la columna:
--       ALTER TABLE usuarios
--         ADD COLUMN imagen_perfil VARCHAR(255) NULL
--         COMMENT 'Ruta de la imagen de perfil del usuario'
--         AFTER ultimo_acceso;
--    c) Comprobar si existe el índice:
--       SHOW INDEX FROM usuarios WHERE Key_name = 'idx_imagen_perfil';
--    d) Si no existe, crear el índice:
--       CREATE INDEX idx_imagen_perfil ON usuarios (imagen_perfil);
--
-- 4) Para MySQL 8+ podrías usar directamente:
--    ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS imagen_perfil VARCHAR(255) NULL COMMENT '...' AFTER ultimo_acceso;
--    CREATE INDEX IF NOT EXISTS idx_imagen_perfil ON usuarios (imagen_perfil);
--    (pero IF NOT EXISTS no está disponible en versiones más antiguas)
--
-- 5) Directorio de uploads y permisos (parte de la funcionalidad, no DB):
--    mkdir -p public/uploads/profiles
--    chmod 755 public/uploads/profiles
--
-- ============================================
-- Fin del script
-- ============================================
