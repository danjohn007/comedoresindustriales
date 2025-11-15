# Instrucciones de Instalación - Nuevas Funcionalidades

## Requisitos Previos

- Acceso a la base de datos MySQL/MariaDB
- Backup actual de la base de datos
- Usuario con permisos de administración en el servidor

---

## Paso 1: Backup de la Base de Datos

**IMPORTANTE:** Antes de realizar cualquier cambio, hacer un respaldo completo.

```bash
# Desde línea de comandos
mysqldump -u usuario -p comedores_industriales > backup_$(date +%Y%m%d).sql

# O desde phpMyAdmin: Exportar > SQL > Ejecutar
```

---

## Paso 2: Aplicar Actualizaciones de Base de Datos

### Método 1: Línea de Comandos (Recomendado)

```bash
# Navegar al directorio del proyecto
cd /ruta/al/proyecto/comedoresindustriales

# Ejecutar el script de actualización
mysql -u usuario -p comedores_industriales < sql/update_system_improvements.sql

# Cuando pida contraseña, ingresarla
```

### Método 2: phpMyAdmin

1. Acceder a phpMyAdmin
2. Seleccionar la base de datos `comedores_industriales`
3. Clic en la pestaña "SQL"
4. Abrir el archivo `sql/update_system_improvements.sql` en un editor de texto
5. Copiar todo el contenido
6. Pegarlo en el área de texto de phpMyAdmin
7. Hacer clic en "Continuar" o "Ejecutar"

### Método 3: Cliente MySQL Workbench

1. Conectarse al servidor
2. Seleccionar la base de datos `comedores_industriales`
3. File > Run SQL Script
4. Seleccionar `sql/update_system_improvements.sql`
5. Ejecutar

---

## Paso 3: Verificar la Actualización

### 3.1 Verificar Tablas Nuevas

Ejecutar en MySQL:

```sql
USE comedores_industriales;

-- Verificar tabla password_resets
SHOW TABLES LIKE 'password_resets';
DESC password_resets;

-- Verificar tabla transacciones_financieras
SHOW TABLES LIKE 'transacciones_financieras';
DESC transacciones_financieras;

-- Verificar tabla presupuestos
SHOW TABLES LIKE 'presupuestos';
DESC presupuestos;

-- Verificar tabla configuracion_correo
SHOW TABLES LIKE 'configuracion_correo';
DESC configuracion_correo;
```

### 3.2 Verificar Actualización de Usuarios

```sql
-- Verificar que el campo rol incluye 'cliente'
SHOW COLUMNS FROM usuarios LIKE 'rol';

-- Debe mostrar: enum('admin','coordinador','chef','operativo','cliente')
```

### 3.3 Verificar Configuraciones Insertadas

```sql
-- Ver configuración de correo
SELECT * FROM configuracion_correo;

-- Ver nuevas configuraciones del sistema
SELECT * FROM configuracion_sistema 
WHERE clave IN ('modulo_financiero_activo', 'moneda_sistema', 'simbolo_moneda', 'iva_porcentaje');
```

---

## Paso 4: Configurar PHP para Envío de Correos

### 4.1 Verificar Configuración PHP

Crear archivo `test_mail.php` en la carpeta `public/`:

```php
<?php
$to = "tu_email@example.com";
$subject = "Test Email";
$message = "Este es un correo de prueba del sistema de comedores.";
$headers = "From: comedores@majorbot.digital\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Email enviado correctamente";
} else {
    echo "Error al enviar email";
}
?>
```

Acceder a: `http://tu-dominio.com/test_mail.php`

### 4.2 Configurar php.ini (si es necesario)

Si el correo no se envía, verificar/agregar en `php.ini`:

```ini
[mail function]
SMTP = majorbot.digital
smtp_port = 465
sendmail_from = comedores@majorbot.digital
```

Reiniciar el servidor web después de cambios:

```bash
# Apache
sudo systemctl restart apache2

# Nginx + PHP-FPM
sudo systemctl restart php-fpm
```

---

## Paso 5: Probar el Sistema

### 5.1 Verificar Login

1. Acceder a la página de login
2. Verificar que NO aparece el cuadro de "Credenciales de Prueba"
3. Verificar que aparece el enlace "¿Olvidó su contraseña?"

### 5.2 Probar Recuperación de Contraseña

1. Hacer clic en "¿Olvidó su contraseña?"
2. Ingresar un correo válido de usuario
3. Verificar que aparece mensaje de éxito
4. Revisar el correo electrónico
5. Hacer clic en el enlace recibido
6. Ingresar nueva contraseña
7. Confirmar que puede iniciar sesión con la nueva contraseña

### 5.3 Crear Usuario Cliente

1. Iniciar sesión como admin
2. Ir a "Gestión de Usuarios"
3. Hacer clic en "Nuevo Usuario"
4. Llenar el formulario:
   - Usuario: `cliente_test`
   - Email: `cliente@test.com`
   - Contraseña: `cliente123`
   - Nombre Completo: `Usuario Cliente Test`
   - Rol: `Cliente`
5. Hacer clic en "Crear"
6. Cerrar sesión
7. Iniciar sesión como `cliente_test`
8. Verificar que NO aparecen botones de crear/editar/eliminar
9. Verificar que NO aparece el menú "Financiero"

### 5.4 Probar Gestión de Comedores

1. Iniciar sesión como admin
2. Ir a "Gestión de Comedores"
3. Hacer clic en un comedor existente en el icono de "Ver" (ojo azul)
4. Verificar que se abre modal con información
5. Hacer clic en el icono de "Editar" (lápiz verde)
6. Modificar algún dato
7. Guardar cambios
8. Verificar que los cambios se aplicaron

### 5.5 Probar Módulo Financiero

1. Iniciar sesión como admin o coordinador
2. Hacer clic en "Financiero" en el menú lateral
3. Verificar que aparece el dashboard con tarjetas de resumen
4. Ir a "Transacciones"
5. Hacer clic en "Nueva Transacción"
6. Crear una transacción de prueba:
   - Comedor: Seleccionar uno existente
   - Tipo: Egreso
   - Concepto: "Compra de ingredientes - Prueba"
   - Categoría: "Ingredientes"
   - Monto: 1000.00
   - Fecha: Fecha actual
7. Guardar
8. Verificar que aparece en la lista
9. Ir a "Presupuestos"
10. Crear un presupuesto:
    - Comedor: El mismo de la transacción
    - Año/Mes: Actual
    - Presupuesto Asignado: 50000.00
11. Guardar
12. Volver al dashboard
13. Verificar que aparece el presupuesto con el porcentaje calculado

---

## Paso 6: Ajustes de Seguridad (Recomendado)

### 6.1 Eliminar Archivo de Prueba

```bash
rm public/test_mail.php
```

### 6.2 Configurar Permisos de Archivos

```bash
# Permisos de archivos
find . -type f -exec chmod 644 {} \;

# Permisos de directorios
find . -type d -exec chmod 755 {} \;

# Archivos de configuración (solo lectura)
chmod 600 config/config.php
chmod 600 config/Database.php
```

### 6.3 Proteger Directorio SQL

Agregar en `.htaccess` en la raíz:

```apache
<DirectoryMatch "^/.*/sql/">
    Order Allow,Deny
    Deny from all
</DirectoryMatch>
```

O crear `.htaccess` dentro de `/sql/`:

```apache
Order Allow,Deny
Deny from all
```

---

## Paso 7: Configuración de Producción

### 7.1 Desactivar Errores de PHP

En `config/config.php`, cambiar:

```php
// Desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// A Producción
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/ruta/logs/php-error.log');
```

### 7.2 Configurar HTTPS (Recomendado)

Asegurar que el sitio funciona sobre HTTPS. En `.htaccess`:

```apache
# Forzar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 7.3 Configurar Backup Automático

Crear script de backup automático:

```bash
#!/bin/bash
# backup_db.sh

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/ruta/backups"
DB_NAME="comedores_industriales"
DB_USER="usuario"
DB_PASS="contraseña"

mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/backup_$TIMESTAMP.sql

# Comprimir
gzip $BACKUP_DIR/backup_$TIMESTAMP.sql

# Eliminar backups antiguos (más de 30 días)
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +30 -delete
```

Agregar a crontab (diario a las 2 AM):

```bash
crontab -e

# Agregar línea:
0 2 * * * /ruta/backup_db.sh
```

---

## Problemas Comunes y Soluciones

### Problema 1: Error al Ejecutar SQL

**Error:** `Table 'xxx' already exists`

**Solución:** El script ya fue ejecutado. Si es necesario, eliminar las tablas manualmente y volver a ejecutar:

```sql
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS transacciones_financieras;
DROP TABLE IF EXISTS presupuestos;
DROP TABLE IF EXISTS configuracion_correo;
-- Luego ejecutar el script nuevamente
```

### Problema 2: No se Envían Correos

**Causas comunes:**
1. Función `mail()` no configurada
2. Puerto 465 bloqueado por firewall
3. Credenciales incorrectas
4. Servidor requiere autenticación SMTP

**Solución:**
- Verificar configuración de PHP
- Verificar firewall: `telnet majorbot.digital 465`
- Consultar logs de PHP: `tail -f /var/log/php-error.log`
- Contactar al hosting para habilitar envío de correos

### Problema 3: Error de Permisos

**Error:** `Access denied for user`

**Solución:** Verificar que el usuario de MySQL tiene permisos:

```sql
GRANT ALL PRIVILEGES ON comedores_industriales.* TO 'usuario'@'localhost';
FLUSH PRIVILEGES;
```

### Problema 4: Módulo Financiero No Aparece

**Causa:** Usuario no tiene rol admin o coordinador

**Solución:** Actualizar rol del usuario:

```sql
UPDATE usuarios SET rol = 'admin' WHERE username = 'tu_usuario';
```

### Problema 5: Error 404 en Rutas Nuevas

**Causa:** .htaccess no está funcionando o mod_rewrite no está habilitado

**Solución:**

```bash
# Habilitar mod_rewrite en Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# Verificar .htaccess en /public/
# Debe contener las reglas de reescritura
```

---

## Verificación Final

### Checklist de Instalación

- [ ] Backup de base de datos realizado
- [ ] Script SQL ejecutado sin errores
- [ ] Todas las tablas nuevas creadas
- [ ] Campo `rol` actualizado en tabla `usuarios`
- [ ] Configuración de correo insertada
- [ ] Envío de correos funcionando
- [ ] Login sin credenciales de prueba
- [ ] Recuperación de contraseña funcionando
- [ ] CRUD de usuarios funcionando
- [ ] CRUD de comedores funcionando
- [ ] Módulo financiero accesible
- [ ] Transacciones pueden crearse
- [ ] Presupuestos pueden crearse
- [ ] Usuario con rol cliente tiene acceso limitado
- [ ] Permisos de archivos configurados
- [ ] Errores PHP desactivados en producción
- [ ] Backup automático configurado

---

## Contacto y Soporte

Si después de seguir estos pasos hay problemas:

1. Revisar logs de errores de PHP y Apache
2. Verificar permisos de archivos y directorios
3. Confirmar que la base de datos está actualizada
4. Verificar configuración de correo

Para asistencia técnica, proporcionar:
- Mensajes de error específicos
- Logs relevantes
- Versión de PHP y MySQL
- Configuración del servidor

---

**Documento de Instalación v1.0**  
**Última actualización:** 15 de Noviembre, 2025
