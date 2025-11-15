# Guía de Reportes Financieros

## Resumen de Cambios

Este documento describe las mejoras implementadas en el sistema de gestión de comedores industriales, incluyendo la corrección de errores críticos y la implementación de nuevos reportes financieros.

## Errores Corregidos

### 1. Error PDO en ProductionController (Línea 69)
**Problema:** El sistema mezclaba parámetros posicionales (`?`) con parámetros nombrados (`:limit`, `:offset`) en la misma consulta PDO, causando el error `SQLSTATE[HY093]: Invalid parameter number: mixed named and positional parameters`.

**Solución:** Se modificó el código para usar únicamente parámetros posicionales, consolidando todos los parámetros en un solo array antes de ejecutar la consulta.

**Archivo modificado:** `/app/controllers/ProductionController.php`

### 2. Error PDO en AttendanceController (Línea 91)
**Problema:** Similar al error anterior, se mezclaban tipos de parámetros en la consulta PDO.

**Solución:** Se aplicó la misma corrección usando solo parámetros posicionales.

**Archivo modificado:** `/app/controllers/AttendanceController.php`

## Nuevos Reportes Financieros

### 1. Reporte Mensual

**Descripción:** Resumen de ingresos, egresos y balance mensual por comedor.

**Características:**
- Vista consolidada de todos los comedores
- Comparación con presupuesto asignado
- Porcentaje de ejecución presupuestal
- Estados presupuestales (activo, cerrado, excedido)
- Filtros por año, mes y comedor

**Acceso:** 
- URL: `/financial/monthly-report`
- Menú: Módulo Financiero → Reportes → Reporte Mensual

**Uso:**
1. Seleccione el año y mes deseados
2. Opcionalmente, filtre por un comedor específico
3. Haga clic en "Aplicar Filtros"
4. El reporte mostrará tarjetas resumen con totales generales
5. La tabla inferior muestra el desglose por comedor

**Datos Mostrados:**
- Total de ingresos
- Total de egresos
- Balance (ingresos - egresos)
- Presupuesto asignado y gastado
- Porcentaje de ejecución presupuestal
- Estado del presupuesto

### 2. Estado de Cuenta

**Descripción:** Detalle completo de todas las transacciones en un período específico.

**Características:**
- Lista detallada de transacciones
- Filtros por fecha, comedor y tipo de transacción
- Información de categoría y usuario que registró
- Resumen de totales
- Formato de fácil lectura

**Acceso:**
- URL: `/financial/account-statement`
- Menú: Módulo Financiero → Reportes → Estado de Cuenta

**Uso:**
1. Seleccione el rango de fechas (fecha inicio y fecha fin)
2. Opcionalmente, filtre por comedor
3. Opcionalmente, filtre por tipo (ingresos, egresos o todos)
4. Haga clic en "Aplicar Filtros"
5. El reporte mostrará todas las transacciones que coincidan con los filtros

**Datos Mostrados:**
- Fecha de la transacción
- Comedor
- Concepto y descripción
- Categoría
- Tipo (ingreso/egreso)
- Monto

### 3. Análisis por Categoría

**Descripción:** Distribución de gastos e ingresos por categoría con análisis estadístico.

**Características:**
- Segregación por tipo (ingresos y egresos)
- Estadísticas por categoría (cantidad, total, promedio)
- Porcentaje de participación de cada categoría
- Indicadores visuales (barras de progreso)
- Comparativa visual entre categorías

**Acceso:**
- URL: `/financial/category-analysis`
- Menú: Módulo Financiero → Reportes → Análisis por Categoría

**Uso:**
1. Seleccione el rango de fechas para el análisis
2. Opcionalmente, filtre por comedor
3. Haga clic en "Aplicar Filtros"
4. El reporte mostrará dos paneles: uno para ingresos y otro para egresos

**Datos Mostrados:**
- Nombre de la categoría
- Cantidad de transacciones
- Monto total
- Monto promedio
- Porcentaje respecto al total
- Barra visual de porcentaje

## Datos de Ejemplo

Se ha creado un script SQL completo con datos de ejemplo para facilitar las pruebas y demostración del sistema.

**Archivo:** `/sql/sample_financial_transactions.sql`

### Contenido del Script:

1. **Transacciones del mes actual (Noviembre 2025):**
   - Subsidios gubernamentales
   - Ventas de servicios (semanales)
   - Donaciones
   - Compras de ingredientes (múltiples)
   - Pagos de nómina (quincenales)
   - Servicios públicos (agua, luz, gas)
   - Mantenimiento
   - Equipo y utensilios
   - Transporte
   - Otros gastos

2. **Transacciones históricas:**
   - Octubre 2025 (completo)
   - Septiembre 2025 (completo)
   - Agosto 2025
   - Julio 2025
   - Junio 2025

3. **Actualización automática de presupuestos:**
   - Calcula totales gastados
   - Actualiza porcentajes de ejecución
   - Ajusta estados presupuestales

### Cómo Ejecutar el Script:

```sql
-- Opción 1: Desde línea de comandos MySQL
mysql -u usuario -p comedores_industriales < sql/sample_financial_transactions.sql

-- Opción 2: Desde phpMyAdmin
-- 1. Abrir phpMyAdmin
-- 2. Seleccionar la base de datos 'comedores_industriales'
-- 3. Ir a la pestaña SQL
-- 4. Copiar y pegar el contenido del archivo
-- 5. Ejecutar
```

### Estadísticas de Datos de Ejemplo:

- **Total de transacciones:** ~150 registros
- **Período cubierto:** 6 meses (Junio - Noviembre 2025)
- **Comedores incluidos:** Los 3 comedores activos en el sistema
- **Categorías utilizadas:** Todas las categorías predefinidas
- **Tipos de transacciones:** Ingresos y egresos

## Funcionalidades Adicionales en los Reportes

### Impresión
Todos los reportes incluyen un botón de "Imprimir" que permite:
- Imprimir el reporte directamente
- Guardar como PDF (usando la función de impresión del navegador)
- El formato de impresión está optimizado para papel

### Responsividad
Los reportes están diseñados con Tailwind CSS y son completamente responsivos:
- Se adaptan a diferentes tamaños de pantalla
- En dispositivos móviles, las tablas son desplazables horizontalmente
- Los filtros se reorganizan en una columna en pantallas pequeñas

### Indicadores Visuales
- **Colores por tipo:**
  - Verde: Ingresos
  - Rojo: Egresos
  - Azul: Balance/Totales
  - Amarillo/Naranja: Alertas presupuestales

- **Estados presupuestales:**
  - Verde: Activo (< 95%)
  - Amarillo: Cerrado (95-100%)
  - Rojo: Excedido (> 100%)

## Requisitos del Sistema

### Base de Datos
Las tablas necesarias ya deben existir si se ejecutaron los scripts de actualización previos:
- `transacciones_financieras`
- `categorias_financieras`
- `presupuestos`
- `comedores`
- `usuarios`

### Permisos
Para acceder a los reportes financieros, el usuario debe tener uno de los siguientes roles:
- `admin`
- `coordinador`

## Rutas Agregadas

```php
// Nuevas rutas en /public/index.php
$router->get('/financial/monthly-report', 'FinancialController', 'monthlyReport');
$router->get('/financial/account-statement', 'FinancialController', 'accountStatement');
$router->get('/financial/category-analysis', 'FinancialController', 'categoryAnalysis');
```

## Archivos Modificados

1. `/app/controllers/ProductionController.php` - Corrección de error PDO
2. `/app/controllers/AttendanceController.php` - Corrección de error PDO
3. `/app/controllers/FinancialController.php` - Nuevos métodos de reportes
4. `/app/views/financial/reports.php` - Actualización de enlaces
5. `/public/index.php` - Nuevas rutas

## Archivos Nuevos

1. `/app/views/financial/monthly_report.php` - Vista del reporte mensual
2. `/app/views/financial/account_statement.php` - Vista del estado de cuenta
3. `/app/views/financial/category_analysis.php` - Vista del análisis por categoría
4. `/sql/sample_financial_transactions.sql` - Datos de ejemplo

## Pruebas Recomendadas

1. **Verificar corrección de errores PDO:**
   - Acceder a `/production` con diferentes filtros y paginación
   - Acceder a `/attendance/history` con diferentes filtros y paginación
   - Verificar que no aparezcan errores PDO

2. **Probar Reporte Mensual:**
   - Acceder sin filtros (debe mostrar mes actual)
   - Filtrar por diferentes meses y años
   - Filtrar por comedor específico
   - Verificar que los totales coincidan

3. **Probar Estado de Cuenta:**
   - Acceder con fechas del último mes
   - Probar diferentes rangos de fechas
   - Filtrar por tipo (solo ingresos, solo egresos)
   - Filtrar por comedor
   - Verificar el conteo de transacciones

4. **Probar Análisis por Categoría:**
   - Verificar que se muestren ambos paneles (ingresos y egresos)
   - Comprobar que los porcentajes sumen 100% en cada tipo
   - Verificar las barras de progreso visuales
   - Probar con diferentes períodos

5. **Ejecutar Script de Datos:**
   - Ejecutar el script SQL de datos de ejemplo
   - Verificar que se insertaron las transacciones
   - Comprobar que los presupuestos se actualizaron
   - Probar los reportes con los datos nuevos

## Mantenimiento Futuro

### Agregar Nuevas Categorías
Para agregar nuevas categorías financieras:
1. Ir a `/financial/categories`
2. Completar el formulario con nombre, tipo y descripción
3. Las nuevas categorías estarán disponibles inmediatamente

### Agregar Exportación a Excel
Para implementar exportación a Excel en el futuro:
1. Instalar una librería PHP como PHPSpreadsheet
2. Crear nuevos métodos en `FinancialController` para exportación
3. Agregar botones de exportación en las vistas

### Gráficas Visuales
Para agregar gráficas (charts):
1. Integrar una librería como Chart.js
2. Preparar los datos en formato JSON desde los controladores
3. Renderizar las gráficas en las vistas

## Soporte

Para reportar problemas o solicitar mejoras, contacte al equipo de desarrollo o cree un issue en el repositorio del proyecto.

---

**Fecha de implementación:** 15 de Noviembre, 2025
**Versión del sistema:** 2.0
**Desarrollado por:** Sistema de Gestión de Comedores Industriales
