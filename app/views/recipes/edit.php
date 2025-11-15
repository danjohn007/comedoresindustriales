<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="<?php echo Router::url('/recipes/view/' . $receta['id']); ?>" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i> Volver a Receta
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-edit mr-2"></i> Editar Receta
            </h2>
            
            <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <p class="text-red-700"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo Router::url('/recipes/update/' . $receta['id']); ?>" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <!-- Nombre -->
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de la Receta *
                    </label>
                    <input type="text" id="nombre" name="nombre" required
                           value="<?php echo htmlspecialchars($receta['nombre']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <!-- Línea de Servicio -->
                <div>
                    <label for="linea_servicio_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Línea de Servicio *
                    </label>
                    <select id="linea_servicio_id" name="linea_servicio_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccione...</option>
                        <?php foreach ($lineas as $linea): ?>
                        <option value="<?php echo $linea['id']; ?>" <?php echo $linea['id'] == $receta['linea_servicio_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($linea['nombre']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($receta['descripcion'] ?? ''); ?></textarea>
                </div>
                
                <!-- Porciones Base -->
                <div>
                    <label for="porciones_base" class="block text-sm font-medium text-gray-700 mb-2">
                        Porciones Base *
                    </label>
                    <input type="number" id="porciones_base" name="porciones_base" required
                           value="<?php echo htmlspecialchars($receta['porciones_base']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <!-- Tiempo de Preparación -->
                <div>
                    <label for="tiempo_preparacion" class="block text-sm font-medium text-gray-700 mb-2">
                        Tiempo de Preparación (minutos)
                    </label>
                    <input type="number" id="tiempo_preparacion" name="tiempo_preparacion"
                           value="<?php echo htmlspecialchars($receta['tiempo_preparacion'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <!-- Ingredientes Section -->
                <div class="pt-6 border-t">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-carrot mr-2"></i> Ingredientes
                            <span class="text-sm text-gray-500 font-normal">(<?php echo count($recetaIngredientes); ?> ingredientes)</span>
                        </h3>
                        <button type="button" onclick="addIngredient()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm">
                            <i class="fas fa-plus mr-2"></i> Agregar Ingrediente
                        </button>
                    </div>
                    
                    <div id="ingredientes-container" class="space-y-3">
                        <?php if (!empty($recetaIngredientes)): ?>
                            <?php foreach ($recetaIngredientes as $idx => $ing): ?>
                            <div class="flex gap-3 items-start p-3 bg-gray-50 rounded-lg" id="ingrediente-existing-<?php echo $ing['id']; ?>">
                                <input type="hidden" name="ingredientes_existentes[<?php echo $ing['id']; ?>][id]" value="<?php echo $ing['id']; ?>">
                                <div class="flex-1">
                                    <select name="ingredientes_existentes[<?php echo $ing['id']; ?>][ingrediente_id]" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <?php foreach ($ingredientes as $ingrediente): ?>
                                        <option value="<?php echo $ingrediente['id']; ?>" <?php echo $ingrediente['id'] == $ing['ingrediente_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ingrediente['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="w-32">
                                    <input type="number" name="ingredientes_existentes[<?php echo $ing['id']; ?>][cantidad]" 
                                           value="<?php echo $ing['cantidad']; ?>" placeholder="Cantidad" step="0.001" min="0" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="w-24">
                                    <select name="ingredientes_existentes[<?php echo $ing['id']; ?>][unidad]" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="kg" <?php echo $ing['unidad'] == 'kg' ? 'selected' : ''; ?>>kg</option>
                                        <option value="g" <?php echo $ing['unidad'] == 'g' ? 'selected' : ''; ?>>g</option>
                                        <option value="l" <?php echo $ing['unidad'] == 'l' ? 'selected' : ''; ?>>l</option>
                                        <option value="ml" <?php echo $ing['unidad'] == 'ml' ? 'selected' : ''; ?>>ml</option>
                                        <option value="pzas" <?php echo $ing['unidad'] == 'pzas' ? 'selected' : ''; ?>>pzas</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <input type="text" name="ingredientes_existentes[<?php echo $ing['id']; ?>][notas]" 
                                           value="<?php echo htmlspecialchars($ing['notas'] ?? ''); ?>" placeholder="Notas (opcional)"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <button type="button" onclick="removeExistingIngredient(<?php echo $ing['id']; ?>)" 
                                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="pt-6 border-t flex justify-end space-x-4">
                    <a href="<?php echo Router::url('/recipes/view/' . $receta['id']); ?>" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                        <i class="fas fa-save mr-2"></i> Actualizar Receta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let ingredienteCounter = 0;
    const ingredientes = <?php echo json_encode($ingredientes); ?>;
    
    function addIngredient() {
        const container = document.getElementById('ingredientes-container');
        const div = document.createElement('div');
        div.className = 'flex gap-3 items-start p-3 bg-gray-50 rounded-lg';
        div.id = `ingrediente-new-${ingredienteCounter}`;
        
        div.innerHTML = `
            <div class="flex-1">
                <select name="ingredientes_nuevos[${ingredienteCounter}][ingrediente_id]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccione ingrediente...</option>
                    ${ingredientes.map(ing => `<option value="${ing.id}">${ing.nombre}</option>`).join('')}
                </select>
            </div>
            <div class="w-32">
                <input type="number" name="ingredientes_nuevos[${ingredienteCounter}][cantidad]" 
                       placeholder="Cantidad" step="0.001" min="0" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="w-24">
                <select name="ingredientes_nuevos[${ingredienteCounter}][unidad]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="kg">kg</option>
                    <option value="g">g</option>
                    <option value="l">l</option>
                    <option value="ml">ml</option>
                    <option value="pzas">pzas</option>
                </select>
            </div>
            <div class="flex-1">
                <input type="text" name="ingredientes_nuevos[${ingredienteCounter}][notas]" 
                       placeholder="Notas (opcional)"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="button" onclick="removeNewIngredient(${ingredienteCounter})" 
                    class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        container.appendChild(div);
        ingredienteCounter++;
    }
    
    function removeNewIngredient(id) {
        const element = document.getElementById(`ingrediente-new-${id}`);
        if (element) {
            element.remove();
        }
    }
    
    function removeExistingIngredient(id) {
        const element = document.getElementById(`ingrediente-existing-${id}`);
        if (element && confirm('¿Está seguro de eliminar este ingrediente?')) {
            // Add hidden input to mark for deletion
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `ingredientes_eliminar[${id}]`;
            input.value = id;
            document.querySelector('form').appendChild(input);
            element.remove();
        }
    }
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
