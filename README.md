# Sistema de Gestión para Comedores Industriales

Sistema web completo para la gestión integral de comedores industriales, desarrollado con PHP puro, MySQL y Tailwind CSS.

## 🎯 Características Principales

### Módulos Implementados

1. **Gestión de Asistencia (REQ-ASIST-001, REQ-DATA-001)**
   - Registro diario de comensales por turno
   - Cálculo automático de proyecciones basado en histórico
   - Integración con formato OPAD-034 (Control Mensual de Comensales)
   - Análisis de tendencias y porcentajes de asistencia

2. **Situaciones Atípicas (REQ-AJUSTES-001)**
   - Registro de eventos que afectan la asistencia
   - Contrataciones, despidos, incapacidades, días festivos
   - Impacto automático en proyecciones futuras
   - Seguimiento de situaciones activas y pasadas

3. **Órdenes de Producción (REQ-PRODUCCION-001)**
   - Generación automática basada en proyecciones
   - Cálculo de ingredientes según gramajes (OPAD-025)
   - Gestión de recetas y líneas de servicio
   - Estimación de costos por orden
   - Formato de impresión OPAD-007

4. **Dashboard Interactivo (REQ-DASHBOARD-001)**
   - Visualización en tiempo real de estadísticas
   - Gráficos de tendencias con Chart.js
   - Alertas de desviaciones >10%
   - Órdenes próximas y situaciones activas

5. **Reportes (REQ-REPORTES-001)**
   - Efectividad de proyecciones
   - Análisis de desviaciones
   - Reportes de producción y costos
   - Comparativos históricos

6. **Configuración del Sistema (REQ-CONFIG-001)**
   - Parámetros de proyección ajustables
   - Gestión de usuarios y permisos
   - Administración de comedores
   - Catálogo de ingredientes

7. **API RESTful (REQ-API-001)**
   - Sincronización con sistemas de control de acceso
   - Exportación de órdenes de producción
   - Webhooks para actualización de nómina
   - Autenticación por tokens

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7.0+ (sin frameworks)
- **Base de Datos**: MySQL 5.7
- **Frontend**: HTML5, CSS3, JavaScript
- **Estilos**: Tailwind CSS
- **Gráficas**: Chart.js
- **Arquitectura**: MVC personalizado
- **Seguridad**: 
  - Sesiones PHP
  - `password_hash()` para contraseñas
  - Prepared statements (PDO)
  - CSRF tokens

## 📋 Requisitos del Sistema

- PHP >= 7.0
- MySQL >= 5.7
- Servidor Apache con mod_rewrite habilitado
- Extensión PDO MySQL para PHP

## 🚀 Instalación

### 1. Clonar o Descargar el Repositorio

```bash
git clone https://github.com/danjohn007/comedoresindustriales.git
cd comedoresindustriales
```

### 2. Configurar la Base de Datos

Edite el archivo `config/config.php` con sus credenciales de MySQL:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'comedores_industriales');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 3. Crear la Base de Datos

Ejecute los siguientes archivos SQL en orden:

```bash
# Desde MySQL CLI o phpMyAdmin:
mysql -u root -p < sql/schema.sql
mysql -u root -p < sql/sample_data.sql
```

O desde phpMyAdmin:
1. Cree una base de datos llamada `comedores_industriales`
2. Importe el archivo `sql/schema.sql`
3. Importe el archivo `sql/sample_data.sql`

### 4. Configurar Apache

**Opción A: Instalación en raíz del dominio**

Copie todos los archivos a `/var/www/html/` o el directorio raíz de su servidor.

**Opción B: Instalación en subdirectorio**

El sistema detecta automáticamente su URL base. Simplemente copie los archivos a cualquier subdirectorio y funcionará.

**Configuración de Virtual Host (Recomendado)**

```apache
<VirtualHost *:80>
    ServerName comedores.local
    DocumentRoot /path/to/comedoresindustriales/public
    
    <Directory /path/to/comedoresindustriales/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 5. Verificar la Instalación

Abra en su navegador:

```
http://localhost/test_connection.php
```

Este archivo verificará:
- ✓ Versión de PHP
- ✓ Extensión PDO MySQL
- ✓ Conexión a base de datos
- ✓ Detección automática de URL base
- ✓ Permisos de escritura

### 6. Acceder al Sistema

**URL de acceso:**
```
http://localhost/public/
```

**Credenciales de prueba:**
- Usuario: `admin`
- Contraseña: `admin123`

**Otros usuarios disponibles:**
- Coordinador Matutino: `coord_mat` / `admin123`
- Coordinador Vespertino: `coord_vesp` / `admin123`
- Chef Principal: `chef_principal` / `admin123`
- Operativo: `operativo1` / `admin123`

## 📁 Estructura del Proyecto

```
comedoresindustriales/
├── app/
│   ├── controllers/      # Controladores MVC
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── AttendanceController.php
│   │   ├── SituationsController.php
│   │   ├── ProductionController.php
│   │   ├── RecipesController.php
│   │   ├── ReportsController.php
│   │   ├── SettingsController.php
│   │   └── ApiController.php
│   ├── models/           # (Futuro: modelos de datos)
│   ├── views/            # Vistas HTML/PHP
│   │   ├── layouts/      # Plantillas base
│   │   ├── auth/         # Login
│   │   ├── dashboard/    # Panel principal
│   │   ├── attendance/   # Asistencia
│   │   ├── situations/   # Situaciones atípicas
│   │   ├── production/   # Órdenes de producción
│   │   └── reports/      # Reportes
│   ├── Controller.php    # Controlador base
│   └── Router.php        # Enrutador
├── config/
│   ├── config.php        # Configuración general
│   └── Database.php      # Clase de conexión
├── public/
│   ├── index.php         # Front controller
│   ├── .htaccess         # Reglas de reescritura
│   ├── css/              # Estilos personalizados
│   ├── js/               # Scripts JavaScript
│   └── images/           # Imágenes
├── sql/
│   ├── schema.sql        # Esquema de base de datos
│   └── sample_data.sql   # Datos de ejemplo (Querétaro)
├── .htaccess             # Redirección a public/
├── test_connection.php   # Prueba de instalación
└── README.md            # Este archivo
```

## 📊 Base de Datos

### Tablas Principales

- `usuarios` - Usuarios del sistema con roles
- `comedores` - Comedores industriales
- `turnos` - Turnos de trabajo (matutino, vespertino, nocturno)
- `asistencia_diaria` - Registros de asistencia (OPAD-034)
- `proyecciones` - Proyecciones calculadas
- `situaciones_atipicas` - Eventos que afectan asistencia
- `recetas` - Catálogo de recetas
- `ingredientes` - Catálogo de ingredientes
- `receta_ingredientes` - Gramajes (OPAD-025)
- `ordenes_produccion` - Órdenes de producción (OPAD-007)
- `orden_ingredientes` - Detalle de ingredientes por orden
- `lineas_servicio` - Líneas de servicio (caliente, fría, grill)
- `configuracion_sistema` - Parámetros configurables
- `logs_sistema` - Auditoría de acciones
- `api_tokens` - Tokens para API externa

### Datos de Ejemplo

El sistema incluye datos de ejemplo del Estado de Querétaro:
- 4 comedores en diferentes parques industriales
- 3 turnos configurados
- 6 recetas con ingredientes
- 20 ingredientes con proveedores locales
- Histórico de 30 días de asistencia
- Situaciones atípicas de ejemplo
- Órdenes de producción de muestra

## 🔐 Seguridad

- Contraseñas hasheadas con `password_hash()`
- Prepared statements para prevenir SQL injection
- Tokens CSRF en formularios
- Validación de roles por controlador
- Sesiones seguras con `httponly`
- Headers de seguridad en `.htaccess`

## 🔌 API REST

### Autenticación

Incluya el token en el header o query string:

```bash
X-API-Token: your-token-here
```

### Endpoints Disponibles

**POST /api/attendance/sync**
Sincronizar asistencia desde sistema externo
```json
{
  "comedor_id": 1,
  "turno_id": 1,
  "fecha": "2024-01-01",
  "comensales": 150
}
```

**POST /api/projections/get**
Obtener proyecciones
```json
{
  "start_date": "2024-01-01",
  "end_date": "2024-01-07"
}
```

**POST /api/production/export**
Exportar órdenes de producción
```json
{
  "fecha": "2024-01-01"
}
```

## 🎨 Personalización

### Cambiar Colores

Edite `app/views/layouts/header.php` y modifique la configuración de Tailwind:

```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#1e40af',  // Azul personalizado
                secondary: '#64748b',
            }
        }
    }
}
```

### Agregar Nuevo Comedor

1. Vaya a Configuración > Comedores
2. O ejecute SQL:
```sql
INSERT INTO comedores (nombre, ubicacion, ciudad, estado, capacidad_total, turnos_activos)
VALUES ('Nuevo Comedor', 'Dirección', 'Ciudad', 'Estado', 500, 'matutino,vespertino');
```

## 📱 Responsive Design

El sistema es completamente responsive y funciona en:
- 💻 Desktop
- 📱 Tablets
- 📱 Móviles

## 🐛 Solución de Problemas

### Error 500 - Internal Server Error

1. Verifique que mod_rewrite esté habilitado:
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

2. Verifique permisos del directorio:
```bash
chmod -R 755 /path/to/comedoresindustriales
```

### No se conecta a la base de datos

1. Verifique credenciales en `config/config.php`
2. Verifique que MySQL esté corriendo:
```bash
sudo service mysql status
```

### Rutas no funcionan (404)

1. Verifique que `.htaccess` exista en `public/`
2. Verifique `AllowOverride All` en la configuración de Apache

### Estilos no cargan

El sistema usa Tailwind CSS via CDN. Verifique su conexión a internet.

## 📖 Guía de Uso

### Flujo de Trabajo Típico

1. **Registrar Asistencia Diaria**
   - Ir a Asistencia > Registrar Asistencia
   - Seleccionar comedor, turno y fecha
   - Ingresar número real de comensales

2. **Calcular Proyecciones**
   - Ir a Asistencia > Proyecciones
   - El sistema calcula automáticamente basado en histórico
   - Aplicar ajustes manuales si es necesario

3. **Registrar Situación Atípica**
   - Ir a Situaciones > Nueva Situación
   - Especificar tipo, fechas e impacto
   - Las proyecciones futuras se ajustan automáticamente

4. **Generar Orden de Producción**
   - Ir a Producción > Nueva Orden
   - Seleccionar comedor, turno, fecha y receta
   - El sistema calcula ingredientes automáticamente
   - Imprimir orden para la cocina

5. **Revisar Dashboard**
   - Ver estadísticas del día
   - Alertas de desviaciones
   - Órdenes pendientes

## 🤝 Contribución

Este es un proyecto de código abierto. Las contribuciones son bienvenidas.

## 📄 Licencia

Este proyecto está bajo licencia MIT.

## 👥 Créditos

Desarrollado para comedores industriales del Estado de Querétaro, México.

## 📞 Soporte

Para soporte o preguntas, contacte al administrador del sistema.

---

**Sistema de Gestión para Comedores Industriales v1.0.0**  
Querétaro, México 🇲🇽
