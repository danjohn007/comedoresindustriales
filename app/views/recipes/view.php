<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="<?php echo Router::url('/recipes'); ?>" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Recetas
        </a>
    </div>
    
    <!-- Recipe Header -->
    <div class="bg-white rounded-lg shadow-lg mb-6">
        <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($receta['nombre']); ?></h2>
                    <p class="text-gray-600"><?php echo htmlspecialchars($receta['linea_servicio']); ?></p>
                </div>
                <?php if (in_array($_SESSION['user_role'], ['admin', 'chef'])): ?>
                <a href="<?php echo Router::url('/recipes/edit/' . $receta['id']); ?>" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="p-6">
            <?php if ($receta['descripcion']): ?>
            <p class="text-gray-700 mb-4"><?php echo nl2br(htmlspecialchars($receta['descripcion'])); ?></p>
            <?php endif; ?>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-600">Porciones Base:</p>
                    <p class="font-bold text-gray-800 text-lg"><?php echo $receta['porciones_base']; ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Tiempo Preparación:</p>
                    <p class="font-bold text-gray-800 text-lg"><?php echo $receta['tiempo_preparacion'] ?? '-'; ?> min</p>
                </div>
                <div>
                    <p class="text-gray-600">Ingredientes:</p>
                    <p class="font-bold text-gray-800 text-lg"><?php echo count($ingredientes); ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Estado:</p>
                    <p class="font-bold text-green-600 text-lg">
                        <?php echo $receta['activo'] ? 'Activo' : 'Inactivo'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ingredients Table (Gramajes OPAD-025) -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-carrot mr-2"></i> Gramajes por Ingrediente (OPAD-025)
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Ingrediente</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Cantidad</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Unidad</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Costo/Unidad</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Costo Total</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $costoTotal = 0;
                    foreach ($ingredientes as $ing): 
                        $costoIngrediente = $ing['cantidad'] * $ing['costo_unitario'];
                        $costoTotal += $costoIngrediente;
                    ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-800 font-semibold">
                            <?php echo htmlspecialchars($ing['ingrediente']); ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800 text-right">
                            <?php echo number_format($ing['cantidad'], 3); ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 text-center">
                            <?php echo htmlspecialchars($ing['unidad']); ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 text-right">
                            $<?php echo number_format($ing['costo_unitario'], 2); ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800 text-right font-semibold">
                            $<?php echo number_format($costoIngrediente, 2); ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <?php echo htmlspecialchars($ing['notas'] ?? '-'); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="4" class="px-4 py-3 text-right text-gray-800">
                            COSTO TOTAL (<?php echo $receta['porciones_base']; ?> porciones):
                        </td>
                        <td class="px-4 py-3 text-right text-green-700 text-lg">
                            $<?php echo number_format($costoTotal, 2); ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            $<?php echo number_format($costoTotal / $receta['porciones_base'], 2); ?>/porción
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
