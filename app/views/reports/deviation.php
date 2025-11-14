<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="<?php echo Router::url('/reports'); ?>" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Reportes
        </a>
    </div>
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-exclamation-circle mr-2"></i> Reporte de Desviaciones
        </h1>
        <p class="text-gray-600">Análisis de precisión de proyecciones - Últimos 30 días</p>
    </div>
    
    <!-- Alert Banner -->
    <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-yellow-600 text-xl mr-3"></i>
            <p class="text-sm text-yellow-800">
                Este reporte muestra las desviaciones mayores al 5% entre la proyección y la asistencia real de los últimos 30 días.
            </p>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <?php
    $totalDeviations = count($deviations);
    $avgDeviation = 0;
    $maxDeviation = 0;
    
    if ($totalDeviations > 0) {
        $sumDeviation = 0;
        foreach ($deviations as $dev) {
            $sumDeviation += $dev['desviacion'];
            if ($dev['desviacion'] > $maxDeviation) {
                $maxDeviation = $dev['desviacion'];
            }
        }
        $avgDeviation = $sumDeviation / $totalDeviations;
    }
    ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 mb-1">Total Desviaciones</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo number_format($totalDeviations); ?></p>
            <p class="text-xs text-gray-500 mt-1">registros con desviación > 5%</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <p class="text-sm text-gray-600 mb-1">Desviación Promedio</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo number_format($avgDeviation, 1); ?>%</p>
            <p class="text-xs text-gray-500 mt-1">promedio de desviación</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <p class="text-sm text-gray-600 mb-1">Desviación Máxima</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo number_format($maxDeviation, 1); ?>%</p>
            <p class="text-xs text-gray-500 mt-1">mayor desviación registrada</p>
        </div>
    </div>
    
    <!-- Deviations Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Detalle de Desviaciones</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Comedor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Turno</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Proyectados</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Reales</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">% Asistencia</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Desviación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deviations)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-check-circle text-4xl mb-2 text-green-500"></i>
                            <p>¡Excelente! No hay desviaciones significativas en los últimos 30 días</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($deviations as $dev): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo date('d/m/Y', strtotime($dev['fecha'])); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($dev['comedor']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($dev['turno']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($dev['comensales_proyectados']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($dev['comensales_reales']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <?php 
                                    $percentage = $dev['porcentaje_asistencia'];
                                    $colorClass = 'text-green-600 bg-green-100';
                                    if ($percentage < 90) {
                                        $colorClass = 'text-red-600 bg-red-100';
                                    } elseif ($percentage < 95) {
                                        $colorClass = 'text-yellow-600 bg-yellow-100';
                                    }
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $colorClass; ?>">
                                    <?php echo number_format($percentage, 1); ?>%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <?php 
                                    $desviacion = $dev['desviacion'];
                                    $deviationColor = 'text-yellow-600 bg-yellow-100';
                                    if ($desviacion > 15) {
                                        $deviationColor = 'text-red-600 bg-red-100';
                                    } elseif ($desviacion > 10) {
                                        $deviationColor = 'text-orange-600 bg-orange-100';
                                    }
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $deviationColor; ?>">
                                    <?php echo number_format($desviacion, 1); ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Export Options -->
    <div class="mt-6 flex justify-end space-x-4">
        <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg">
            <i class="fas fa-print mr-2"></i> Imprimir
        </button>
        <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
            <i class="fas fa-file-excel mr-2"></i> Exportar Excel
        </button>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
