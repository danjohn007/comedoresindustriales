<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-chart-line mr-2"></i> Ejecución Presupuestal
                </h1>
                <p class="text-gray-600">Comparativo entre presupuesto asignado y ejecutado</p>
            </div>
            <a href="<?php echo Router::url('/financial'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                    <select name="anio" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo $filters['anio'] == $y ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mes (Opcional)</label>
                    <select name="mes" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Todos los meses</option>
                        <?php 
                        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                        for ($m = 1; $m <= 12; $m++): 
                        ?>
                            <option value="<?php echo $m; ?>" <?php echo $filters['mes'] == $m ? 'selected' : ''; ?>>
                                <?php echo $meses[$m-1]; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-filter mr-2"></i> Filtrar
                </button>
            </form>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Presupuesto Asignado</p>
                    <p class="text-2xl font-bold text-blue-600">$<?php echo number_format($totals['presupuesto_asignado'], 2); ?></p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-wallet text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Presupuesto Gastado</p>
                    <p class="text-2xl font-bold text-red-600">$<?php echo number_format($totals['presupuesto_gastado'], 2); ?></p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Presupuesto Disponible</p>
                    <p class="text-2xl font-bold text-green-600">$<?php echo number_format($totals['presupuesto_disponible'], 2); ?></p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-piggy-bank text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Budget Execution Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comedor</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Período</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Asignado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gastado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Disponible</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ejecución</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($budgetData)): ?>
                    <?php foreach ($budgetData as $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($row['comedor']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                            <?php 
                            $meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                            echo $meses[$row['mes']] . ' ' . $row['anio']; 
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-600 font-medium">
                            $<?php echo number_format($row['presupuesto_asignado'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 font-medium">
                            $<?php echo number_format($row['presupuesto_gastado'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 font-medium">
                            $<?php echo number_format($row['presupuesto_disponible'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <?php 
                                    $porcentaje = min($row['porcentaje_ejecutado'], 100);
                                    $color = $porcentaje > 100 ? 'bg-red-600' : ($porcentaje >= 90 ? 'bg-yellow-500' : 'bg-blue-600');
                                    ?>
                                    <div class="<?php echo $color; ?> h-full rounded-full" style="width: <?php echo $porcentaje; ?>%"></div>
                                </div>
                                <span class="font-medium"><?php echo number_format($row['porcentaje_ejecutado'], 1); ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <?php
                            $estado = $row['estado'];
                            if ($estado === 'excedido') {
                                echo '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 font-bold">Excedido</span>';
                            } elseif ($estado === 'cerrado' || $row['porcentaje_ejecutado'] >= 95) {
                                echo '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-bold">Crítico</span>';
                            } else {
                                echo '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activo</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                            <p class="text-lg">No hay datos de presupuesto para el período seleccionado</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Mostrando página <?php echo $pagination['current_page']; ?> de <?php echo $pagination['total_pages']; ?>
                (<?php echo $pagination['total_records']; ?> registros en total)
            </div>
            <div class="flex space-x-2">
                <?php if ($pagination['current_page'] > 1): ?>
                    <a href="?anio=<?php echo $filters['anio']; ?>&mes=<?php echo $filters['mes']; ?>&page=<?php echo $pagination['current_page'] - 1; ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-chevron-left mr-2"></i> Anterior
                    </a>
                <?php endif; ?>
                
                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <a href="?anio=<?php echo $filters['anio']; ?>&mes=<?php echo $filters['mes']; ?>&page=<?php echo $pagination['current_page'] + 1; ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        Siguiente <i class="fas fa-chevron-right ml-2"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
