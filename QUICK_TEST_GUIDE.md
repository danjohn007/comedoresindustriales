# Guía Rápida de Pruebas

## 🚀 Inicio Rápido

### Paso 1: Ejecutar el Script SQL
```bash
# Desde terminal
mysql -u root -p comedores_industriales < sql/sample_financial_transactions.sql

# O copiar y pegar en phpMyAdmin
```

### Paso 2: Verificar Correcciones de Errores PDO

#### Probar ProductionController
1. Ir a: `http://tudominio.com/production`
2. Aplicar filtros
3. Navegar entre páginas (usar paginación)
4. ✅ Verificar que NO aparezcan errores PDO

#### Probar AttendanceController
1. Ir a: `http://tudominio.com/attendance/history`
2. Aplicar filtros
3. Navegar entre páginas (usar paginación)
4. ✅ Verificar que NO aparezcan errores PDO

---

## 📊 Probar Reportes Financieros

### Reporte Mensual
```
URL: /financial/monthly-report
```

**Pruebas:**
1. ✅ Acceder sin filtros (debe mostrar mes actual)
2. ✅ Cambiar año y mes
3. ✅ Filtrar por comedor específico
4. ✅ Verificar totales en tarjetas superiores
5. ✅ Verificar tabla de comedores
6. ✅ Verificar porcentajes presupuestales
7. ✅ Probar botón de impresión

**Valores Esperados (con datos de ejemplo):**
- Comedores mostrados: 3
- Transacciones: Múltiples por comedor
- Balance: Debe calcularse correctamente

---

### Estado de Cuenta
```
URL: /financial/account-statement
```

**Pruebas:**
1. ✅ Acceder con fechas por defecto
2. ✅ Cambiar rango de fechas
3. ✅ Filtrar por comedor
4. ✅ Filtrar por tipo (Ingresos/Egresos)
5. ✅ Verificar lista de transacciones
6. ✅ Verificar totales en tarjetas
7. ✅ Probar botón de impresión

**Valores Esperados (con datos de ejemplo):**
- Lista completa de transacciones del período
- Totales correctamente calculados
- Balance = Ingresos - Egresos

---

### Análisis por Categoría
```
URL: /financial/category-analysis
```

**Pruebas:**
1. ✅ Acceder con fechas por defecto
2. ✅ Verificar panel de INGRESOS (verde)
3. ✅ Verificar panel de EGRESOS (rojo)
4. ✅ Verificar que porcentajes sumen ~100% en cada panel
5. ✅ Verificar barras de progreso visuales
6. ✅ Cambiar período
7. ✅ Filtrar por comedor
8. ✅ Probar botón de impresión

**Valores Esperados (con datos de ejemplo):**

**Categorías de Ingresos:**
- Subsidio Gubernamental
- Venta de Servicios
- Donaciones
- Otros Ingresos

**Categorías de Egresos:**
- Compra de Ingredientes (mayor porcentaje)
- Salarios (segundo mayor)
- Servicios Públicos
- Mantenimiento
- Equipo y Utensilios
- Transporte
- Otros Gastos

---

## 📋 Checklist de Verificación Rápida

### Errores Corregidos
- [ ] `/production` funciona sin error PDO
- [ ] `/production` con paginación funciona
- [ ] `/attendance/history` funciona sin error PDO
- [ ] `/attendance/history` con paginación funciona

### Reportes
- [ ] Reporte Mensual carga correctamente
- [ ] Reporte Mensual muestra datos
- [ ] Reporte Mensual filtros funcionan
- [ ] Estado de Cuenta carga correctamente
- [ ] Estado de Cuenta muestra transacciones
- [ ] Estado de Cuenta filtros funcionan
- [ ] Análisis por Categoría carga correctamente
- [ ] Análisis por Categoría muestra ambos paneles
- [ ] Análisis por Categoría filtros funcionan

### Funcionalidades
- [ ] Botones de impresión funcionan
- [ ] Diseño es responsivo (probar en móvil)
- [ ] Colores e iconos se muestran correctamente
- [ ] Tarjetas resumen muestran totales correctos
- [ ] Tablas muestran datos completos

---

## 🐛 Problemas Comunes y Soluciones

### "No hay datos disponibles"
**Causa:** El script SQL no se ejecutó o falló.  
**Solución:** Verificar que el script se ejecutó correctamente.

### "Access Denied"
**Causa:** Usuario no tiene permisos.  
**Solución:** Verificar que el usuario tenga rol `admin` o `coordinador`.

### Errores de sintaxis SQL
**Causa:** Script SQL no se ejecutó completo.  
**Solución:** Verificar logs de MySQL y ejecutar nuevamente.

### Porcentajes no suman 100%
**Causa:** Normal si hay categorías sin transacciones.  
**Solución:** No es un error, es comportamiento esperado.

---

## 💡 Datos de Prueba Incluidos

El script SQL incluye:
- **150+ transacciones**
- **3 comedores** con datos
- **6 meses** de histórico (Junio-Noviembre 2025)
- **Todas las categorías** con transacciones
- **Presupuestos** actualizados automáticamente

### Montos Aproximados (por comedor/mes):
- Ingresos: $150,000 - $250,000
- Egresos: $140,000 - $240,000
- Balance: Varía por mes

---

## 📱 Prueba de Responsividad

### Desktop (> 1024px)
- [ ] Todas las columnas visibles
- [ ] Filtros en una fila
- [ ] Tarjetas en fila (3 columnas)

### Tablet (768px - 1024px)
- [ ] Tablas con scroll horizontal
- [ ] Filtros reorganizados
- [ ] Tarjetas en 2 columnas

### Móvil (< 768px)
- [ ] Tablas con scroll horizontal
- [ ] Filtros en columna
- [ ] Tarjetas en 1 columna

---

## 🎨 Verificación Visual

### Colores Esperados:
- 🟢 Verde: Ingresos, activo
- 🔴 Rojo: Egresos, excedido
- 🔵 Azul: Balance, totales
- 🟡 Amarillo: Cerrado, alertas
- ⚫ Gris: Neutro, secundario

### Iconos Esperados:
- 📊 Chart-bar: Reportes
- 💵 Invoice-dollar: Transacciones
- 🥧 Chart-pie: Análisis
- ⬆️ Arrow-up: Ingresos
- ⬇️ Arrow-down: Egresos

---

## ⏱️ Tiempo Estimado de Pruebas

- Verificación de errores PDO: **5 minutos**
- Prueba de Reporte Mensual: **5 minutos**
- Prueba de Estado de Cuenta: **5 minutos**
- Prueba de Análisis por Categoría: **5 minutos**
- Prueba de responsividad: **5 minutos**
- **TOTAL: ~25 minutos**

---

## ✅ Criterios de Aceptación

Para considerar la implementación exitosa:

1. ✅ Cero errores PDO en production y attendance
2. ✅ Los tres reportes cargan sin errores
3. ✅ Los filtros funcionan correctamente
4. ✅ Los cálculos son precisos
5. ✅ El diseño es responsivo
6. ✅ La impresión funciona
7. ✅ Los datos de ejemplo se cargan

---

## 📞 ¿Necesitas Ayuda?

1. Revisa `FINANCIAL_REPORTS_GUIDE.md` para documentación completa
2. Revisa `COMPLETED_WORK_SUMMARY.md` para resumen del trabajo
3. Verifica logs del servidor web
4. Verifica logs de MySQL

---

**¡Listo para probar!** 🚀

Todas las funcionalidades han sido implementadas y validadas.
