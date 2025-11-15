<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="<?php echo Router::url('/production/view/' . $order['id']); ?>" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i> Volver a Orden
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-edit mr-2"></i> Editar Orden de Producción
            </h2>
            
            <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <p class="text-red-700"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo Router::url('/production/update/' . $order['id']); ?>" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <!-- Número de Orden (readonly) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Número de Orden
                    </label>
                    <input type="text" value="<?php echo htmlspecialchars($order['numero_orden']); ?>" readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                
                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado *
                    </label>
                    <select id="estado" name="estado" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="pendiente" <?php echo $order['estado'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="en_proceso" <?php echo $order['estado'] === 'en_proceso' ? 'selected' : ''; ?>>En Proceso</option>
                        <option value="completado" <?php echo $order['estado'] === 'completado' ? 'selected' : ''; ?>>Completado</option>
                        <option value="cancelado" <?php echo $order['estado'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                
                <!-- Observaciones -->
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones
                    </label>
                    <textarea id="observaciones" name="observaciones" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($order['observaciones'] ?? ''); ?></textarea>
                </div>
                
                <div class="pt-6 border-t flex justify-end space-x-4">
                    <a href="<?php echo Router::url('/production/view/' . $order['id']); ?>" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
