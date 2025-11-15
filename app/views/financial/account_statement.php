<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-file-invoice-dollar mr-2"></i> Estado de Cuenta
            </h1>
            <p class="text-gray-600">Detalle completo de todas las transacciones en un período específico</p>
        </div>
        
        <a href="<?php echo Router::url('/financial/reports'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="<?php echo Router::url('/financial/account-statement'); ?>" class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <select name="tipo" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="all" <?php echo ($filters['tipo'] == 'all') ? 'selected' : ''; ?>>Todos</option>
                    <option value="ingreso" <?php echo ($filters['tipo'] == 'ingreso') ? 'selected' : ''; ?>>Ingresos</option>
                    <option value="egreso" <?php echo ($filters['tipo'] == 'egreso') ? 'selected' : ''; ?>>Egresos</option>
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium">Ingresos</p>
                    <p class="text-2xl font-bold text-green-700 mt-2">
                        $<?php echo number_format($totals['ingresos'], 2); ?>
                    </p>
                </div>
                <i class="fas fa-arrow-circle-up text-green-600 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-600 font-medium">Egresos</p>
                    <p class="text-2xl font-bold text-red-700 mt-2">
                        $<?php echo number_format($totals['egresos'], 2); ?>
                    </p>
                </div>
                <i class="fas fa-arrow-circle-down text-red-600 text-3xl"></i>
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
                <i class="fas fa-calculator text-blue-600 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 font-medium">Transacciones</p>
                    <p class="text-2xl font-bold text-purple-700 mt-2">
                        <?php echo number_format($totals['total_transacciones']); ?>
                    </p>
                </div>
                <i class="fas fa-list text-purple-600 text-3xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Transacciones</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>No hay transacciones disponibles para el período seleccionado</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo date('d/m/Y', strtotime($tx['fecha_transaccion'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($tx['comedor']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($tx['concepto']); ?></div>
                                    <?php if ($tx['descripcion']): ?>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars(substr($tx['descripcion'], 0, 50)); ?><?php echo strlen($tx['descripcion']) > 50 ? '...' : ''; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo $tx['categoria'] ? htmlspecialchars($tx['categoria']) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full <?php echo $tx['tipo'] === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo ucfirst($tx['tipo']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium <?php echo $tx['tipo'] === 'ingreso' ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $tx['tipo'] === 'ingreso' ? '+' : '-'; ?>$<?php echo number_format($tx['monto'], 2); ?>
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
