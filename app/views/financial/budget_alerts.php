<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i> Alertas Presupuestales
            </h1>
            <p class="text-gray-600">Comedores con presupuesto excedido o próximo a exceder (≥90%)</p>
        </div>
        <a href="<?php echo Router::url('/financial'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>
    
    <?php if (!empty($alerts)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Atención:</strong> Se encontraron <?php echo count($alerts); ?> presupuestos que requieren atención.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Alerts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comedor</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Período</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Asignado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gastado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Disponible</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">% Ejecutado</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nivel de Alerta</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($alerts)): ?>
                    <?php foreach ($alerts as $alert): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <i class="fas fa-utensils mr-2 text-gray-400"></i>
                            <?php echo htmlspecialchars($alert['comedor']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                            <?php 
                            $meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                            echo $meses[$alert['mes']] . ' ' . $alert['anio']; 
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-600 font-medium">
                            $<?php echo number_format($alert['presupuesto_asignado'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 font-medium">
                            $<?php echo number_format($alert['presupuesto_gastado'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                            <?php 
                            $disponible = $alert['presupuesto_disponible'];
                            $color = $disponible < 0 ? 'text-red-600' : 'text-green-600';
                            ?>
                            <span class="<?php echo $color; ?>">
                                $<?php echo number_format(abs($disponible), 2); ?>
                                <?php if ($disponible < 0): ?>
                                    <i class="fas fa-arrow-down ml-1"></i>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <?php 
                                    $porcentaje = min($alert['porcentaje_ejecutado'], 100);
                                    $color = $alert['porcentaje_ejecutado'] > 100 ? 'bg-red-600' : 'bg-yellow-500';
                                    ?>
                                    <div class="<?php echo $color; ?> h-full rounded-full" style="width: <?php echo $porcentaje; ?>%"></div>
                                </div>
                                <span class="font-bold <?php echo $alert['porcentaje_ejecutado'] > 100 ? 'text-red-600' : 'text-yellow-600'; ?>">
                                    <?php echo number_format($alert['porcentaje_ejecutado'], 1); ?>%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <?php
                            if ($alert['porcentaje_ejecutado'] > 100 || $alert['estado'] === 'excedido') {
                                echo '<span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800 font-bold">';
                                echo '<i class="fas fa-times-circle mr-1"></i>EXCEDIDO';
                                echo '</span>';
                            } elseif ($alert['porcentaje_ejecutado'] >= 95) {
                                echo '<span class="px-3 py-1 text-xs rounded-full bg-orange-100 text-orange-800 font-bold">';
                                echo '<i class="fas fa-exclamation-circle mr-1"></i>CRÍTICO';
                                echo '</span>';
                            } else {
                                echo '<span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-bold">';
                                echo '<i class="fas fa-exclamation-triangle mr-1"></i>ADVERTENCIA';
                                echo '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-check-circle text-green-300 text-6xl mb-4"></i>
                            <p class="text-lg">No hay alertas presupuestales en este momento</p>
                            <p class="text-sm text-gray-400 mt-2">Todos los presupuestos están dentro de los límites normales</p>
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
                (<?php echo $pagination['total_records']; ?> alertas en total)
            </div>
            <div class="flex space-x-2">
                <?php if ($pagination['current_page'] > 1): ?>
                    <a href="?page=<?php echo $pagination['current_page'] - 1; ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-chevron-left mr-2"></i> Anterior
                    </a>
                <?php endif; ?>
                
                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <a href="?page=<?php echo $pagination['current_page'] + 1; ?>" 
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
