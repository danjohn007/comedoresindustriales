<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-users mr-2"></i> Gestión de Asistencia
        </h1>
        <p class="text-gray-600">Control de asistencia y proyecciones de comensales</p>
    </div>
    
    <!-- Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="<?php echo Router::url('/attendance/record'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-4 mr-4">
                    <i class="fas fa-clipboard-check text-blue-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Registrar Asistencia</h3>
                    <p class="text-sm text-gray-600">Capturar asistencia del día</p>
                </div>
            </div>
        </a>
        
        <a href="<?php echo Router::url('/attendance/history'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-4 mr-4">
                    <i class="fas fa-history text-green-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Historial</h3>
                    <p class="text-sm text-gray-600">Ver registros históricos</p>
                </div>
            </div>
        </a>
        
        <a href="<?php echo Router::url('/attendance/projections'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-4 mr-4">
                    <i class="fas fa-chart-line text-purple-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Proyecciones</h3>
                    <p class="text-sm text-gray-600">Calcular proyecciones futuras</p>
                </div>
            </div>
        </a>
    </div>
    
    <!-- Quick Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Comedores -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-building mr-2"></i> Comedores Activos
            </h3>
            <div class="space-y-2">
                <?php foreach ($comedores as $comedor): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <div>
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($comedor['nombre']); ?></p>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($comedor['ubicacion']); ?></p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                        Cap: <?php echo $comedor['capacidad_total']; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Turnos -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-clock mr-2"></i> Turnos Configurados
            </h3>
            <div class="space-y-2">
                <?php foreach ($turnos as $turno): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <div>
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($turno['nombre']); ?></p>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($turno['descripcion']); ?></p>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                        <?php echo substr($turno['hora_inicio'], 0, 5); ?> - <?php echo substr($turno['hora_fin'], 0, 5); ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
