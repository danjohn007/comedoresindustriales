<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="<?php echo Router::url('/attendance'); ?>" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i> Volver a Asistencia
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-clipboard-check mr-2"></i> Registrar Asistencia
            </h2>
            
            <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                <p class="text-green-700"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <p class="text-red-700"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo Router::url('/attendance/record'); ?>" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <!-- Comedor -->
                <div>
                    <label for="comedor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-1"></i> Comedor *
                    </label>
                    <select 
                        id="comedor_id" 
                        name="comedor_id" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Seleccione un comedor</option>
                        <?php foreach ($comedores as $comedor): ?>
                        <option value="<?php echo $comedor['id']; ?>">
                            <?php echo htmlspecialchars($comedor['nombre']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Turno -->
                <div>
                    <label for="turno_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock mr-1"></i> Turno *
                    </label>
                    <select 
                        id="turno_id" 
                        name="turno_id" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Seleccione un turno</option>
                        <?php foreach ($turnos as $turno): ?>
                        <option value="<?php echo $turno['id']; ?>">
                            <?php echo htmlspecialchars($turno['nombre']); ?> 
                            (<?php echo substr($turno['hora_inicio'], 0, 5); ?> - <?php echo substr($turno['hora_fin'], 0, 5); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Fecha -->
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i> Fecha *
                    </label>
                    <input 
                        type="date" 
                        id="fecha" 
                        name="fecha" 
                        value="<?php echo date('Y-m-d'); ?>"
                        max="<?php echo date('Y-m-d'); ?>"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Comensales Reales -->
                <div>
                    <label for="comensales_reales" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users mr-1"></i> Comensales Reales *
                    </label>
                    <input 
                        type="number" 
                        id="comensales_reales" 
                        name="comensales_reales" 
                        min="0"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Ingrese el número de comensales reales"
                    >
                </div>
                
                <!-- Observaciones -->
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment mr-1"></i> Observaciones
                    </label>
                    <textarea 
                        id="observaciones" 
                        name="observaciones" 
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Ingrese observaciones adicionales (opcional)"
                    ></textarea>
                </div>
                
                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button 
                        type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition"
                    >
                        <i class="fas fa-save mr-2"></i> Guardar Registro
                    </button>
                    <a 
                        href="<?php echo Router::url('/attendance'); ?>"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg transition text-center"
                    >
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
