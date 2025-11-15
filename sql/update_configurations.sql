-- SQL Update Script for New System Configurations
-- Sistema de Gestión para Comedores Industriales
-- Fecha: 2024

USE comedores_industriales;

-- Insertar nuevas configuraciones del sistema
-- Estas configuraciones se agregan a la tabla configuracion_sistema

-- 1. Nombre del sitio y Logotipo
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) 
VALUES 
('sitio_nombre', 'Sistema de Comedores Industriales', 'string', 'Nombre del sitio', 'general'),
('sitio_logotipo', '', 'string', 'URL o ruta del logotipo del sistema', 'general')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- 2. Configurar correo principal
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) 
VALUES 
('email_remitente', 'noreply@comedores.com', 'string', 'Email principal del sistema', 'correo'),
('email_smtp_host', 'smtp.gmail.com', 'string', 'Servidor SMTP', 'correo'),
('email_smtp_port', '587', 'integer', 'Puerto SMTP', 'correo'),
('email_smtp_usuario', '', 'string', 'Usuario SMTP', 'correo'),
('email_smtp_password', '', 'string', 'Contraseña SMTP', 'correo'),
('email_smtp_seguridad', 'tls', 'string', 'Tipo de seguridad (tls/ssl)', 'correo')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- 3. WhatsApp Chatbot
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) 
VALUES 
('whatsapp_numero', '', 'string', 'Número de WhatsApp del Chatbot (con código de país)', 'whatsapp'),
('whatsapp_token', '', 'string', 'Token de API de WhatsApp Business', 'whatsapp'),
('whatsapp_activo', '0', 'boolean', 'Activar/Desactivar integración WhatsApp', 'whatsapp')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- 4. Teléfonos de contacto y horarios
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) 
VALUES 
('contacto_telefono_principal', '', 'string', 'Teléfono principal de contacto', 'contacto'),
('contacto_telefono_emergencias', '', 'string', 'Teléfono de emergencias', 'contacto'),
('contacto_horario_inicio', '08:00', 'string', 'Horario de atención - Inicio', 'contacto'),
('contacto_horario_fin', '18:00', 'string', 'Horario de atención - Fin', 'contacto'),
('contacto_dias_atencion', 'Lunes a Viernes', 'string', 'Días de atención', 'contacto')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- 5. Estilos de color del sistema
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) 
VALUES 
('tema_color_primario', '#1e40af', 'string', 'Color primario del sistema (hexadecimal)', 'tema'),
('tema_color_secundario', '#64748b', 'string', 'Color secundario del sistema (hexadecimal)', 'tema'),
('tema_color_acento', '#3b82f6', 'string', 'Color de acento (hexadecimal)', 'tema'),
('tema_color_exito', '#10b981', 'string', 'Color para mensajes de éxito (hexadecimal)', 'tema'),
('tema_color_advertencia', '#f59e0b', 'string', 'Color para advertencias (hexadecimal)', 'tema'),
('tema_color_error', '#ef4444', 'string', 'Color para errores (hexadecimal)', 'tema')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- 6. Configuración de PayPal
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) 
VALUES 
('paypal_modo', 'sandbox', 'string', 'Modo de PayPal (sandbox/production)', 'paypal'),
('paypal_client_id', '', 'string', 'Client ID de PayPal', 'paypal'),
('paypal_secret', '', 'string', 'Secret de PayPal', 'paypal'),
('paypal_email_cuenta', '', 'string', 'Email de la cuenta principal de PayPal', 'paypal'),
('paypal_activo', '0', 'boolean', 'Activar/Desactivar pagos por PayPal', 'paypal')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- 7. API para crear QR's masivos
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) 
VALUES 
('qr_api_proveedor', 'qrcode-monkey', 'string', 'Proveedor de API para QR (qrcode-monkey, goqr, custom)', 'apis'),
('qr_api_key', '', 'string', 'API Key para servicio de QR', 'apis'),
('qr_api_url', '', 'string', 'URL del servicio API de QR personalizado', 'apis'),
('qr_tamano_default', '300', 'integer', 'Tamaño por defecto de QR en píxeles', 'apis')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- 8. API para dispositivos Shelly Relay
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) 
VALUES 
('shelly_api_url', '', 'string', 'URL base de la API de Shelly', 'apis'),
('shelly_api_token', '', 'string', 'Token de autenticación para Shelly', 'apis'),
('shelly_dispositivos', '[]', 'json', 'Lista de dispositivos Shelly configurados (JSON)', 'apis'),
('shelly_activo', '0', 'boolean', 'Activar/Desactivar integración con Shelly', 'apis')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- 9. API para dispositivos HikVision
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) 
VALUES 
('hikvision_api_url', '', 'string', 'URL base de la API de HikVision', 'apis'),
('hikvision_usuario', '', 'string', 'Usuario para autenticación HikVision', 'apis'),
('hikvision_password', '', 'string', 'Contraseña para autenticación HikVision', 'apis'),
('hikvision_dispositivos', '[]', 'json', 'Lista de dispositivos HikVision configurados (JSON)', 'apis'),
('hikvision_activo', '0', 'boolean', 'Activar/Desactivar integración con HikVision', 'apis')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- 10. Configuraciones globales recomendadas
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) 
VALUES 
('sistema_mantenimiento', '0', 'boolean', 'Activar modo mantenimiento', 'sistema'),
('sistema_registro_logs', '1', 'boolean', 'Registrar logs del sistema', 'sistema'),
('sistema_tiempo_sesion', '3600', 'integer', 'Tiempo de sesión en segundos (1 hora = 3600)', 'sistema'),
('sistema_max_intentos_login', '5', 'integer', 'Máximo de intentos de login fallidos', 'sistema'),
('sistema_zona_horaria', 'America/Mexico_City', 'string', 'Zona horaria del sistema', 'sistema'),
('sistema_idioma', 'es', 'string', 'Idioma del sistema (es/en)', 'sistema'),
('backup_automatico', '1', 'boolean', 'Realizar backups automáticos', 'sistema'),
('backup_frecuencia', 'diario', 'string', 'Frecuencia de backups (diario/semanal/mensual)', 'sistema'),
('notificaciones_email', '1', 'boolean', 'Enviar notificaciones por email', 'sistema'),
('notificaciones_push', '0', 'boolean', 'Enviar notificaciones push', 'sistema')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Mensaje de confirmación
SELECT 'Configuraciones actualizadas correctamente' AS mensaje;

-- Verificar las nuevas configuraciones
SELECT categoria, COUNT(*) as total_configs
FROM configuracion_sistema
GROUP BY categoria
ORDER BY categoria;
