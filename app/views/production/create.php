<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="<?php echo Router::url('/production'); ?>" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i> Volver a Producción
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-clipboard-list mr-2"></i> Nueva Orden de Producción
            </h2>
            
            <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <p class="text-red-700"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo Router::url('/production/create'); ?>" class="space-y-6" id="productionForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <!-- Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Comedor -->
                    <div>
                        <label for="comedor_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-1"></i> Comedor *
                        </label>
                        <select id="comedor_id" name="comedor_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Seleccione...</option>
                            <?php foreach ($comedores as $comedor): ?>
                            <option value="<?php echo $comedor['id']; ?>"><?php echo htmlspecialchars($comedor['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Turno -->
                    <div>
                        <label for="turno_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock mr-1"></i> Turno *
                        </label>
                        <select id="turno_id" name="turno_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Seleccione...</option>
                            <?php foreach ($turnos as $turno): ?>
                            <option value="<?php echo $turno['id']; ?>">
                                <?php echo htmlspecialchars($turno['nombre']); ?> 
                                (<?php echo substr($turno['hora_inicio'], 0, 5); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Fecha Servicio -->
                    <div>
                        <label for="fecha_servicio" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1"></i> Fecha de Servicio *
                        </label>
                        <input 
                            type="date" 
                            id="fecha_servicio" 
                            name="fecha_servicio" 
                            min="<?php echo date('Y-m-d'); ?>"
                            required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                        >
                    </div>
                    
                    <!-- Receta -->
                    <div>
                        <label for="receta_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-utensils mr-1"></i> Receta *
                        </label>
                        <select id="receta_id" name="receta_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Seleccione...</option>
                            <?php 
                            $currentLine = '';
                            foreach ($recetas as $receta): 
                                if ($currentLine !== $receta['linea_servicio']) {
                                    if ($currentLine !== '') echo '</optgroup>';
                                    echo '<optgroup label="' . htmlspecialchars($receta['linea_servicio']) . '">';
                                    $currentLine = $receta['linea_servicio'];
                                }
                            ?>
                            <option value="<?php echo $receta['id']; ?>">
                                <?php echo htmlspecialchars($receta['nombre']); ?> 
                                (<?php echo $receta['porciones_base']; ?> porciones base)
                            </option>
                            <?php endforeach; ?>
                            <?php if ($currentLine !== '') echo '</optgroup>'; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Comensales Proyectados -->
                <div>
                    <label for="comensales_proyectados" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users mr-1"></i> Comensales Proyectados *
                    </label>
                    <input 
                        type="number" 
                        id="comensales_proyectados" 
                        name="comensales_proyectados" 
                        min="1"
                        required 
                        placeholder="Número de comensales para calcular ingredientes"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        Tip: El sistema puede sugerir automáticamente basado en proyecciones existentes
                    </p>
                </div>
                
                <!-- Preview Button -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <button 
                        type="button" 
                        onclick="previewIngredients()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded"
                    >
                        <i class="fas fa-eye mr-2"></i> Vista Previa de Ingredientes
                    </button>
                    <span class="ml-4 text-sm text-gray-600">Verifique los ingredientes antes de crear la orden</span>
                </div>
                
                <!-- Preview Results -->
                <div id="ingredientsPreview" class="hidden">
                    <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Ingredientes Calculados:</h4>
                        <div id="ingredientsList" class="space-y-2"></div>
                        <div class="mt-4 pt-4 border-t border-gray-300">
                            <p class="text-lg font-bold text-gray-800">
                                Costo Total Estimado: <span id="totalCost" class="text-green-600">$0.00</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Observaciones -->
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment mr-1"></i> Observaciones
                    </label>
                    <textarea 
                        id="observaciones" 
                        name="observaciones" 
                        rows="3"
                        placeholder="Notas o instrucciones especiales (opcional)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    ></textarea>
                </div>
                
                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg">
                        <i class="fas fa-save mr-2"></i> Crear Orden de Producción
                    </button>
                    <a href="<?php echo Router::url('/production'); ?>" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewIngredients() {
    const recetaId = document.getElementById('receta_id').value;
    const porciones = document.getElementById('comensales_proyectados').value;
    
    if (!recetaId || !porciones) {
        alert('Por favor seleccione una receta e ingrese el número de comensales');
        return;
    }
    
    const formData = new FormData();
    formData.append('receta_id', recetaId);
    formData.append('porciones', porciones);
    
    fetch('<?php echo Router::url('/production/calculate-ingredients'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = '';
            data.ingredientes.forEach(ing => {
                html += `
                    <div class="flex justify-between items-center text-sm p-2 bg-white rounded">
                        <span class="font-semibold text-gray-800">${ing.nombre}</span>
                        <span class="text-gray-600">${ing.cantidad_requerida} ${ing.unidad}</span>
                        <span class="text-green-600">$${ing.costo_total.toFixed(2)}</span>
                    </div>
                `;
            });
            
            document.getElementById('ingredientsList').innerHTML = html;
            document.getElementById('totalCost').textContent = '$' + data.costo_total.toFixed(2);
            document.getElementById('ingredientsPreview').classList.remove('hidden');
        } else {
            alert('Error: ' + (data.error || 'No se pudo calcular'));
        }
    })
    .catch(error => {
        alert('Error al calcular ingredientes: ' + error);
    });
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
