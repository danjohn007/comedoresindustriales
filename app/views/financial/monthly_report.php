<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-chart-bar mr-2"></i> Reporte Mensual
            </h1>
            <p class="text-gray-600">Resumen de ingresos, egresos y balance mensual por comedor</p>
        </div>
        
        <a href="<?php echo Router::url('/financial/reports'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="<?php echo Router::url('/financial/monthly-report'); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                <select name="anio" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo ($filters['anio'] == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
                <select name="mes" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <?php 
                    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    foreach ($meses as $idx => $mes): 
                    ?>
                        <option value="<?php echo $idx + 1; ?>" <?php echo ($filters['mes'] == $idx + 1) ? 'selected' : ''; ?>><?php echo $mes; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Comedor</label>
                <select name="comedor_id" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los comedores</option>
                    <?php foreach ($comedores as $comedor): ?>
                        <option value="<?php echo $comedor['id']; ?>" <?php echo ($filters['comedor_id'] == $comedor['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($comedor['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                    <i class="fas fa-filter mr-2"></i> Aplicar Filtros
                </button>
            </div>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium">Total Ingresos</p>
                    <p class="text-2xl font-bold text-green-700 mt-2">
                        $<?php echo number_format($totals['ingresos'], 2); ?>
                    </p>
                </div>
                <i class="fas fa-arrow-up text-green-600 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-600 font-medium">Total Egresos</p>
                    <p class="text-2xl font-bold text-red-700 mt-2">
                        $<?php echo number_format($totals['egresos'], 2); ?>
                    </p>
                </div>
                <i class="fas fa-arrow-down text-red-600 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Balance</p>
                    <p class="text-2xl font-bold <?php echo $totals['balance'] >= 0 ? 'text-blue-700' : 'text-red-700'; ?> mt-2">
                        $<?php echo number_format($totals['balance'], 2); ?>
                    </p>
                </div>
                <i class="fas fa-balance-scale text-blue-600 text-3xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Report Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Detalle por Comedor</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comedor</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Egresos</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Presupuesto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">% Ejecutado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($reportData)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>No hay datos disponibles para el período seleccionado</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reportData as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['comedor']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo $row['total_transacciones']; ?> transacciones</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-green-600 font-medium">
                                    $<?php echo number_format($row['total_ingresos'] ?? 0, 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-red-600 font-medium">
                                    $<?php echo number_format($row['total_egresos'] ?? 0, 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium <?php echo ($row['balance'] ?? 0) >= 0 ? 'text-blue-600' : 'text-red-600'; ?>">
                                    $<?php echo number_format($row['balance'] ?? 0, 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700">
                                    <?php echo $row['presupuesto_asignado'] ? '$' . number_format($row['presupuesto_asignado'], 2) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <?php if ($row['porcentaje_ejecutado']): ?>
                                        <span class="<?php 
                                            if ($row['porcentaje_ejecutado'] > 100) echo 'text-red-600 font-bold';
                                            elseif ($row['porcentaje_ejecutado'] >= 90) echo 'text-yellow-600 font-medium';
                                            else echo 'text-gray-700';
                                        ?>">
                                            <?php echo number_format($row['porcentaje_ejecutado'], 1); ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php if ($row['estado_presupuesto']): ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?php 
                                            echo $row['estado_presupuesto'] === 'activo' ? 'bg-green-100 text-green-800' : 
                                                 ($row['estado_presupuesto'] === 'excedido' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');
                                        ?>">
                                            <?php echo ucfirst($row['estado_presupuesto']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Print/Export Actions -->
    <div class="mt-6 flex justify-end gap-4">
        <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-print mr-2"></i> Imprimir
        </button>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
