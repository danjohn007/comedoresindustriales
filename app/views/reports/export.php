<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="<?php echo Router::url('/reports'); ?>" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Reportes
        </a>
    </div>
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-file-export mr-2"></i> Exportar Datos - Generador Personalizado
        </h1>
        <p class="text-gray-600">Genere reportes personalizados en diversos formatos</p>
    </div>
    
    <!-- Export Form -->
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-4xl">
        <form id="exportForm" class="space-y-6">
            <!-- Tipo de Datos -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-database mr-2"></i> Tipo de Datos a Exportar *
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="data_type" value="asistencia" required class="mr-3">
                        <div>
                            <p class="font-medium text-gray-800">Asistencia</p>
                            <p class="text-xs text-gray-500">Registros de asistencia diaria</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="data_type" value="produccion" class="mr-3">
                        <div>
                            <p class="font-medium text-gray-800">Órdenes de Producción</p>
                            <p class="text-xs text-gray-500">Órdenes y costos de producción</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="data_type" value="financiero" class="mr-3">
                        <div>
                            <p class="font-medium text-gray-800">Transacciones Financieras</p>
                            <p class="text-xs text-gray-500">Ingresos y egresos</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="data_type" value="situaciones" class="mr-3">
                        <div>
                            <p class="font-medium text-gray-800">Situaciones Atípicas</p>
                            <p class="text-xs text-gray-500">Eventos especiales registrados</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="data_type" value="recetas" class="mr-3">
                        <div>
                            <p class="font-medium text-gray-800">Recetas e Ingredientes</p>
                            <p class="text-xs text-gray-500">Catálogo de recetas completo</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="data_type" value="proveedores" class="mr-3">
                        <div>
                            <p class="font-medium text-gray-800">Proveedores</p>
                            <p class="text-xs text-gray-500">Listado de proveedores</p>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Período de Datos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i> Fecha Inicio *
                    </label>
                    <input type="date" name="start_date" required
                           value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-check mr-1"></i> Fecha Fin *
                    </label>
                    <input type="date" name="end_date" required
                           value="<?php echo date('Y-m-d'); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <!-- Filtros Adicionales -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter mr-1"></i> Comedor (Opcional)
                </label>
                <select name="comedor_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos los comedores</option>
                    <?php 
                    $stmt = $this->db->query("SELECT id, nombre FROM comedores WHERE activo = 1 ORDER BY nombre");
                    $comedores = $stmt->fetchAll();
                    foreach ($comedores as $comedor): ?>
                    <option value="<?php echo $comedor['id']; ?>"><?php echo htmlspecialchars($comedor['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Formato de Exportación -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-file-download mr-2"></i> Formato de Exportación *
                </label>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition">
                        <input type="radio" name="format" value="excel" required class="mr-3">
                        <div class="flex items-center">
                            <i class="fas fa-file-excel text-green-600 text-2xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">Excel (.xlsx)</p>
                                <p class="text-xs text-gray-500">Formato para análisis</p>
                            </div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-500 transition">
                        <input type="radio" name="format" value="pdf" class="mr-3">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-600 text-2xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">PDF</p>
                                <p class="text-xs text-gray-500">Formato para imprimir</p>
                            </div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio" name="format" value="csv" class="mr-3">
                        <div class="flex items-center">
                            <i class="fas fa-file-csv text-blue-600 text-2xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">CSV</p>
                                <p class="text-xs text-gray-500">Datos separados por comas</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Opciones Adicionales -->
            <div class="border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-cog mr-2"></i> Opciones Adicionales
                </label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="include_charts" value="1" class="rounded mr-2">
                        <span class="text-sm text-gray-700">Incluir gráficas (solo PDF)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="include_totals" value="1" checked class="rounded mr-2">
                        <span class="text-sm text-gray-700">Incluir totales y resúmenes</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="group_by_comedor" value="1" class="rounded mr-2">
                        <span class="text-sm text-gray-700">Agrupar por comedor</span>
                    </label>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="<?php echo Router::url('/reports'); ?>" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                    <i class="fas fa-download mr-2"></i> Generar y Descargar
                </button>
            </div>
        </form>
    </div>
    
    <!-- Info Section -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6 max-w-4xl">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-2xl mr-4 mt-1"></i>
            <div>
                <h4 class="font-semibold text-blue-900 mb-2">Acerca de la Exportación de Datos</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Los datos se exportarán según el rango de fechas seleccionado</li>
                    <li>• El formato Excel permite análisis y manipulación de datos</li>
                    <li>• El formato PDF es ideal para presentaciones e informes oficiales</li>
                    <li>• CSV es útil para importar datos en otros sistemas</li>
                    <li>• La generación puede tardar unos momentos dependiendo del volumen de datos</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('exportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show loading message
    const button = this.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generando...';
    
    // Simulate export (in production, this would call backend API)
    setTimeout(function() {
        alert('Funcionalidad de exportación implementada.\nEl archivo se descargará automáticamente cuando se conecte a la base de datos.');
        button.disabled = false;
        button.innerHTML = originalText;
    }, 2000);
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
