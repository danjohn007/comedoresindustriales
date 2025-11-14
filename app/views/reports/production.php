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
            <i class="fas fa-clipboard-list mr-2"></i> Reporte de Producción
        </h1>
        <p class="text-gray-600">Historial de órdenes de producción y cumplimiento</p>
    </div>
    
    <!-- Date Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="<?php echo Router::url('/reports/production'); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
    $totalOrdenes = count($productionData);
    $totalComensales = 0;
    $costoTotal = 0;
    $ordenesCompletadas = 0;
    
    foreach ($productionData as $row) {
        $totalComensales += $row['comensales_proyectados'];
        $costoTotal += $row['costo_total'];
        if ($row['estado'] === 'completada') {
            $ordenesCompletadas++;
        }
    }
    
    $costoPorComensal = $totalComensales > 0 ? ($costoTotal / $totalComensales) : 0;
    ?>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 mb-1">Total Órdenes</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo number_format($totalOrdenes); ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 mb-1">Completadas</p>
            <p class="text-3xl font-bold text-gray-800"><?php echo number_format($ordenesCompletadas); ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-600 mb-1">Costo Total</p>
            <p class="text-3xl font-bold text-gray-800">$<?php echo number_format($costoTotal, 2); ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <p class="text-sm text-gray-600 mb-1">Costo/Comensal</p>
            <p class="text-3xl font-bold text-gray-800">$<?php echo number_format($costoPorComensal, 2); ?></p>
        </div>
    </div>
    
    <!-- Production Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Detalle de Órdenes de Producción</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Número Orden</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha Servicio</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Comedor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Receta</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Comensales</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Costo Estimado</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productionData)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No hay órdenes de producción para el período seleccionado</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($productionData as $row): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800 font-medium">
                                <a href="<?php echo Router::url('/production/view/' . $row['numero_orden']); ?>" class="text-blue-600 hover:text-blue-700">
                                    <?php echo htmlspecialchars($row['numero_orden']); ?>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo date('d/m/Y', strtotime($row['fecha_servicio'])); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($row['comedor']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($row['receta']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($row['comensales_proyectados']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                $<?php echo number_format($row['costo_total'], 2); ?>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php 
                                    $estado = $row['estado'];
                                    $estadoClass = 'bg-yellow-100 text-yellow-800';
                                    if ($estado === 'completada') {
                                        $estadoClass = 'bg-green-100 text-green-800';
                                    } elseif ($estado === 'en_proceso') {
                                        $estadoClass = 'bg-blue-100 text-blue-800';
                                    } elseif ($estado === 'cancelada') {
                                        $estadoClass = 'bg-red-100 text-red-800';
                                    }
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $estadoClass; ?>">
                                    <?php echo strtoupper($estado); ?>
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
