<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-clock mr-2"></i> Movimientos Recientes
            </h1>
            <p class="text-gray-600">Transacciones de los últimos 30 días</p>
        </div>
        <a href="<?php echo Router::url('/financial'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>
    
    <!-- Movements Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($movements)): ?>
                    <?php foreach ($movements as $mov): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo date('d/m/Y', strtotime($mov['fecha_transaccion'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($mov['comedor_nombre'] ?? 'N/A'); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?php echo htmlspecialchars($mov['concepto']); ?>
                            <?php if (!empty($mov['descripcion'])): ?>
                                <br><span class="text-xs text-gray-500"><?php echo htmlspecialchars($mov['descripcion']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php echo htmlspecialchars($mov['categoria_nombre'] ?? $mov['categoria'] ?? 'Sin categoría'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if ($mov['tipo'] === 'ingreso'): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Ingreso</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Egreso</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                            <?php 
                            $color = $mov['tipo'] === 'ingreso' ? 'text-green-600' : 'text-red-600';
                            echo "<span class='{$color}'>$" . number_format($mov['monto'], 2) . "</span>";
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                            <p class="text-lg">No hay movimientos en los últimos 30 días</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
