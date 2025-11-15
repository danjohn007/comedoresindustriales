<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-carrot mr-2"></i> Catálogo de Ingredientes
            </h1>
            <p class="text-gray-600">Gestión de ingredientes para recetas</p>
        </div>
        
        <div class="flex space-x-2">
            <?php if (in_array($_SESSION['user_role'], ['admin', 'chef'])): ?>
            <button onclick="showAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Agregar Ingrediente
            </button>
            <?php endif; ?>
            <a href="<?php echo Router::url('/dashboard'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
            </a>
        </div>
    </div>
    
    <!-- Ingredients Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nombre
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Unidad de Medida
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Costo Unitario
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($ingredientes)): ?>
                    <?php foreach ($ingredientes as $ingrediente): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($ingrediente['nombre'] ?? ''); ?>
                            </div>
                            <?php if (!empty($ingrediente['descripcion'])): ?>
                            <div class="text-sm text-gray-500">
                                <?php echo htmlspecialchars($ingrediente['descripcion']); ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">
                                <?php echo htmlspecialchars($ingrediente['unidad_medida'] ?? ''); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">
                                $<?php echo number_format($ingrediente['costo_unitario'] ?? 0, 2); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($ingrediente['activo']): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewIngredient(<?php echo $ingrediente['id']; ?>)" class="text-blue-600 hover:text-blue-900 mr-3" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editIngredient(<?php echo $ingrediente['id']; ?>)" class="text-green-600 hover:text-green-900 mr-3" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="toggleIngredient(<?php echo $ingrediente['id']; ?>, <?php echo $ingrediente['activo'] ? 'false' : 'true'; ?>)" 
                                    class="text-yellow-600 hover:text-yellow-900 mr-3" title="<?php echo $ingrediente['activo'] ? 'Suspender' : 'Activar'; ?>">
                                <i class="fas fa-<?php echo $ingrediente['activo'] ? 'pause' : 'play'; ?>"></i>
                            </button>
                            <button onclick="deleteIngredient(<?php echo $ingrediente['id']; ?>)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <i class="fas fa-carrot text-gray-300 text-6xl mb-4"></i>
                            <p class="text-gray-500 text-lg">No hay ingredientes registrados</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="ingredientModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-2xl font-bold text-gray-800">Agregar Ingrediente</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="ingredientForm" onsubmit="return false;" class="space-y-4">
                <input type="hidden" id="ingrediente_id" name="id">
                
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Ingrediente *
                    </label>
                    <input type="text" id="nombre" name="nombre" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="unidad_medida" class="block text-sm font-medium text-gray-700 mb-2">
                            Unidad de Medida *
                        </label>
                        <select id="unidad_medida" name="unidad_medida" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccione...</option>
                            <option value="kg">Kilogramos (kg)</option>
                            <option value="g">Gramos (g)</option>
                            <option value="l">Litros (l)</option>
                            <option value="ml">Mililitros (ml)</option>
                            <option value="pzas">Piezas (pzas)</option>
                            <option value="lata">Lata</option>
                            <option value="caja">Caja</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="costo_unitario" class="block text-sm font-medium text-gray-700 mb-2">
                            Costo Unitario *
                        </label>
                        <input type="number" id="costo_unitario" name="costo_unitario" step="0.01" min="0" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-2">
                        Proveedor
                    </label>
                    <input type="text" id="proveedor" name="proveedor"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="pt-4 flex justify-end space-x-4">
                    <button type="button" onclick="closeModal()" 
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="button" onclick="saveIngredient()" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                        <i class="fas fa-save mr-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showAddModal() {
        document.getElementById('modalTitle').textContent = 'Agregar Ingrediente';
        document.getElementById('ingredientForm').reset();
        document.getElementById('ingrediente_id').value = '';
        document.getElementById('ingredientModal').classList.remove('hidden');
    }
    
    function viewIngredient(id) {
        alert('Funcionalidad de vista detallada en desarrollo. ID: ' + id);
    }
    
    function editIngredient(id) {
        // In a real implementation, this would fetch data via AJAX
        document.getElementById('modalTitle').textContent = 'Editar Ingrediente';
        document.getElementById('ingrediente_id').value = id;
        document.getElementById('ingredientModal').classList.remove('hidden');
        alert('Cargue los datos del ingrediente ID: ' + id + ' desde el servidor');
    }
    
    function saveIngredient() {
        const form = document.getElementById('ingredientForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // In a real implementation, this would send data via AJAX
        alert('Guardando ingrediente... (implementar llamada AJAX al servidor)');
        closeModal();
        location.reload();
    }
    
    function toggleIngredient(id, activate) {
        const action = activate ? 'activar' : 'suspender';
        if (confirm('¿Está seguro de ' + action + ' este ingrediente?')) {
            // In a real implementation, this would send request via AJAX
            alert('Ingrediente ' + action + 'do. (implementar llamada AJAX)');
            location.reload();
        }
    }
    
    function deleteIngredient(id) {
        if (confirm('¿Está seguro de eliminar este ingrediente?\nEsta acción no se puede deshacer.')) {
            // In a real implementation, this would send request via AJAX
            alert('Eliminando ingrediente ID: ' + id + ' (implementar llamada AJAX)');
            location.reload();
        }
    }
    
    function closeModal() {
        document.getElementById('ingredientModal').classList.add('hidden');
    }
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
