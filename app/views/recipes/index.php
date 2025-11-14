<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-book mr-2"></i> Catálogo de Recetas
            </h1>
            <p class="text-gray-600">Recetas con gramajes (OPAD-025)</p>
        </div>
        
        <?php if (in_array($_SESSION['user_role'], ['admin', 'chef'])): ?>
        <a href="<?php echo Router::url('/recipes/create'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Nueva Receta
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Recipes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($recetas as $receta): ?>
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <?php echo htmlspecialchars($receta['nombre']); ?>
                    </h3>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        <?php echo htmlspecialchars($receta['linea_servicio']); ?>
                    </span>
                </div>
                
                <?php if ($receta['descripcion']): ?>
                <p class="text-sm text-gray-600 mb-3">
                    <?php echo htmlspecialchars(substr($receta['descripcion'], 0, 100)); ?>
                    <?php if (strlen($receta['descripcion']) > 100) echo '...'; ?>
                </p>
                <?php endif; ?>
                
                <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                    <span>
                        <i class="fas fa-users mr-1"></i>
                        <?php echo $receta['porciones_base']; ?> porciones
                    </span>
                    <span>
                        <i class="fas fa-carrot mr-1"></i>
                        <?php echo $receta['num_ingredientes']; ?> ingredientes
                    </span>
                </div>
                
                <a href="<?php echo Router::url('/recipes/view/' . $receta['id']); ?>" class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded transition">
                    <i class="fas fa-eye mr-2"></i> Ver Detalles
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($recetas)): ?>
    <div class="text-center py-12">
        <i class="fas fa-book text-gray-300 text-6xl mb-4"></i>
        <p class="text-gray-500 text-lg">No hay recetas registradas</p>
    </div>
    <?php endif; ?>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
