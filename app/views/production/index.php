<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-clipboard-list mr-2"></i> Órdenes de Producción
            </h1>
            <p class="text-gray-600">Gestión de órdenes basadas en proyecciones (OPAD-007)</p>
        </div>
        
        <?php if (in_array($_SESSION['user_role'], ['admin', 'coordinador', 'chef'])): ?>
        <a href="<?php echo Router::url('/production/create'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Nueva Orden
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="<?php echo Router::url('/production'); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="all" <?php echo $filters['status'] === 'all' ? 'selected' : ''; ?>>Todos</option>
                    <option value="pendiente" <?php echo $filters['status'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="en_proceso" <?php echo $filters['status'] === 'en_proceso' ? 'selected' : ''; ?>>En Proceso</option>
                    <option value="completado" <?php echo $filters['status'] === 'completado' ? 'selected' : ''; ?>>Completado</option>
                    <option value="cancelado" <?php echo $filters['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="start_date" value="<?php echo $filters['start_date']; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="end_date" value="<?php echo $filters['end_date']; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
                <a href="<?php echo Router::url('/production'); ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg" title="Restaurar filtros">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>
    
    <!-- Orders List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Número Orden</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha Servicio</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Comedor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Turno</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Receta</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Línea</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Porciones</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Estado</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron órdenes con los filtros seleccionados</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-semibold text-blue-600">
                                <a href="<?php echo Router::url('/production/view/' . $order['id']); ?>" class="hover:underline">
                                    <?php echo htmlspecialchars($order['numero_orden']); ?>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo date('d/m/Y', strtotime($order['fecha_servicio'])); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($order['comedor']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($order['turno']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($order['receta']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?php echo htmlspecialchars($order['linea_servicio']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($order['porciones_calcular']); ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php 
                                    $statusColors = [
                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                        'en_proceso' => 'bg-blue-100 text-blue-800',
                                        'completado' => 'bg-green-100 text-green-800',
                                        'cancelado' => 'bg-red-100 text-red-800'
                                    ];
                                    echo $statusColors[$order['estado']] ?? 'bg-gray-100 text-gray-800';
                                ?>">
                                    <?php echo strtoupper($order['estado']); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="<?php echo Router::url('/production/view/' . $order['id']); ?>" class="text-blue-600 hover:text-blue-700" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo Router::url('/production/print/' . $order['id']); ?>" class="text-green-600 hover:text-green-700" title="Imprimir" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Mostrando <?php echo count($orders); ?> de <?php echo $pagination['total_records']; ?> registros
            </div>
            <div class="flex space-x-1">
                <?php if ($pagination['current_page'] > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($filters, ['page' => $pagination['current_page'] - 1])); ?>" 
                   class="px-3 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                <a href="?<?php echo http_build_query(array_merge($filters, ['page' => $i])); ?>" 
                   class="px-3 py-1 <?php echo $i == $pagination['current_page'] ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 hover:bg-gray-50'; ?> rounded">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                <a href="?<?php echo http_build_query(array_merge($filters, ['page' => $pagination['current_page'] + 1])); ?>" 
                   class="px-3 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
