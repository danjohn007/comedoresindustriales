# Resumen de Implementación - Mejoras al Sistema de Comedores Industriales

**Fecha:** 15 de noviembre de 2025  
**Versión:** 1.1.0

## Descripción General

Este documento detalla todas las mejoras implementadas en el sistema de gestión para comedores industriales basado en los requerimientos especificados en el issue.

---

## 1. Corrección de Errores SQL

### 1.1 Error LIMIT/OFFSET en AttendanceController
**Archivo:** `app/controllers/AttendanceController.php`  
**Línea:** 78-91  
**Problema:** PDO trataba LIMIT y OFFSET como strings en lugar de integers, causando error de sintaxis SQL.  
**Solución:** Implementado uso de named parameters (`:limit`, `:offset`) con `PDO::PARAM_INT`.

```php
$query .= " ORDER BY a.fecha DESC, t.hora_inicio LIMIT :limit OFFSET :offset";

$stmt = $this->db->prepare($query);

// Bind regular params
foreach ($params as $key => $value) {
    $stmt->bindValue($key + 1, $value);
}

// Bind LIMIT and OFFSET as integers
$stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

$stmt->execute();
```

### 1.2 Error LIMIT/OFFSET en ProductionController
**Archivo:** `app/controllers/ProductionController.php`  
**Línea:** 56-69  
**Problema:** Mismo error que en AttendanceController.  
**Solución:** Misma implementación con named parameters.

---

## 2. Corrección de Warnings Deprecated

### 2.1 Warnings substr/strlen en Projections
**Archivo:** `app/views/attendance/projections.php`  
**Líneas:** 152-153  
**Problema:** PHP 8.1+ no acepta `null` como parámetro en `substr()` y `strlen()`.  
**Solución:** Uso del operador null coalescing.

```php
<?php echo htmlspecialchars(substr($proj['justificacion_ajuste'] ?? '', 0, 50)); ?>
<?php if (strlen($proj['justificacion_ajuste'] ?? '') > 50) echo '...'; ?>
```

---

## 3. Corrección de Ruta del Logo

### 3.1 Logo Upload Path Fix
**Archivo:** `app/controllers/SettingsController.php`  
**Línea:** 47-55  
**Problema:** La ruta del logo no consideraba la BASE_URL, enviando ruta incorrecta.  
**Solución:** Cálculo de ruta relativa correcta basada en DOCUMENT_ROOT.

```php
if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $filePath)) {
    // Use BASE_URL to ensure correct path
    $logoPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $uploadDir) . $fileName;
    // Normalize path separators
    $logoPath = str_replace('\\', '/', $logoPath);
    $_POST['logo_sistema'] = $logoPath;
}
```

---

## 4. Agregar Ingrediente - Proveedor Seleccionable

### 4.1 Actualización de Controller
**Archivo:** `app/controllers/SettingsController.php`

**Método `ingredients()`** - Agregado fetch de proveedores:
```php
// Get suppliers for dropdown
$stmt = $this->db->query("SELECT id, nombre FROM proveedores WHERE activo = 1 ORDER BY nombre");
$proveedores = $stmt->fetchAll();

$data = [
    'title' => 'Catálogo de Ingredientes',
    'ingredientes' => $ingredientes,
    'proveedores' => $proveedores  // Nuevo
];
```

**Método `createIngredient()`** - Modificado para usar `proveedor_id`:
```php
$proveedorId = !empty($_POST['proveedor_id']) ? intval($_POST['proveedor_id']) : null;

$stmt = $this->db->prepare("
    INSERT INTO ingredientes (nombre, unidad_medida, costo_unitario, proveedor_id, activo)
    VALUES (?, ?, ?, ?, 1)
");
$stmt->execute([$nombre, $unidadMedida, $costoUnitario, $proveedorId]);
```

**Método `updateIngredient()`** - Similar actualización.

### 4.2 Actualización de Vista
**Archivo:** `app/views/settings/ingredients.php`

Campo cambiado de input text a dropdown:
```php
<select id="proveedor_id" name="proveedor_id"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    <option value="">Seleccione un proveedor...</option>
    <?php if (!empty($proveedores)): ?>
        <?php foreach ($proveedores as $prov): ?>
            <option value="<?php echo $prov['id']; ?>">
                <?php echo htmlspecialchars($prov['nombre']); ?>
            </option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>
```

JavaScript actualizado para manejar `proveedor_id`:
```javascript
document.getElementById('proveedor_id').value = ing.proveedor_id || '';
```

---

## 5. Editar Receta - Permitir Edición de Ingredientes

### 5.1 Actualización de Vista
**Archivo:** `app/views/recipes/edit.php`

Ingredientes ahora son editables con campos de formulario:
```php
<div id="ingredientes-container" class="space-y-3">
    <?php if (!empty($recetaIngredientes)): ?>
        <?php foreach ($recetaIngredientes as $idx => $ing): ?>
        <div class="flex gap-3 items-start p-3 bg-gray-50 rounded-lg" id="ingrediente-existing-<?php echo $ing['id']; ?>">
            <input type="hidden" name="ingredientes_existentes[<?php echo $ing['id']; ?>][id]" value="<?php echo $ing['id']; ?>">
            <div class="flex-1">
                <select name="ingredientes_existentes[<?php echo $ing['id']; ?>][ingrediente_id]" required>
                    <!-- Options -->
                </select>
            </div>
            <div class="w-32">
                <input type="number" name="ingredientes_existentes[<?php echo $ing['id']; ?>][cantidad]" 
                       value="<?php echo $ing['cantidad']; ?>" required>
            </div>
            <!-- Más campos -->
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
```

JavaScript para agregar/eliminar ingredientes:
```javascript
function addIngredient() {
    // Agregar nuevo ingrediente dinámicamente
}

function removeExistingIngredient(id) {
    // Marcar para eliminación
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = `ingredientes_eliminar[${id}]`;
    input.value = id;
    document.querySelector('form').appendChild(input);
    element.remove();
}
```

### 5.2 Actualización de Controller
**Archivo:** `app/controllers/RecipesController.php`

Método `update()` completamente reescrito para manejar ingredientes:
```php
public function update($id) {
    // ... validaciones ...
    
    $ingredientesExistentes = $_POST['ingredientes_existentes'] ?? [];
    $ingredientesNuevos = $_POST['ingredientes_nuevos'] ?? [];
    $ingredientesEliminar = $_POST['ingredientes_eliminar'] ?? [];
    
    try {
        $this->db->beginTransaction();
        
        // Update recipe
        $stmt = $this->db->prepare("UPDATE recetas SET ...");
        $stmt->execute([...]);
        
        // Delete marked ingredients
        if (!empty($ingredientesEliminar)) {
            $stmtDel = $this->db->prepare("DELETE FROM receta_ingredientes WHERE id = ? AND receta_id = ?");
            foreach ($ingredientesEliminar as $ingId) {
                $stmtDel->execute([$ingId, $id]);
            }
        }
        
        // Update existing ingredients
        $stmtUpdate = $this->db->prepare("UPDATE receta_ingredientes SET ...");
        foreach ($ingredientesExistentes as $ingId => $ing) {
            $stmtUpdate->execute([...]);
        }
        
        // Add new ingredients
        $stmtInsert = $this->db->prepare("INSERT INTO receta_ingredientes ...");
        foreach ($ingredientesNuevos as $ing) {
            $stmtInsert->execute([...]);
        }
        
        $this->db->commit();
        // ...
    }
}
```

---

## 6. Nueva Receta - Mínimo 2 Ingredientes Requeridos

### 6.1 Actualización de Vista
**Archivo:** `app/views/recipes/create.php`

Sección de ingredientes agregada:
```php
<div class="pt-6 border-t">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-carrot mr-2"></i> Ingredientes *
            <span class="text-sm text-red-600 font-normal">(Mínimo 2 ingredientes requeridos)</span>
        </h3>
        <button type="button" onclick="addIngredient()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Agregar Ingrediente
        </button>
    </div>
    
    <div id="ingredientes-container" class="space-y-3">
        <!-- Ingredients will be added dynamically -->
    </div>
</div>
```

JavaScript para gestión dinámica:
```javascript
let ingredienteCounter = 0;

function addIngredient() {
    // Crear y agregar nuevo campo de ingrediente
    const div = document.createElement('div');
    div.innerHTML = `
        <select name="ingredientes[${ingredienteCounter}][ingrediente_id]" required>...</select>
        <input type="number" name="ingredientes[${ingredienteCounter}][cantidad]" required>
        <select name="ingredientes[${ingredienteCounter}][unidad]" required>...</select>
        <input type="text" name="ingredientes[${ingredienteCounter}][notas]">
        <button type="button" onclick="removeIngredient(${ingredienteCounter})">...</button>
    `;
    container.appendChild(div);
}

// Validación de mínimo 2 ingredientes
document.querySelector('form').addEventListener('submit', function(e) {
    const ingredientInputs = document.querySelectorAll('[name^="ingredientes"][name$="[ingrediente_id]"]');
    if (ingredientInputs.length < 2) {
        e.preventDefault();
        alert('Debe agregar al menos 2 ingredientes a la receta');
        return false;
    }
});

// Agregar 2 ingredientes por defecto al cargar
document.addEventListener('DOMContentLoaded', function() {
    addIngredient();
    addIngredient();
});
```

### 6.2 Validación en Controller
**Archivo:** `app/controllers/RecipesController.php`

Método `store()` ya incluía validación:
```php
$ingredientes = $_POST['ingredientes'] ?? [];

// Validar ingredientes mínimos
if (count($ingredientes) < 2) {
    $_SESSION['error'] = 'Debe agregar al menos 2 ingredientes a la receta';
    $this->redirect('/recipes/create');
}
```

---

## 7. Módulo Financiero - Movimientos Recientes

### 7.1 Nueva Vista
**Archivo:** `app/views/financial/recent_movements.php` (NUEVO)

Vista completa con tabla de movimientos de últimos 30 días:
```php
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th>Fecha</th>
                <th>Comedor</th>
                <th>Concepto</th>
                <th>Categoría</th>
                <th>Tipo</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movements as $mov): ?>
            <tr>
                <!-- Mostrar datos del movimiento -->
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

### 7.2 Nuevo Método en Controller
**Archivo:** `app/controllers/FinancialController.php`

```php
public function recentMovements() {
    $this->requireAuth();
    $this->requireRole(['admin', 'coordinador']);
    
    // Get last 30 days transactions
    $stmt = $this->db->query("
        SELECT t.*, c.nombre as comedor_nombre, u.nombre_completo as creado_por_nombre,
               cat.nombre as categoria_nombre
        FROM transacciones_financieras t
        LEFT JOIN comedores c ON t.comedor_id = c.id
        LEFT JOIN usuarios u ON t.creado_por = u.id
        LEFT JOIN categorias_financieras cat ON t.categoria_id = cat.id
        WHERE t.fecha_transaccion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ORDER BY t.fecha_transaccion DESC, t.fecha_creacion DESC
        LIMIT 100
    ");
    $movements = $stmt->fetchAll();
    
    $data = [
        'title' => 'Movimientos Recientes (últimos 30 días)',
        'movements' => $movements
    ];
    
    $this->view('financial/recent_movements', $data);
}
```

### 7.3 Botón de Acceso Rápido
**Archivo:** `app/views/financial/index.php`

Agregado en la sección de Quick Actions:
```php
<a href="<?php echo Router::url('/financial/recent-movements'); ?>" 
   class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg p-6 text-center transition">
    <i class="fas fa-clock text-3xl mb-3"></i>
    <p class="font-semibold">Movimientos Recientes</p>
</a>
```

### 7.4 Nueva Ruta
**Archivo:** `public/index.php`

```php
$router->get('/financial/recent-movements', 'FinancialController', 'recentMovements');
```

---

## 8. Módulo Financiero - Catálogo de Categorías

### 8.1 Nueva Vista
**Archivo:** `app/views/financial/categories.php` (NUEVO)

Vista completa con gestión de categorías separadas por tipo (Ingresos/Egresos):
```php
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Income Categories -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b bg-green-50">
            <h2 class="text-xl font-bold text-green-800">
                <i class="fas fa-arrow-up mr-2"></i> Categorías de Ingresos
            </h2>
        </div>
        <div class="p-6">
            <?php foreach ($ingresos as $cat): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <div class="font-medium"><?php echo $cat['nombre']; ?></div>
                    <div class="text-sm text-gray-600"><?php echo $cat['descripcion']; ?></div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="editCategory(<?php echo $cat['id']; ?>)">Editar</button>
                    <button onclick="toggleCategory(<?php echo $cat['id']; ?>)">Toggle</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Expense Categories (similar structure) -->
</div>

<!-- Add/Edit Modal -->
<div id="categoryModal">
    <form id="categoryForm">
        <input type="text" id="nombre" name="nombre" required>
        <select id="tipo" name="tipo" required>
            <option value="ingreso">Ingreso</option>
            <option value="egreso">Egreso</option>
        </select>
        <textarea id="descripcion" name="descripcion"></textarea>
    </form>
</div>
```

JavaScript para gestión AJAX:
```javascript
async function editCategory(id) {
    const response = await fetch(baseUrl + '/financial/categories/get/' + id);
    const result = await response.json();
    
    if (result.success) {
        document.getElementById('nombre').value = result.data.nombre;
        document.getElementById('tipo').value = result.data.tipo;
        // ...
    }
}

async function saveCategory() {
    const formData = new FormData(form);
    const endpoint = id ? '/financial/categories/update' : '/financial/categories/create';
    
    const response = await fetch(baseUrl + endpoint, {
        method: 'POST',
        body: formData
    });
    // ...
}
```

### 8.2 Nuevos Métodos en Controller
**Archivo:** `app/controllers/FinancialController.php`

```php
public function categories() {
    $stmt = $this->db->query("
        SELECT * FROM categorias_financieras 
        ORDER BY tipo, nombre
    ");
    $categorias = $stmt->fetchAll();
    // ...
}

public function createCategory() {
    $nombre = trim($_POST['nombre'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');
    
    $stmt = $this->db->prepare("
        INSERT INTO categorias_financieras (nombre, tipo, descripcion)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$nombre, $tipo, $descripcion]);
    // ...
}

public function getCategory($id) { /* ... */ }
public function updateCategory() { /* ... */ }
public function toggleCategory() { /* ... */ }
```

### 8.3 Integración en Formulario de Transacciones
**Archivo:** `app/views/financial/transactions.php`

Campo de categoría actualizado:
```php
<div>
    <label>Categoría 
        <a href="<?php echo Router::url('/financial/categories'); ?>" 
           class="text-blue-600 text-xs ml-2" target="_blank">
            (Ver catálogo)
        </a>
    </label>
    <select name="categoria_id" id="categoria_id">
        <option value="">Seleccione una categoría...</option>
        <?php foreach ($categorias as $cat): ?>
        <option value="<?php echo $cat['id']; ?>" data-tipo="<?php echo $cat['tipo']; ?>">
            <?php echo htmlspecialchars($cat['nombre']); ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>
```

JavaScript para filtrar por tipo:
```javascript
function filterCategories() {
    const tipo = document.getElementById('tipo').value;
    const categoriaSelect = document.getElementById('categoria_id');
    const options = categoriaSelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = '';
            return;
        }
        
        const optionTipo = option.getAttribute('data-tipo');
        if (!tipo || optionTipo === tipo) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });
}
```

### 8.4 Controller de Transacciones Actualizado
**Archivo:** `app/controllers/FinancialController.php`

Método `transactions()` actualizado para incluir categorías:
```php
public function transactions() {
    // ...
    
    // Get categorias for dropdown
    $stmt = $this->db->query("
        SELECT id, nombre, tipo 
        FROM categorias_financieras 
        WHERE activo = 1 
        ORDER BY tipo, nombre
    ");
    $categorias = $stmt->fetchAll();
    
    $data = [
        'title' => 'Transacciones Financieras',
        'transactions' => $transactions,
        'comedores' => $comedores,
        'categorias' => $categorias  // Nuevo
    ];
}
```

Método `createTransaction()` actualizado:
```php
public function createTransaction() {
    $categoriaId = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;
    
    $stmt = $this->db->prepare("
        INSERT INTO transacciones_financieras 
        (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([..., $categoriaId, ...]);
}
```

### 8.5 Nuevas Rutas
**Archivo:** `public/index.php`

```php
$router->get('/financial/categories', 'FinancialController', 'categories');
$router->post('/financial/categories/create', 'FinancialController', 'createCategory');
$router->get('/financial/categories/get/:id', 'FinancialController', 'getCategory');
$router->post('/financial/categories/update', 'FinancialController', 'updateCategory');
$router->post('/financial/categories/toggle', 'FinancialController', 'toggleCategory');
```

---

## 9. Script SQL de Actualización

### 9.1 Archivo Principal
**Archivo:** `sql/update_improvements.sql` (NUEVO)

Script completo con las siguientes secciones:

#### 9.1.1 Creación de Tablas
```sql
-- Tabla proveedores
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

-- Tabla categorias_financieras
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

-- Tabla logs_exportacion
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
```

#### 9.1.2 Ajustes en Ingredientes
```sql
-- Agregar columna proveedor_id
ALTER TABLE ingredientes ADD COLUMN proveedor_id INT DEFAULT NULL AFTER proveedor;

-- Migrar datos de proveedor (texto) a tabla proveedores
INSERT INTO proveedores (nombre, ciudad, activo, fecha_creacion)
SELECT DISTINCT TRIM(proveedor), 'Querétaro', 1, NOW()
FROM ingredientes
WHERE proveedor IS NOT NULL AND TRIM(proveedor) != ''
  AND NOT EXISTS (SELECT 1 FROM proveedores p WHERE p.nombre = TRIM(ingredientes.proveedor));

-- Actualizar proveedor_id basado en nombre
UPDATE ingredientes ig
LEFT JOIN proveedores p ON p.nombre = TRIM(ig.proveedor)
SET ig.proveedor_id = p.id
WHERE ig.proveedor IS NOT NULL AND TRIM(ig.proveedor) != '';

-- Agregar foreign key
ALTER TABLE ingredientes 
ADD CONSTRAINT fk_ingrediente_proveedor 
FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL;
```

#### 9.1.3 Ajustes en Transacciones
```sql
-- Agregar columna categoria_id
ALTER TABLE transacciones_financieras 
ADD COLUMN categoria_id INT DEFAULT NULL AFTER categoria;

-- Migrar categorías existentes (texto) a tabla categorias_financieras
INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT DISTINCT TRIM(categoria), 'egreso', 'Migrada automáticamente desde transacciones'
FROM transacciones_financieras
WHERE categoria IS NOT NULL AND TRIM(categoria) != ''
  AND NOT EXISTS (SELECT 1 FROM categorias_financieras c WHERE c.nombre = TRIM(transacciones_financieras.categoria));

-- Actualizar categoria_id basado en nombre
UPDATE transacciones_financieras tf
LEFT JOIN categorias_financieras cf ON cf.nombre = TRIM(tf.categoria)
SET tf.categoria_id = cf.id
WHERE tf.categoria IS NOT NULL AND TRIM(tf.categoria) != '';

-- Agregar foreign key
ALTER TABLE transacciones_financieras 
ADD CONSTRAINT fk_transaccion_categoria 
FOREIGN KEY (categoria_id) REFERENCES categorias_financieras(id) ON DELETE SET NULL;
```

#### 9.1.4 Población de Categorías
```sql
-- Categorías de Ingresos
INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Subsidio Gubernamental', 'ingreso', 'Ingresos por subsidios del gobierno'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Subsidio Gubernamental');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Venta de Servicios', 'ingreso', 'Ingresos por venta de comidas'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Venta de Servicios');

-- ... más categorías ...

-- Categorías de Egresos
INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Compra de Ingredientes', 'egreso', 'Gastos en compra de ingredientes y alimentos'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Compra de Ingredientes');

INSERT INTO categorias_financieras (nombre, tipo, descripcion)
SELECT 'Salarios', 'egreso', 'Pago de salarios al personal'
WHERE NOT EXISTS (SELECT 1 FROM categorias_financieras WHERE nombre = 'Salarios');

-- ... más categorías ...
```

### 9.2 Instrucciones de Ejecución

```bash
# Conectar a MySQL
mysql -u [usuario] -p [base_datos]

# Ejecutar script
source /path/to/update_improvements.sql;

# O desde línea de comando
mysql -u [usuario] -p [base_datos] < update_improvements.sql
```

---

## 10. Resumen de Cambios por Archivo

### Archivos Modificados

1. **app/controllers/AttendanceController.php**
   - Corrección de error SQL LIMIT/OFFSET

2. **app/controllers/ProductionController.php**
   - Corrección de error SQL LIMIT/OFFSET

3. **app/controllers/SettingsController.php**
   - Corrección de ruta del logo
   - Agregado fetch de proveedores en `ingredients()`
   - Modificado `createIngredient()` para usar `proveedor_id`
   - Modificado `updateIngredient()` para usar `proveedor_id`

4. **app/controllers/RecipesController.php**
   - Reescrito método `update()` para permitir edición de ingredientes

5. **app/controllers/FinancialController.php**
   - Agregado método `recentMovements()`
   - Agregado método `categories()`
   - Agregado método `createCategory()`
   - Agregado método `getCategory()`
   - Agregado método `updateCategory()`
   - Agregado método `toggleCategory()`
   - Modificado `transactions()` para incluir categorías
   - Modificado `createTransaction()` para usar `categoria_id`

6. **app/views/attendance/projections.php**
   - Corrección de warnings deprecated

7. **app/views/settings/ingredients.php**
   - Campo proveedor cambiado a dropdown
   - JavaScript actualizado para manejar `proveedor_id`

8. **app/views/recipes/create.php**
   - Agregada sección de ingredientes dinámicos
   - JavaScript para gestión de ingredientes
   - Validación de mínimo 2 ingredientes

9. **app/views/recipes/edit.php**
   - Ingredientes ahora editables
   - JavaScript para agregar/eliminar/editar ingredientes

10. **app/views/financial/index.php**
    - Agregados 2 nuevos botones de acceso rápido

11. **app/views/financial/transactions.php**
    - Campo categoría cambiado a dropdown
    - JavaScript para filtrar categorías por tipo
    - Link al catálogo de categorías

12. **public/index.php**
    - Agregadas rutas para movimientos recientes
    - Agregadas rutas para categorías

### Archivos Nuevos

1. **app/views/financial/recent_movements.php**
   - Vista completa para movimientos recientes

2. **app/views/financial/categories.php**
   - Vista completa para gestión de categorías
   - Modal para crear/editar categorías
   - JavaScript para operaciones AJAX

3. **sql/update_improvements.sql**
   - Script completo de actualización de BD

---

## 11. Pruebas Recomendadas

### 11.1 Ingredientes
1. ✓ Crear nuevo ingrediente con proveedor seleccionado
2. ✓ Editar ingrediente existente y cambiar proveedor
3. ✓ Verificar que dropdown muestra solo proveedores activos

### 11.2 Recetas
1. ✓ Crear nueva receta con 2 ingredientes (mínimo)
2. ✓ Intentar crear receta con 1 ingrediente (debe fallar)
3. ✓ Editar receta existente y modificar ingredientes
4. ✓ Agregar ingredientes nuevos a receta existente
5. ✓ Eliminar ingredientes de receta existente

### 11.3 Módulo Financiero
1. ✓ Acceder a "Movimientos Recientes" desde dashboard
2. ✓ Verificar que muestra últimos 30 días
3. ✓ Acceder a "Categorías" desde dashboard
4. ✓ Crear nueva categoría de ingreso
5. ✓ Crear nueva categoría de egreso
6. ✓ Editar categoría existente
7. ✓ Desactivar/activar categoría
8. ✓ Crear nueva transacción y seleccionar categoría
9. ✓ Verificar que categorías se filtran por tipo

### 11.4 Errores SQL
1. ✓ Navegar a historial de asistencia con paginación
2. ✓ Navegar a órdenes de producción con paginación
3. ✓ Verificar que no hay errores LIMIT/OFFSET

### 11.5 Logo
1. ✓ Subir nuevo logo desde configuración
2. ✓ Verificar que la ruta se guarda correctamente
3. ✓ Verificar que el logo se muestra correctamente

---

## 12. Compatibilidad

- **PHP:** 7.4+, 8.0+, 8.1+
- **MySQL:** 5.7+, 8.0+
- **Navegadores:** Chrome, Firefox, Safari, Edge (últimas versiones)

---

## 13. Notas Importantes

1. **Backup de Base de Datos:** Antes de ejecutar el script SQL, realizar backup completo de la base de datos.

2. **Migración de Datos:** El script migra automáticamente:
   - Proveedores desde campo texto a tabla
   - Categorías desde campo texto a tabla
   - Mantiene compatibilidad con datos existentes

3. **Columnas Antiguas:** El script NO elimina las columnas `proveedor` y `categoria` antiguas. Se recomienda mantenerlas por un periodo de transición.

4. **Permisos:** Los nuevos métodos de categorías requieren rol `admin` o `coordinador`.

5. **Validaciones:** Se agregaron validaciones tanto en frontend (JavaScript) como backend (PHP).

---

## 14. Próximas Mejoras Recomendadas

1. **Ingredientes:**
   - Agregar búsqueda/filtrado de ingredientes
   - Implementar importación masiva de ingredientes

2. **Recetas:**
   - Agregar cálculo automático de costos por receta
   - Implementar plantillas de recetas

3. **Financiero:**
   - Dashboard de análisis financiero con gráficas
   - Exportación de reportes a Excel/PDF
   - Presupuestos por categoría

4. **General:**
   - Implementar sistema de notificaciones
   - Agregar auditoría de cambios
   - Mejorar sistema de permisos por módulo

---

## 15. Soporte y Contacto

Para reportar bugs o solicitar nuevas funcionalidades, crear un issue en el repositorio de GitHub.

**Versión del documento:** 1.0  
**Última actualización:** 15 de noviembre de 2025
