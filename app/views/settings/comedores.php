<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-building mr-2"></i> Gestión de Comedores
            </h1>
            <p class="text-gray-600">Administrar comedores del sistema</p>
        </div>
        
        <a href="<?php echo Router::url('/dashboard'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
        </a>
    </div>
    
    <!-- Comedores Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (!empty($comedores)): ?>
            <?php foreach ($comedores as $comedor): ?>
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            <?php echo htmlspecialchars($comedor['nombre'] ?? ''); ?>
                        </h3>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <?php echo htmlspecialchars($comedor['ubicacion'] ?? ''); ?>
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fas fa-city mr-1"></i>
                            <?php echo htmlspecialchars($comedor['ciudad'] ?? ''); ?>, <?php echo htmlspecialchars($comedor['estado'] ?? ''); ?>
                        </p>
                    </div>
                    <?php if ($comedor['activo']): ?>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Activo
                        </span>
                    <?php else: ?>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Inactivo
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="border-t pt-4 mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-users mr-1"></i> Capacidad
                        </span>
                        <span class="text-sm font-semibold text-gray-800">
                            <?php echo number_format($comedor['capacidad_total'] ?? 0); ?> comensales
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="text-sm text-gray-600 block mb-1">
                            <i class="fas fa-clock mr-1"></i> Turnos Activos
                        </span>
                        <div class="flex flex-wrap gap-1">
                            <?php 
                            $turnos = explode(',', $comedor['turnos_activos'] ?? '');
                            foreach ($turnos as $turno):
                            ?>
                            <span class="px-2 py-1 text-xs rounded bg-blue-50 text-blue-700">
                                <?php echo htmlspecialchars(trim($turno)); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2 mt-4">
                    <button class="text-blue-600 hover:text-blue-900 px-3 py-1 text-sm" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="text-green-600 hover:text-green-900 px-3 py-1 text-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="text-red-600 hover:text-red-900 px-3 py-1 text-sm" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <i class="fas fa-building text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No hay comedores registrados</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
