# Nuevas Funcionalidades Implementadas

## Resumen de Cambios

Este documento describe las nuevas funcionalidades implementadas en el sistema de gestión de comedores industriales según los requerimientos especificados.

---

## 1. Mejoras en el Sistema de Login

### 1.1 Recuperación de Contraseña

**Nueva funcionalidad:** Los usuarios ahora pueden recuperar su contraseña a través de correo electrónico.

**Características:**
- Formulario de recuperación accesible desde la página de login
- Generación de tokens seguros con expiración de 1 hora
- Envío de correo electrónico con enlace de recuperación
- Validación de tokens y cambio seguro de contraseña

**Configuración de correo:**
- Host SMTP: `majorbot.digital`
- Puerto SMTP: `465`
- Usuario: `comedores@majorbot.digital`
- Contraseña: `Danjohn007`
- Encriptación: `SSL`

**Rutas nuevas:**
- `GET /forgot-password` - Formulario de solicitud de recuperación
- `POST /forgot-password` - Envío de enlace de recuperación
- `GET /reset-password` - Formulario de nueva contraseña
- `POST /reset-password` - Actualización de contraseña

**Archivos nuevos:**
- `app/views/auth/forgot-password.php`
- `app/views/auth/reset-password.php`

### 1.2 Eliminación de Credenciales de Prueba

**Cambio:** Se eliminó el cuadro de "Credenciales de Prueba" de la página de login que mostraba:
- Usuario: admin
- Contraseña: admin123

**Nota de seguridad:** Aunque se ocultó del frontend, el usuario admin aún existe en la base de datos para acceso administrativo.

---

## 2. Gestión de Usuarios

### 2.1 CRUD Completo de Usuarios

**Nueva funcionalidad:** Panel administrativo completo para gestionar usuarios del sistema.

**Características:**
- **Crear Usuario:** Modal con formulario para crear nuevos usuarios
  - Campos: username, email, contraseña, nombre completo, rol
  - Validación de duplicados
  - Encriptación segura de contraseñas
  
- **Editar Usuario:** Modificación de datos de usuarios existentes
  - Todos los campos excepto contraseña
  - Cambio de rol y estado (activo/inactivo)
  
- **Ver Usuario:** Modal con información completa del usuario
  - Datos básicos
  - Último acceso
  - Estado actual
  
- **Eliminar Usuario:** Eliminación con confirmación
  - Protección contra auto-eliminación
  - Validación de permisos

**Rutas nuevas:**
- `POST /settings/users/create` - Crear usuario
- `GET /settings/users/get/:id` - Obtener usuario
- `POST /settings/users/update` - Actualizar usuario
- `POST /settings/users/delete` - Eliminar usuario

**Permisos:** Solo accesible para usuarios con rol `admin`

### 2.2 Nuevo Rol: Cliente

**Nueva funcionalidad:** Se agregó el rol "cliente" con permisos de solo lectura.

**Características:**
- Los usuarios con rol `cliente` pueden:
  - Ver el dashboard
  - Consultar asistencia, situaciones, producción, recetas, reportes
  - No pueden crear, editar o eliminar registros
  
- Restricciones implementadas:
  - Botones de crear/editar/eliminar ocultos para clientes
  - Método `isReadOnly()` en el controlador base
  - Método `denyReadOnly()` para bloquear acciones

**Colores en UI:**
- Admin: Púrpura
- Coordinador: Azul
- Chef: Verde
- Operativo: Gris
- Cliente: Amarillo

---

## 3. Gestión de Comedores

### 3.1 CRUD Completo de Comedores

**Nueva funcionalidad:** Gestión completa de comedores desde el panel administrativo.

**Características:**
- **Crear Comedor:** Modal con formulario
  - Nombre, ubicación, ciudad, estado
  - Capacidad total
  - Turnos activos (matutino, vespertino, nocturno)
  
- **Editar Comedor:** Modificación de todos los datos
  - Actualización de capacidad
  - Cambio de turnos activos
  - Estado activo/inactivo
  
- **Ver Comedor:** Visualización completa de información
  - Datos de ubicación
  - Capacidad y turnos
  - Fecha de creación
  
- **Eliminar Comedor:** Eliminación con validaciones
  - Verifica si tiene órdenes de producción asociadas
  - Confirmación antes de eliminar

**Rutas nuevas:**
- `POST /settings/comedores/create` - Crear comedor
- `GET /settings/comedores/get/:id` - Obtener comedor
- `POST /settings/comedores/update` - Actualizar comedor
- `POST /settings/comedores/delete` - Eliminar comedor

**Permisos:** Solo accesible para usuarios con rol `admin`

---

## 4. Módulo Financiero

### 4.1 Dashboard Financiero

**Nueva funcionalidad:** Módulo completo para gestión financiera.

**Características:**
- Resumen del mes actual:
  - Total de ingresos
  - Total de egresos
  - Balance neto
  
- Estado de presupuestos:
  - Porcentaje ejecutado por comedor
  - Indicadores visuales de alertas
  - Presupuesto disponible
  
- Transacciones recientes:
  - Últimas 10 transacciones
  - Vista rápida por comedor

**Ruta:** `GET /financial`

**Permisos:** Accesible para roles `admin` y `coordinador`

### 4.2 Gestión de Transacciones

**Nueva funcionalidad:** Registro y seguimiento de ingresos y egresos.

**Características:**
- Tipos de transacciones:
  - Ingreso
  - Egreso
  - Ajuste
  
- Información registrada:
  - Comedor asociado
  - Concepto y categoría
  - Monto
  - Fecha de transacción
  - Descripción opcional
  - Usuario que registró
  
- Actualización automática de presupuestos

**Rutas:**
- `GET /financial/transactions` - Lista de transacciones
- `POST /financial/transactions/create` - Crear transacción

### 4.3 Gestión de Presupuestos

**Nueva funcionalidad:** Definición y seguimiento de presupuestos mensuales.

**Características:**
- Presupuestos por comedor y mes
- Cálculo automático de:
  - Monto gastado
  - Porcentaje ejecutado
  - Saldo disponible
  
- Estados del presupuesto:
  - Activo (< 95%)
  - Cerrado (95-100%)
  - Excedido (> 100%)
  
- Indicadores visuales:
  - Barras de progreso
  - Colores según estado (verde, amarillo, rojo)

**Rutas:**
- `GET /financial/budgets` - Lista de presupuestos
- `POST /financial/budgets/create` - Crear presupuesto

### 4.4 Reportes Financieros

**Nueva funcionalidad:** Sección preparada para generación de reportes.

**Tipos de reportes disponibles:**
- Reporte mensual
- Estado de cuenta
- Análisis por categoría
- Ejecución presupuestal
- Alertas presupuestales
- Exportación de datos

**Ruta:** `GET /financial/reports`

**Nota:** Los reportes están preparados para implementación futura con generación de PDFs y Excel.

### 4.5 Menú de Navegación

**Cambio:** Se agregó el enlace "Financiero" en el menú lateral.

**Acceso:** Solo visible para usuarios con rol `admin` o `coordinador`

**Icono:** Símbolo de dólar ($)

---

## 5. Actualizaciones de Base de Datos

### 5.1 Script SQL de Actualización

**Archivo:** `sql/update_system_improvements.sql`

**Cambios en la base de datos:**

1. **Tabla `usuarios`:**
   - Modificación del campo `rol` para incluir 'cliente'
   - Nuevo ENUM: `('admin', 'coordinador', 'chef', 'operativo', 'cliente')`

2. **Nueva tabla `password_resets`:**
   ```sql
   - id (INT, PRIMARY KEY)
   - email (VARCHAR 100)
   - token (VARCHAR 100, UNIQUE)
   - expira_en (DATETIME)
   - usado (TINYINT)
   - fecha_creacion (TIMESTAMP)
   ```

3. **Nueva tabla `transacciones_financieras`:**
   ```sql
   - id (INT, PRIMARY KEY)
   - comedor_id (INT, FK)
   - tipo (ENUM: ingreso, egreso, ajuste)
   - concepto (VARCHAR 255)
   - monto (DECIMAL 12,2)
   - categoria (VARCHAR 100)
   - fecha_transaccion (DATE)
   - orden_produccion_id (INT, FK, NULL)
   - descripcion (TEXT)
   - comprobante_path (VARCHAR 255)
   - creado_por (INT, FK)
   - fecha_creacion (TIMESTAMP)
   ```

4. **Nueva tabla `presupuestos`:**
   ```sql
   - id (INT, PRIMARY KEY)
   - comedor_id (INT, FK)
   - anio (INT)
   - mes (INT)
   - presupuesto_asignado (DECIMAL 12,2)
   - presupuesto_gastado (DECIMAL 12,2)
   - porcentaje_ejecutado (DECIMAL 5,2)
   - estado (ENUM: activo, cerrado, excedido)
   - notas (TEXT)
   - creado_por (INT, FK)
   - fecha_creacion (TIMESTAMP)
   - fecha_actualizacion (TIMESTAMP)
   ```

5. **Nueva tabla `configuracion_correo`:**
   ```sql
   - id (INT, PRIMARY KEY)
   - smtp_host (VARCHAR 255)
   - smtp_port (INT)
   - smtp_user (VARCHAR 255)
   - smtp_password (VARCHAR 255)
   - smtp_encryption (VARCHAR 10)
   - from_email (VARCHAR 255)
   - from_name (VARCHAR 255)
   - activo (TINYINT)
   - fecha_modificacion (TIMESTAMP)
   ```

6. **Configuración inicial insertada:**
   - Datos del servidor de correo
   - Configuraciones del módulo financiero
   - Parámetros de moneda y IVA

### 5.2 Cómo Aplicar las Actualizaciones

**Opción 1 - Línea de comandos:**
```bash
mysql -u usuario -p nombre_base_datos < sql/update_system_improvements.sql
```

**Opción 2 - phpMyAdmin:**
1. Acceder a phpMyAdmin
2. Seleccionar la base de datos `comedores_industriales`
3. Ir a la pestaña "SQL"
4. Copiar y pegar el contenido del archivo `sql/update_system_improvements.sql`
5. Ejecutar

**Opción 3 - Cliente MySQL:**
1. Conectarse al servidor MySQL
2. Ejecutar:
```sql
USE comedores_industriales;
SOURCE /ruta/completa/sql/update_system_improvements.sql;
```

**IMPORTANTE:** 
- Hacer un respaldo de la base de datos antes de ejecutar el script
- El script es idempotente (puede ejecutarse múltiples veces sin causar errores)
- Verificar que todos los cambios se aplicaron correctamente

---

## 6. Archivos Modificados

### 6.1 Controladores

**Modificados:**
- `app/Controller.php` - Métodos para rol cliente (isReadOnly, denyReadOnly)
- `app/controllers/AuthController.php` - Métodos de recuperación de contraseña
- `app/controllers/SettingsController.php` - CRUD de usuarios y comedores

**Nuevos:**
- `app/controllers/FinancialController.php` - Gestión del módulo financiero

### 6.2 Vistas

**Modificadas:**
- `app/views/auth/login.php` - Eliminado cuadro de credenciales, agregado enlace de recuperación
- `app/views/layouts/nav.php` - Agregado enlace a módulo financiero
- `app/views/settings/users.php` - CRUD completo con modales
- `app/views/settings/comedores.php` - CRUD completo con modales

**Nuevas:**
- `app/views/auth/forgot-password.php`
- `app/views/auth/reset-password.php`
- `app/views/financial/index.php`
- `app/views/financial/transactions.php`
- `app/views/financial/budgets.php`
- `app/views/financial/reports.php`

### 6.3 Rutas

**Modificado:**
- `public/index.php` - Agregadas rutas de recuperación de contraseña, CRUD usuarios/comedores, y módulo financiero

### 6.4 SQL

**Nuevo:**
- `sql/update_system_improvements.sql` - Script de actualización de base de datos

---

## 7. Configuración Adicional Requerida

### 7.1 Configuración de Correo Electrónico

Para que la recuperación de contraseña funcione correctamente, el servidor debe estar configurado para enviar correos.

**Opción 1 - PHP mail() (Recomendado para producción):**
El sistema usa la función `mail()` de PHP. Configurar en `php.ini`:
```ini
[mail function]
SMTP = majorbot.digital
smtp_port = 465
sendmail_from = comedores@majorbot.digital
```

**Opción 2 - Biblioteca PHPMailer (Alternativa):**
Si se requiere mayor control, se puede integrar PHPMailer para SMTP directo.

**Verificación:**
Probar el envío de correos con:
```php
mail('destino@example.com', 'Test', 'Mensaje de prueba', 
     'From: comedores@majorbot.digital');
```

### 7.2 Permisos de Archivos

Verificar que el servidor web tenga permisos de escritura si se implementa carga de comprobantes:
```bash
chmod 755 /ruta/uploads/comprobantes
chown www-data:www-data /ruta/uploads/comprobantes
```

---

## 8. Testing y Validación

### 8.1 Checklist de Pruebas

**Login y Recuperación:**
- [ ] Login con credenciales existentes
- [ ] Solicitar recuperación de contraseña
- [ ] Verificar recepción de correo
- [ ] Restablecer contraseña con token válido
- [ ] Intentar usar token expirado o usado
- [ ] Iniciar sesión con nueva contraseña

**Gestión de Usuarios:**
- [ ] Crear usuario como admin
- [ ] Editar información de usuario
- [ ] Ver detalles de usuario
- [ ] Cambiar estado activo/inactivo
- [ ] Eliminar usuario
- [ ] Verificar permisos por rol

**Gestión de Comedores:**
- [ ] Crear nuevo comedor
- [ ] Editar información de comedor
- [ ] Ver detalles de comedor
- [ ] Eliminar comedor sin órdenes
- [ ] Intentar eliminar comedor con órdenes (debe fallar)

**Módulo Financiero:**
- [ ] Acceder al dashboard financiero
- [ ] Crear transacción de ingreso
- [ ] Crear transacción de egreso
- [ ] Verificar actualización de balance
- [ ] Crear presupuesto mensual
- [ ] Verificar cálculo automático de porcentaje ejecutado
- [ ] Crear transacción que actualice presupuesto
- [ ] Ver cambio de estado del presupuesto

**Rol Cliente:**
- [ ] Crear usuario con rol cliente
- [ ] Iniciar sesión como cliente
- [ ] Verificar acceso a vistas de solo lectura
- [ ] Verificar que botones de crear/editar/eliminar no aparecen
- [ ] Verificar que módulo financiero no es visible

### 8.2 Casos de Prueba Críticos

**Seguridad:**
1. Intentar acceder a rutas administrativas sin permisos
2. Intentar crear usuario con rol admin siendo operativo
3. Verificar que contraseñas se almacenan hasheadas
4. Probar SQL injection en formularios
5. Verificar tokens CSRF en formularios POST

**Integridad de Datos:**
1. Crear transacción y verificar actualización de presupuesto
2. Eliminar usuario y verificar que no se pierden relaciones críticas
3. Crear presupuesto duplicado (debe fallar por UNIQUE constraint)
4. Verificar cálculos de porcentajes y montos

---

## 9. Notas Importantes

### 9.1 Seguridad

- Las contraseñas se hashean con `password_hash()` usando `PASSWORD_DEFAULT`
- Los tokens de recuperación son criptográficamente seguros (`random_bytes()`)
- Los tokens expiran en 1 hora
- Las sesiones usan `httponly` y `samesite=Lax`

### 9.2 Compatibilidad

- PHP 7.4 o superior requerido
- MySQL 5.7 o superior / MariaDB 10.2 o superior
- Todos los navegadores modernos soportados

### 9.3 Mantenimiento

**Limpieza periódica recomendada:**
- Tokens de recuperación expirados (tabla `password_resets`)
- Logs antiguos (tabla `logs_sistema`)
- Sesiones antiguas

**Script de limpieza sugerido (ejecutar mensualmente):**
```sql
-- Eliminar tokens expirados
DELETE FROM password_resets WHERE expira_en < NOW() - INTERVAL 7 DAY;

-- Eliminar logs antiguos (más de 6 meses)
DELETE FROM logs_sistema WHERE fecha_hora < NOW() - INTERVAL 6 MONTH;
```

---

## 10. Soporte y Contacto

Para reportar problemas o solicitar mejoras:
- Crear issue en el repositorio de GitHub
- Contactar al administrador del sistema

**Versión del documento:** 1.0  
**Fecha:** 15 de Noviembre, 2025  
**Autor:** Sistema de Gestión de Comedores Industriales
