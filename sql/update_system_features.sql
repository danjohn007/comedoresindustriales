-- ============================================
-- SQL UPDATE SCRIPT
-- Sistema de Gestión para Comedores Industriales
-- Feature Updates and Improvements
-- ============================================

-- Add imagen_perfil column to usuarios table for profile image upload functionality
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS imagen_perfil VARCHAR(255) NULL 
COMMENT 'Ruta de la imagen de perfil del usuario' 
AFTER ultimo_acceso;

-- Add index for better query performance
CREATE INDEX IF NOT EXISTS idx_imagen_perfil ON usuarios(imagen_perfil);

-- ============================================
-- Notes and Considerations:
-- ============================================
-- 1. The imagen_perfil column stores the relative path to the profile image
--    Example: /uploads/profiles/profile_1_1234567890.jpg
--
-- 2. Images are stored in: public/uploads/profiles/
--    Make sure this directory exists and has write permissions
--
-- 3. Supported image formats: JPG, PNG, GIF
--    Maximum file size: 5MB
--
-- 4. All other improvements (recipe unit preload, real-time cost calculation,
--    pagination, new financial reports) are implemented in the application
--    layer and don't require database schema changes
--
-- ============================================
-- Summary of Non-Database Changes:
-- ============================================
--
-- FIXED ERRORS:
-- - Fixed SQL syntax errors in ProductionController.php (LIMIT/OFFSET)
-- - Fixed SQL syntax errors in AttendanceController.php (LIMIT/OFFSET)
-- - Fixed number_format() deprecation warnings in monthly_report.php
--
-- NEW FEATURES:
-- - Recipe creation: Auto-preload ingredient unit of measure (read-only)
-- - Recipe creation: Real-time cost calculation display
-- - Profile module: Profile image upload and management
--
-- PAGINATION ADDED TO:
-- - Movimientos Recientes (Recent Movements)
-- - Transacciones Financieras (Financial Transactions)
-- - Reporte Mensual (Monthly Report)
-- - Estado de Cuenta (Account Statement)
-- - Análisis por Categoría (Category Analysis)
--
-- NEW FINANCIAL REPORTS:
-- - Ejecución Presupuestal: Budget execution comparison report
-- - Alertas Presupuestales: Budget alerts for exceeded or near-exceeding budgets
-- - Exportar Datos: Excel export functionality for transactions and budgets
--
-- ============================================
-- Deployment Instructions:
-- ============================================
-- 1. Backup your database before running this script
-- 2. Run this SQL script on your database
-- 3. Create the uploads directory structure:
--    mkdir -p public/uploads/profiles
--    chmod 755 public/uploads/profiles
-- 4. Deploy the updated PHP files
-- 5. Test all functionality
--
-- ============================================
