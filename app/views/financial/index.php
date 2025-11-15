<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-dollar-sign mr-2"></i> Módulo Financiero
        </h1>
        <p class="text-gray-600">Gestión financiera y presupuestos</p>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ingresos del Mes</p>
                    <p class="text-2xl font-bold text-green-600">
                        $<?php echo number_format($summary['total_ingresos'] ?? 0, 2); ?>
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-arrow-up text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Egresos del Mes</p>
                    <p class="text-2xl font-bold text-red-600">
                        $<?php echo number_format($summary['total_egresos'] ?? 0, 2); ?>
                    </p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-arrow-down text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Balance</p>
                    <?php 
                    $balance = ($summary['total_ingresos'] ?? 0) - ($summary['total_egresos'] ?? 0);
                    $balanceColor = $balance >= 0 ? 'text-blue-600' : 'text-red-600';
                    ?>
                    <p class="text-2xl font-bold <?php echo $balanceColor; ?>">
                        $<?php echo number_format($balance, 2); ?>
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-balance-scale text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <a href="<?php echo Router::url('/financial/transactions'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-exchange-alt text-3xl mb-3"></i>
            <p class="font-semibold">Transacciones</p>
        </a>
        
        <a href="<?php echo Router::url('/financial/budgets'); ?>" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-wallet text-3xl mb-3"></i>
            <p class="font-semibold">Presupuestos</p>
        </a>
        
        <a href="<?php echo Router::url('/financial/reports'); ?>" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-chart-line text-3xl mb-3"></i>
            <p class="font-semibold">Reportes</p>
        </a>
        
        <a href="<?php echo Router::url('/dashboard'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white rounded-lg p-6 text-center transition">
            <i class="fas fa-arrow-left text-3xl mb-3"></i>
            <p class="font-semibold">Dashboard</p>
        </a>
    </div>
    
    <!-- Budget Status -->
    <?php if (!empty($budgets)): ?>
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-chart-pie mr-2"></i> Estado de Presupuestos - <?php echo date('F Y'); ?>
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <?php foreach ($budgets as $budget): ?>
                <?php 
                $porcentaje = $budget['porcentaje_ejecutado'] ?? 0;
                $colorClass = 'bg-green-500';
                if ($porcentaje > 90) $colorClass = 'bg-red-500';
                elseif ($porcentaje > 75) $colorClass = 'bg-yellow-500';
                ?>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">
                            <?php echo htmlspecialchars($budget['comedor_nombre']); ?>
                        </span>
                        <span class="text-sm font-medium text-gray-700">
                            $<?php echo number_format($budget['presupuesto_gastado'], 2); ?> / 
                            $<?php echo number_format($budget['presupuesto_asignado'], 2); ?>
                            (<?php echo number_format($porcentaje, 1); ?>%)
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="<?php echo $colorClass; ?> h-2.5 rounded-full" style="width: <?php echo min($porcentaje, 100); ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-history mr-2"></i> Transacciones Recientes
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($recentTransactions)): ?>
                        <?php foreach ($recentTransactions as $trans): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo date('d/m/Y', strtotime($trans['fecha_transaccion'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($trans['comedor_nombre']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo htmlspecialchars($trans['concepto']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($trans['tipo'] === 'ingreso'): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Ingreso</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Egreso</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                                <?php 
                                $color = $trans['tipo'] === 'ingreso' ? 'text-green-600' : 'text-red-600';
                                echo "<span class='{$color}'>$" . number_format($trans['monto'], 2) . "</span>";
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No hay transacciones recientes
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
