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
            <i class="fas fa-dollar-sign mr-2"></i> Reporte de Costos
        </h1>
        <p class="text-gray-600">Análisis de costos de producción por período</p>
    </div>
    
    <!-- Info Banner -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
            <div>
                <p class="text-sm text-blue-800 font-semibold mb-1">Funcionalidad en Desarrollo</p>
                <p class="text-sm text-blue-700">
                    Este módulo está siendo implementado. Pronto podrá visualizar análisis detallados de costos por comedor, receta e ingrediente.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Coming Soon Features -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-chart-pie mr-2"></i> Costos por Comedor
            </h3>
            <p class="text-gray-600 text-sm mb-4">
                Análisis comparativo de costos entre diferentes comedores, incluyendo tendencias y promedios históricos.
            </p>
            <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                Próximamente
            </span>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-utensils mr-2"></i> Costos por Receta
            </h3>
            <p class="text-gray-600 text-sm mb-4">
                Desglose de costos por receta, incluyendo costo por porción y análisis de ingredientes más costosos.
            </p>
            <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                Próximamente
            </span>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-carrot mr-2"></i> Costos de Ingredientes
            </h3>
            <p class="text-gray-600 text-sm mb-4">
                Seguimiento de costos de ingredientes a lo largo del tiempo, alertas de variaciones y optimización de compras.
            </p>
            <span class="inline-block px-3 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                Próximamente
            </span>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-calendar-alt mr-2"></i> Proyecciones de Costos
            </h3>
            <p class="text-gray-600 text-sm mb-4">
                Estimaciones y proyecciones de costos futuros basados en tendencias históricas y programación de menús.
            </p>
            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                Próximamente
            </span>
        </div>
    </div>
    
    <!-- Sample Stats (Placeholder) -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line mr-2"></i> Vista Previa de Funcionalidades
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Costo Promedio Mensual</p>
                <p class="text-2xl font-bold text-gray-400">---</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Costo por Comensal</p>
                <p class="text-2xl font-bold text-gray-400">---</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Tendencia del Mes</p>
                <p class="text-2xl font-bold text-gray-400">---</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Ahorro Potencial</p>
                <p class="text-2xl font-bold text-gray-400">---</p>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-4">
            <p class="text-center text-gray-500 text-sm">
                <i class="fas fa-wrench mr-2"></i>
                Esta funcionalidad estará disponible en una próxima actualización del sistema.
            </p>
        </div>
    </div>
    
    <!-- Temporary Data Table -->
    <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Datos Preliminares</h3>
        </div>
        
        <div class="p-8 text-center">
            <i class="fas fa-database text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500 mb-2">No hay datos de costos disponibles todavía</p>
            <p class="text-sm text-gray-400">
                Configure las recetas e ingredientes para comenzar a generar reportes de costos automáticamente.
            </p>
            <div class="mt-6">
                <a href="<?php echo Router::url('/recipes'); ?>" class="inline-block px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg mr-3">
                    <i class="fas fa-utensils mr-2"></i> Ver Recetas
                </a>
                <a href="<?php echo Router::url('/settings/ingredients'); ?>" class="inline-block px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
                    <i class="fas fa-carrot mr-2"></i> Ver Ingredientes
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
