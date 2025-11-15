<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-chart-line mr-2"></i> Reportes Financieros
            </h1>
            <p class="text-gray-600">Análisis y reportes del módulo financiero</p>
        </div>
        
        <a href="<?php echo Router::url('/financial'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>
    
    <!-- Report Types -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <i class="fas fa-chart-bar text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Reporte Mensual</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Resumen de ingresos, egresos y balance mensual por comedor.
            </p>
            <a href="<?php echo Router::url('/financial/monthly-report'); ?>" class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-center">
                Generar Reporte
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-file-invoice-dollar text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Estado de Cuenta</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Detalle completo de todas las transacciones en un período específico.
            </p>
            <a href="<?php echo Router::url('/financial/account-statement'); ?>" class="block w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-center">
                Generar Reporte
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 p-3 rounded-full mr-4">
                    <i class="fas fa-chart-pie text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Análisis por Categoría</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Distribución de gastos e ingresos por categoría.
            </p>
            <a href="<?php echo Router::url('/financial/category-analysis'); ?>" class="block w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg text-center">
                Generar Reporte
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-yellow-100 p-3 rounded-full mr-4">
                    <i class="fas fa-balance-scale text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Ejecución Presupuestal</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Comparativo entre presupuesto asignado y ejecutado.
            </p>
            <a href="<?php echo Router::url('/financial/budget-execution'); ?>" class="block w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 rounded-lg text-center">
                Generar Reporte
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-red-100 p-3 rounded-full mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Alertas Presupuestales</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Comedores con presupuesto excedido o próximo a exceder.
            </p>
            <a href="<?php echo Router::url('/financial/budget-alerts'); ?>" class="block w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg text-center">
                Ver Alertas
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="bg-indigo-100 p-3 rounded-full mr-4">
                    <i class="fas fa-file-excel text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Exportar Datos</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Exportar datos financieros a Excel para análisis externo.
            </p>
            <a href="<?php echo Router::url('/financial/export-data'); ?>" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-center">
                Exportar
            </a>
        </div>
    </div>
    
    <!-- Info Notice -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-2xl mr-4 mt-1"></i>
            <div>
                <h4 class="font-semibold text-blue-900 mb-2">Módulo de Reportes</h4>
                <p class="text-sm text-blue-800">
                    Esta sección permite generar diversos reportes financieros para análisis y toma de decisiones.
                    Los reportes pueden ser filtrados por período, comedor y categoría según sea necesario.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
