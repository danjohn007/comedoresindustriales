<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="<?php echo Router::url('/production'); ?>" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Órdenes
        </a>
    </div>
    
    <!-- Order Header -->
    <div class="bg-white rounded-lg shadow-lg mb-6">
        <div class="px-6 py-4 bg-blue-50 border-b border-blue-200 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($order['numero_orden']); ?></h2>
                <p class="text-gray-600"><?php echo htmlspecialchars($order['receta']); ?></p>
            </div>
            <div class="flex space-x-3">
                <a href="<?php echo Router::url('/production/print/' . $order['id']); ?>" target="_blank" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">
                    <i class="fas fa-print mr-2"></i> Imprimir
                </a>
                <?php if (in_array($_SESSION['user_role'], ['admin', 'coordinador', 'chef'])): ?>
                <a href="<?php echo Router::url('/production/edit/' . $order['id']); ?>" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Información General</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>Comedor:</strong> <?php echo htmlspecialchars($order['comedor']); ?></p>
                        <p><strong>Turno:</strong> <?php echo htmlspecialchars($order['turno']); ?></p>
                        <p><strong>Línea:</strong> <?php echo htmlspecialchars($order['linea_servicio']); ?></p>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Fechas y Cantidades</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>Fecha Servicio:</strong> <?php echo date('d/m/Y', strtotime($order['fecha_servicio'])); ?></p>
                        <p><strong>Comensales:</strong> <?php echo number_format($order['comensales_proyectados']); ?></p>
                        <p><strong>Porciones:</strong> <?php echo number_format($order['porciones_calcular']); ?></p>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Estado y Creación</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>Estado:</strong> 
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
                        </p>
                        <p><strong>Creado por:</strong> <?php echo htmlspecialchars($order['creado_por'] ?? 'Sistema'); ?></p>
                        <p><strong>Fecha creación:</strong> <?php echo date('d/m/Y H:i', strtotime($order['fecha_creacion'])); ?></p>
                    </div>
                </div>
            </div>
            
            <?php if ($order['observaciones']): ?>
            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                <p class="text-sm"><strong>Observaciones:</strong> <?php echo nl2br(htmlspecialchars($order['observaciones'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Ingredients Table -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-list-ul mr-2"></i> Ingredientes Requeridos
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Ingrediente</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Cantidad</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Unidad</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Costo Estimado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $costoTotal = 0;
                    foreach ($ingredientes as $ing): 
                        $costoTotal += $ing['costo_estimado'];
                    ?>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($ing['ingrediente']); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-800 text-right font-semibold">
                            <?php echo number_format($ing['cantidad_requerida'], 3); ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 text-center"><?php echo htmlspecialchars($ing['unidad']); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-800 text-right">
                            $<?php echo number_format($ing['costo_estimado'], 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="3" class="px-4 py-3 text-right text-gray-800">COSTO TOTAL:</td>
                        <td class="px-4 py-3 text-right text-green-700 text-lg">
                            $<?php echo number_format($costoTotal, 2); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
