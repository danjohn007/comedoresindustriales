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
            <i class="fas fa-users mr-2"></i> Reporte de Asistencia
        </h1>
        <p class="text-gray-600">Análisis de comensales por período</p>
    </div>
    
    <!-- Date Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="<?php echo Router::url('/reports/attendance'); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="start_date" value="<?php echo $startDate; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="end_date" value="<?php echo $endDate; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                    <i class="fas fa-search mr-2"></i> Generar Reporte
                </button>
            </div>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <?php 
    $totalProyectados = 0;
    $totalReales = 0;
    foreach ($reportData as $row) {
        $totalProyectados += $row['total_proyectados'];
        $totalReales += $row['total_reales'];
    }
    $promedioGlobal = $totalProyectados > 0 ? ($totalReales / $totalProyectados * 100) : 0;
    ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 mb-1">Total Proyectados</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo number_format($totalProyectados); ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 mb-1">Total Reales</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo number_format($totalReales); ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-600 mb-1">Promedio de Asistencia</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo number_format($promedioGlobal, 1); ?>%</p>
        </div>
    </div>
    
    <!-- Report Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Detalle por Comedor y Turno</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Comedor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Turno</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Registros</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total Proyectados</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total Reales</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Promedio Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reportData)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No hay datos disponibles para el período seleccionado</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($reportData as $row): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800 font-medium">
                                <?php echo htmlspecialchars($row['comedor']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($row['turno']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($row['total_registros']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($row['total_proyectados']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($row['total_reales']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <?php 
                                    $percentage = $row['promedio_asistencia'];
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
