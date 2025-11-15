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
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-carrot mr-2"></i> Ingredientes
                        <span class="text-sm text-gray-500 font-normal">(<?php echo count($recetaIngredientes); ?> ingredientes)</span>
                    </h3>
                    
                    <?php if (!empty($recetaIngredientes)): ?>
                    <div class="space-y-2 mb-4">
                        <?php foreach ($recetaIngredientes as $ing): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <span class="font-medium text-gray-800"><?php echo htmlspecialchars($ing['ingrediente_nombre']); ?></span>
                                <span class="text-sm text-gray-600 ml-2">
                                    <?php echo number_format($ing['cantidad'], 2); ?> <?php echo htmlspecialchars($ing['unidad']); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-gray-500 text-sm italic mb-4">No hay ingredientes asociados a esta receta</p>
                    <?php endif; ?>
                    
                    <p class="text-sm text-blue-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Para modificar ingredientes, contacte al administrador o use el módulo de gestión de ingredientes.
                    </p>
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

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
