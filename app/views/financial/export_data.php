<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-file-excel mr-2 text-green-600"></i> Exportar Datos Financieros
                    </h1>
                    <p class="text-gray-600">Exportar datos a formato Excel para análisis externo</p>
                </div>
                <a href="<?php echo Router::url('/financial'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="<?php echo Router::url('/financial/download-export'); ?>">
                <div class="space-y-6">
                    <!-- Export Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-list mr-2"></i> Tipo de Reporte
                        </label>
                        <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="transactions" <?php echo $filters['type'] === 'transactions' ? 'selected' : ''; ?>>
                                Transacciones Financieras
                            </option>
                            <option value="budgets" <?php echo $filters['type'] === 'budgets' ? 'selected' : ''; ?>>
                                Presupuestos
                            </option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500">
                            Selecciona el tipo de datos que deseas exportar
                        </p>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-2"></i> Fecha Inicio
                            </label>
                            <input type="date" name="start_date" required
                                   value="<?php echo $filters['start_date']; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-2"></i> Fecha Fin
                            </label>
                            <input type="date" name="end_date" required
                                   value="<?php echo $filters['end_date']; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <!-- Comedor Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-utensils mr-2"></i> Comedor (Opcional)
                        </label>
                        <select name="comedor_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos los comedores</option>
                            <?php foreach ($comedores as $comedor): ?>
                                <option value="<?php echo $comedor['id']; ?>">
                                    <?php echo htmlspecialchars($comedor['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-2 text-sm text-gray-500">
                            Deja en blanco para exportar datos de todos los comedores
                        </p>
                    </div>
                    
                    <!-- Info Box -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Información sobre la exportación</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>El archivo se descargará en formato Excel (.xls)</li>
                                        <li>Incluye todos los datos dentro del rango de fechas seleccionado</li>
                                        <li>Puedes abrir el archivo con Microsoft Excel, LibreOffice Calc u otras aplicaciones compatibles</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <button type="button" onclick="window.history.back()"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
                            <i class="fas fa-download mr-2"></i> Descargar Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Recent Exports Info (Optional) -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-history mr-2"></i> Consejos de uso
            </h3>
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                    <p>Utiliza filtros específicos por comedor para generar reportes individualizados</p>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                    <p>Exporta regularmente para mantener respaldos de tus datos financieros</p>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                    <p>Los archivos Excel son ideales para crear gráficos y análisis personalizados</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
