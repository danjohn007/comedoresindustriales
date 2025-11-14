<?php
/**
 * Route Testing Page
 * This page helps verify that URL routing is working correctly
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Rutas - Sistema Comedores</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-route mr-2"></i>
                    Test de Rutas del Sistema
                </h1>
                
                <?php
                require_once __DIR__ . '/../config/config.php';
                
                echo "<div class='mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded'>";
                echo "<h3 class='font-bold text-blue-800'>Configuración Actual</h3>";
                echo "<ul class='text-sm text-blue-700 mt-2 space-y-1'>";
                echo "<li><strong>BASE_URL:</strong> " . BASE_URL . "</li>";
                echo "<li><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</li>";
                echo "<li><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</li>";
                echo "<li><strong>HTTP_HOST:</strong> " . $_SERVER['HTTP_HOST'] . "</li>";
                echo "</ul>";
                echo "</div>";
                
                $routes = [
                    ['path' => '/', 'name' => 'Inicio / Redirect to Login', 'type' => 'GET'],
                    ['path' => '/login', 'name' => 'Página de Login', 'type' => 'GET'],
                    ['path' => '/dashboard', 'name' => 'Dashboard', 'type' => 'GET'],
                    ['path' => '/attendance', 'name' => 'Asistencia', 'type' => 'GET'],
                    ['path' => '/production', 'name' => 'Producción', 'type' => 'GET'],
                    ['path' => '/reports', 'name' => 'Reportes', 'type' => 'GET'],
                ];
                ?>
                
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Rutas Disponibles</h2>
                <p class="text-gray-600 mb-4">Haga clic en las siguientes rutas para verificar que funcionan correctamente:</p>
                
                <div class="space-y-3">
                    <?php foreach ($routes as $route): ?>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800"><?php echo $route['name']; ?></h3>
                                    <p class="text-sm text-gray-600">
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs mr-2">
                                            <?php echo $route['type']; ?>
                                        </span>
                                        <?php echo $route['path']; ?>
                                    </p>
                                </div>
                                <a href="<?php echo BASE_URL . $route['path']; ?>" 
                                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                    <i class="fas fa-arrow-right"></i> Probar
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                    <h3 class="font-bold text-green-800">Información</h3>
                    <p class="text-green-700 mt-2">
                        Si puede ver esta página, significa que el enrutamiento básico está funcionando. 
                        Las rutas protegidas (como Dashboard) redirigirán a la página de login si no está autenticado.
                    </p>
                </div>
                
                <div class="mt-4">
                    <a href="<?php echo BASE_URL; ?>/" class="inline-block px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                        <i class="fas fa-home mr-2"></i> Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
