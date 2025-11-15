<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-chart-bar mr-2"></i> Centro de Reportes
        </h1>
        <p class="text-gray-600">Análisis y reportes del sistema</p>
    </div>
    
    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Attendance Report -->
        <a href="<?php echo Router::url('/reports/attendance'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-blue-500">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 rounded-full p-4 mr-4">
                    <i class="fas fa-users text-blue-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Reporte de Asistencia</h3>
                    <p class="text-sm text-gray-600">Análisis de comensales por período</p>
                </div>
            </div>
            <p class="text-sm text-gray-600">
                Consulte estadísticas de asistencia, promedios por turno y comparativos históricos.
            </p>
        </a>
        
        <!-- Deviation Report -->
        <a href="<?php echo Router::url('/reports/deviation'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-yellow-500">
            <div class="flex items-center mb-4">
                <div class="bg-yellow-100 rounded-full p-4 mr-4">
                    <i class="fas fa-exclamation-circle text-yellow-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Desviaciones</h3>
                    <p class="text-sm text-gray-600">Análisis de precisión de proyecciones</p>
                </div>
            </div>
            <p class="text-sm text-gray-600">
                Identifique desviaciones significativas entre proyecciones y asistencia real.
            </p>
        </a>
        
        <!-- Production Report -->
        <a href="<?php echo Router::url('/reports/production'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-green-500">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 rounded-full p-4 mr-4">
                    <i class="fas fa-clipboard-list text-green-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Producción</h3>
                    <p class="text-sm text-gray-600">Órdenes y cumplimiento</p>
                </div>
            </div>
            <p class="text-sm text-gray-600">
                Revise el historial de órdenes de producción, estados y tiempos de ejecución.
            </p>
        </a>
        
        <!-- Cost Report -->
        <a href="<?php echo Router::url('/reports/costs'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-purple-500">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 rounded-full p-4 mr-4">
                    <i class="fas fa-dollar-sign text-purple-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Costos</h3>
                    <p class="text-sm text-gray-600">Análisis de costos de producción</p>
                </div>
            </div>
            <p class="text-sm text-gray-600">
                Consulte costos estimados por comedor, receta y período de tiempo.
            </p>
        </a>
        
        <!-- Custom Report Generator -->
        <a href="<?php echo Router::url('/reports/export-data'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-gray-500">
            <div class="flex items-center mb-4">
                <div class="bg-gray-100 rounded-full p-4 mr-4">
                    <i class="fas fa-file-export text-gray-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Exportar Datos</h3>
                    <p class="text-sm text-gray-600">Generador personalizado</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Exporte datos en formato Excel, PDF o CSV para análisis externos.
            </p>
            <span class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                <i class="fas fa-download mr-1"></i> Generar Exportación
            </span>
        </a>
        
        <!-- API Documentation -->
        <div class="block bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
            <div class="flex items-center mb-4">
                <div class="bg-indigo-100 rounded-full p-4 mr-4">
                    <i class="fas fa-code text-indigo-600 text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Documentación API</h3>
                    <p class="text-sm text-gray-600">Integraciones externas</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Consulte la documentación de endpoints disponibles para integraciones.
            </p>
            <a href="#api-docs" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                <i class="fas fa-book mr-1"></i> Ver Documentación
            </a>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
