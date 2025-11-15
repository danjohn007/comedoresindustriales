# 🚀 Guía Rápida de Uso - Nuevas Funcionalidades

## Sistema de Comedores Industriales v1.1.0

---

## 📋 Índice Rápido
1. [Instalación en 3 Pasos](#instalación-en-3-pasos)
2. [Navegación con Nuevo Sidebar](#navegación-con-nuevo-sidebar)
3. [Mi Perfil](#mi-perfil)
4. [Gestión de Ingredientes](#gestión-de-ingredientes)
5. [Accesos Directos](#accesos-directos)
6. [Configuración del Sistema](#configuración-del-sistema)

---

## Instalación en 3 Pasos

### ⚡ Paso 1: Código
El código ya está en la rama actual. Si no está actualizado:
```bash
git checkout copilot/develop-mi-perfil-module
git pull origin copilot/develop-mi-perfil-module
```

### ⚡ Paso 2: Base de Datos
Ejecutar el script SQL:
```bash
mysql -u root -p comedores_industriales < sql/update_configurations.sql
```

### ⚡ Paso 3: Verificar
Abrir navegador en: `http://localhost/public/`

---

## Navegación con Nuevo Sidebar

### 🖥️ En Desktop
```
┌─────────────────────────────────────────┐
│ SIDEBAR (Siempre Visible)              │
├─────────────────────────────────────────┤
│ 👤 Usuario Actual                       │
│    Rol                                  │
├─────────────────────────────────────────┤
│ 🏠 Dashboard                            │
│ 👥 Asistencia                           │
│ ⚠️  Situaciones                          │
│ 📋 Producción                           │
│ 📖 Recetas                              │
│ 📊 Reportes                             │
├─ Accesos Directos ─────────────────────┤
│ 👨‍💼 Usuarios                              │
│ 🏢 Comedores                            │
│ 🥕 Ingredientes                         │
├─ Administración ────────────────────────┤
│ ⚙️  Configuración                        │
├─────────────────────────────────────────┤
│ 👤 Mi Perfil                            │
│ 🚪 Cerrar Sesión                        │
└─────────────────────────────────────────┘
```

### 📱 En Móvil
```
┌─────────────────────────────────────────┐
│ ☰  Comedores                            │  ← Barra superior
└─────────────────────────────────────────┘

Al tocar ☰:
┌─────────────────────┐
│ ✕  Cerrar           │  ← Sidebar deslizante
│                     │
│ 👤 Usuario          │
│ ...menú completo... │
└─────────────────────┘
```

---

## Mi Perfil

### Cómo Acceder
1. Clic en "Mi Perfil" en el sidebar (parte inferior)
2. O ir a: `/profile`

### Qué Puedes Ver
- ✅ Foto de perfil (icono)
- ✅ Nombre completo
- ✅ Username
- ✅ Email
- ✅ Rol en el sistema
- ✅ Estado (Activo/Inactivo)
- ✅ Fecha de registro
- ✅ Último acceso

### Cambiar Contraseña
1. En perfil, clic en "Cambiar Contraseña"
2. Ingresar contraseña actual
3. Ingresar nueva contraseña (mínimo 6 caracteres)
4. Confirmar nueva contraseña
5. Guardar

**Validaciones:**
- ✓ Contraseña actual debe ser correcta
- ✓ Nueva contraseña mínimo 6 caracteres
- ✓ Nueva contraseña y confirmación deben coincidir

---

## Gestión de Ingredientes

### Cómo Acceder
**3 formas:**
1. Dashboard → Card "Ingredientes"
2. Sidebar → Accesos Directos → "Ingredientes"
3. URL: `/settings/ingredients`

### Ver Ingredientes
Tabla con columnas:
- Nombre
- Unidad de Medida
- Costo Unitario
- Estado (Activo/Inactivo)
- **Acciones** ← NUEVO

### Agregar Ingrediente
1. Clic en botón "Agregar Ingrediente"
2. Llenar formulario modal:
   - Nombre (requerido)
   - Unidad de medida (requerido)
   - Costo unitario (requerido)
   - Proveedor (opcional)
3. Guardar

### Editar Ingrediente
1. Clic en ✏️ (icono editar) en la fila
2. Modificar campos en el modal
3. Guardar

### Ver Detalles
1. Clic en 👁️ (icono ver)
2. Se muestra información en alerta

### Suspender/Activar
1. Clic en ⏸️ (icono suspender) o ▶️ (icono activar)
2. Confirmar acción
3. Estado cambia automáticamente

### Eliminar
1. Clic en 🗑️ (icono eliminar)
2. Confirmar eliminación
3. **Importante:** No se puede eliminar si está en uso en recetas

---

## Accesos Directos

### En Dashboard
```
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│  👨‍💼 Usuarios     │  │  🏢 Comedores    │  │  🥕 Ingredientes │
│                  │  │                  │  │                  │
│  Gestionar       │  │  Gestionar       │  │  Catálogo de     │
│  usuarios del    │  │  comedores       │  │  ingredientes    │
│  sistema         │  │                  │  │                  │
└──────────────────┘  └──────────────────┘  └──────────────────┘
```

### Permisos
- **Usuarios**: Solo Administradores
- **Comedores**: Solo Administradores
- **Ingredientes**: Administradores y Chefs

---

## Configuración del Sistema

### Cómo Acceder
1. Sidebar → Administración → "Configuración" (solo admin)
2. URL: `/settings`

### Nuevas Configuraciones Disponibles

#### 1. General
```
📝 Nombre del sitio: _________________
🖼️ Logotipo: _________________________
```

#### 2. Correo Electrónico
```
📧 Email remitente: __________________
🌐 SMTP Host: ________________________
🔌 SMTP Puerto: ______________________
👤 SMTP Usuario: _____________________
🔒 SMTP Contraseña: __________________
🔐 Seguridad: ☐ TLS  ☐ SSL
```

#### 3. WhatsApp Chatbot
```
📱 Número WhatsApp: __________________
🔑 Token API: ________________________
☐ Activar integración
```

#### 4. Contacto
```
☎️  Teléfono principal: ______________
🚨 Teléfono emergencias: _____________
🕐 Horario inicio: ___________________
🕔 Horario fin: ______________________
📅 Días de atención: _________________
```

#### 5. Colores del Sistema
```
🎨 Color primario:     #______
🎨 Color secundario:   #______
🎨 Color acento:       #______
🎨 Color éxito:        #______
🎨 Color advertencia:  #______
🎨 Color error:        #______
```

#### 6. PayPal
```
🏦 Modo: ☐ Sandbox  ☐ Production
🔑 Client ID: ________________________
🔒 Secret: ___________________________
📧 Email cuenta: _____________________
☐ Activar pagos PayPal
```

#### 7. API QR Códigos
```
🔧 Proveedor: ________________________
🔑 API Key: __________________________
🌐 URL personalizada: ________________
📏 Tamaño default: _____ px
```

#### 8. Shelly Relay
```
🌐 URL API: __________________________
🔑 Token: ____________________________
📋 Dispositivos (JSON): ______________
☐ Activar integración
```

#### 9. HikVision
```
🌐 URL API: __________________________
👤 Usuario: __________________________
🔒 Contraseña: _______________________
📋 Dispositivos (JSON): ______________
☐ Activar integración
```

#### 10. Sistema Global
```
🔧 Modo mantenimiento:        ☐
📝 Registro de logs:           ☑
⏱️  Tiempo sesión:             3600 seg
🔐 Max intentos login:        5
🌍 Zona horaria:              America/Mexico_City
🌐 Idioma:                    Español
💾 Backup automático:          ☑
📅 Frecuencia backup:         Diario
📧 Notificaciones email:       ☑
📱 Notificaciones push:        ☐
```

---

## 🎯 Casos de Uso Comunes

### Caso 1: Agregar un Nuevo Ingrediente
```
1. Ir a Dashboard
2. Clic en card "Ingredientes"
3. Clic en "Agregar Ingrediente"
4. Llenar formulario:
   - Nombre: "Aceite de Oliva"
   - Unidad: "Litros (l)"
   - Costo: 85.50
   - Proveedor: "Aceites del Sur"
5. Guardar
✅ Listo! Ingrediente agregado
```

### Caso 2: Cambiar mi Contraseña
```
1. Clic en "Mi Perfil" (sidebar inferior)
2. Clic en "Cambiar Contraseña"
3. Ingresar contraseña actual
4. Ingresar nueva contraseña
5. Confirmar nueva contraseña
6. Guardar
✅ Contraseña actualizada!
```

### Caso 3: Configurar Email del Sistema
```
1. Sidebar → Configuración (solo admin)
2. Buscar sección "Correo"
3. Llenar datos SMTP:
   - Host: smtp.gmail.com
   - Puerto: 587
   - Usuario: sistema@empresa.com
   - Contraseña: ********
   - Seguridad: TLS
4. Guardar Cambios
✅ Email configurado!
```

### Caso 4: Suspender un Ingrediente
```
1. Ir a Ingredientes
2. Buscar ingrediente en tabla
3. Clic en icono ⏸️ (suspender)
4. Confirmar
✅ Ingrediente suspendido (no se eliminará)
```

---

## ❓ Preguntas Frecuentes

### ¿El sidebar se ve en móvil?
Sí, pero está oculto. Toca el botón ☰ en la esquina superior izquierda para abrirlo.

### ¿Puedo eliminar un ingrediente en uso?
No, el sistema no lo permitirá. Primero debes removerlo de las recetas.

### ¿Todos pueden ver Mi Perfil?
Sí, todos los usuarios autenticados pueden ver su propio perfil.

### ¿Quién puede agregar ingredientes?
Solo administradores y chefs.

### ¿Las configuraciones son inmediatas?
Sí, los cambios en Configuración se guardan inmediatamente al hacer clic en "Guardar Cambios".

### ¿Se pierden datos al suspender un ingrediente?
No, solo cambia a estado "Inactivo". Puedes reactivarlo cuando quieras.

---

## 🔧 Solución de Problemas

### El sidebar no aparece
- **Desktop**: Debe aparecer automáticamente a la izquierda
- **Móvil**: Toca el botón ☰ en la parte superior

### No puedo agregar ingredientes
- Verifica que tienes rol de "admin" o "chef"
- Verifica que todos los campos requeridos estén llenos

### El modal no se cierra
- Toca el botón "Cancelar" o la "X" en la esquina superior derecha
- Si persiste, recarga la página

### Cambios no se guardan
- Verifica tu conexión a internet
- Revisa que no haya errores en la consola del navegador (F12)
- Verifica que ejecutaste el script SQL

### No veo las nuevas configuraciones
- **Asegúrate de haber ejecutado:** `sql/update_configurations.sql`
- Verifica que eres administrador
- Recarga la página

---

## 📞 Soporte

Si necesitas ayuda adicional:

1. **Documentación Técnica**: Ver `CAMBIOS_IMPLEMENTADOS.md`
2. **Resumen Completo**: Ver `RESUMEN_FINAL.md`
3. **Logs del Sistema**: Revisar tabla `logs_sistema` en BD
4. **Contacto**: Administrador del sistema

---

## ✅ Checklist Post-Instalación

Verifica que todo funciona:

- [ ] ✅ Sidebar aparece en desktop
- [ ] ✅ Sidebar funciona en móvil con overlay
- [ ] ✅ Puedo acceder a Mi Perfil
- [ ] ✅ Puedo cambiar mi contraseña
- [ ] ✅ Veo accesos directos en dashboard
- [ ] ✅ Puedo agregar ingredientes
- [ ] ✅ Puedo editar ingredientes
- [ ] ✅ Puedo suspender/activar ingredientes
- [ ] ✅ Todas las vistas cargan sin error
- [ ] ✅ Veo nuevas configuraciones en Settings

---

## 🎉 ¡Disfruta las Nuevas Funcionalidades!

El sistema ahora es más fácil de usar con:
- ✨ Navegación lateral intuitiva
- ✨ Gestión completa de ingredientes
- ✨ Perfil personalizable
- ✨ 50+ nuevas configuraciones
- ✨ Todas las vistas completas

---

**Versión:** 1.1.0  
**Última Actualización:** Noviembre 2024  
**Mantenedor:** Sistema de Comedores Industriales
