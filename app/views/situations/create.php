<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="<?php echo Router::url('/situations'); ?>" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i> Volver a Situaciones
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-exclamation-triangle mr-2"></i> Registrar Situación Atípica
            </h2>
            
            <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <p class="text-red-700"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo Router::url('/situations/create'); ?>" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <!-- Comedor -->
                <div>
                    <label for="comedor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-1"></i> Comedor *
                    </label>
                    <select id="comedor_id" name="comedor_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Seleccione un comedor</option>
                        <?php foreach ($comedores as $comedor): ?>
                        <option value="<?php echo $comedor['id']; ?>"><?php echo htmlspecialchars($comedor['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Tipo -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-1"></i> Tipo de Situación *
                    </label>
                    <select id="tipo" name="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Seleccione un tipo</option>
                        <option value="contratacion">Contratación</option>
                        <option value="despido">Despido</option>
                        <option value="incapacidad">Incapacidad</option>
                        <option value="evento_especial">Evento Especial</option>
                        <option value="dia_festivo">Día Festivo</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                
                <!-- Fechas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-1"></i> Fecha Inicio *
                        </label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-check mr-1"></i> Fecha Fin
                        </label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Dejar vacío si es indefinido</p>
                    </div>
                </div>
                
                <!-- Impacto -->
                <div>
                    <label for="impacto_comensales" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-chart-line mr-1"></i> Impacto en Comensales *
                    </label>
                    <input 
                        type="number" 
                        id="impacto_comensales" 
                        name="impacto_comensales" 
                        required
                        placeholder="Número positivo (incremento) o negativo (reducción)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        Ejemplo: +50 para contratación, -30 para día festivo
                    </p>
                </div>
                
                <!-- Turnos Afectados -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock mr-1"></i> Turnos Afectados
                    </label>
                    <div class="space-y-2">
                        <?php foreach ($turnos as $turno): ?>
                        <label class="inline-flex items-center mr-6">
                            <input type="checkbox" name="turnos_afectados[]" value="<?php echo htmlspecialchars($turno['nombre']); ?>" class="rounded">
                            <span class="ml-2 text-gray-700"><?php echo htmlspecialchars($turno['nombre']); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Dejar sin selección si afecta todos los turnos</p>
                </div>
                
                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment mr-1"></i> Descripción *
                    </label>
                    <textarea 
                        id="descripcion" 
                        name="descripcion" 
                        rows="4"
                        required
                        placeholder="Describa detalladamente la situación y su justificación..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    ></textarea>
                </div>
                
                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                        <i class="fas fa-save mr-2"></i> Guardar Situación
                    </button>
                    <a href="<?php echo Router::url('/situations'); ?>" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
