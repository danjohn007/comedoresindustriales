<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-chart-pie mr-2"></i> Análisis por Categoría
            </h1>
            <p class="text-gray-600">Distribución de gastos e ingresos por categoría</p>
        </div>
        
        <a href="<?php echo Router::url('/financial/reports'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="<?php echo Router::url('/financial/category-analysis'); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="start_date" value="<?php echo $filters['start_date']; ?>" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="end_date" value="<?php echo $filters['end_date']; ?>" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
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
                <i class="fas fa-plus-circle text-green-600 text-3xl"></i>
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
                <i class="fas fa-minus-circle text-red-600 text-3xl"></i>
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
                <i class="fas fa-equals text-blue-600 text-3xl"></i>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Income Categories -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-green-50 border-b border-green-200">
                <h3 class="text-lg font-semibold text-green-800">
                    <i class="fas fa-arrow-up mr-2"></i> Categorías de Ingreso
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Transacciones</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($ingresos)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p class="text-sm">No hay datos de ingresos</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ingresos as $cat): ?>
                                <?php 
                                $total = $cat['total_monto'] ?? 0;
                                $porcentaje = ($totals['ingresos'] > 0) ? ($total / $totals['ingresos']) * 100 : 0;
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cat['categoria']); ?></div>
                                        <?php if ($cat['promedio_monto']): ?>
                                            <div class="text-xs text-gray-500">Promedio: $<?php echo number_format($cat['promedio_monto'], 2); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-700">
                                        <?php echo $cat['cantidad_transacciones']; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium text-green-600">
                                        $<?php echo number_format($total, 2); ?>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-700">
                                        <div class="flex items-center justify-end">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo min($porcentaje, 100); ?>%"></div>
                                            </div>
                                            <span class="font-medium"><?php echo number_format($porcentaje, 1); ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Expense Categories -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <h3 class="text-lg font-semibold text-red-800">
                    <i class="fas fa-arrow-down mr-2"></i> Categorías de Egreso
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Transacciones</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($egresos)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p class="text-sm">No hay datos de egresos</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($egresos as $cat): ?>
                                <?php 
                                $total = $cat['total_monto'] ?? 0;
                                $porcentaje = ($totals['egresos'] > 0) ? ($total / $totals['egresos']) * 100 : 0;
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cat['categoria']); ?></div>
                                        <?php if ($cat['promedio_monto']): ?>
                                            <div class="text-xs text-gray-500">Promedio: $<?php echo number_format($cat['promedio_monto'], 2); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-700">
                                        <?php echo $cat['cantidad_transacciones']; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium text-red-600">
                                        $<?php echo number_format($total, 2); ?>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-700">
                                        <div class="flex items-center justify-end">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-red-600 h-2 rounded-full" style="width: <?php echo min($porcentaje, 100); ?>%"></div>
                                            </div>
                                            <span class="font-medium"><?php echo number_format($porcentaje, 1); ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="mt-4 px-6 py-4 bg-white rounded-lg shadow flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Mostrando página <?php echo $pagination['current_page']; ?> de <?php echo $pagination['total_pages']; ?>
                (<?php echo $pagination['total_records']; ?> categorías en total)
            </div>
            <div class="flex space-x-2">
                <?php 
                $queryParams = [
                    'start_date' => $filters['start_date'],
                    'end_date' => $filters['end_date'],
                    'comedor_id' => $filters['comedor_id']
                ];
                ?>
                
                <?php if ($pagination['current_page'] > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($queryParams, ['page' => $pagination['current_page'] - 1])); ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-chevron-left mr-2"></i> Anterior
                    </a>
                <?php endif; ?>
                
                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <a href="?<?php echo http_build_query(array_merge($queryParams, ['page' => $pagination['current_page'] + 1])); ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        Siguiente <i class="fas fa-chevron-right ml-2"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Print/Export Actions -->
    <div class="mt-6 flex justify-end gap-4">
        <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-print mr-2"></i> Imprimir
        </button>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
