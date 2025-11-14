<?php
/**
 * Database Connection Test and Base URL Verification
 * 
 * This file tests the database connection and displays the configured base URL
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - Sistema Comedores</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">
                    <svg class="inline-block w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Test de Conexión del Sistema
                </h1>
                
                <?php
                require_once __DIR__ . '/config/config.php';
                
                $tests = [];
                
                // Test 1: PHP Version
                $phpVersion = phpversion();
                $tests[] = [
                    'name' => 'Versión de PHP',
                    'status' => version_compare($phpVersion, '7.0.0', '>='),
                    'message' => "PHP $phpVersion " . (version_compare($phpVersion, '7.0.0', '>=') ? '(Correcto)' : '(Se requiere PHP 7.0+)'),
                    'details' => ''
                ];
                
                // Test 2: PDO MySQL Extension
                $pdoAvailable = extension_loaded('pdo_mysql');
                $tests[] = [
                    'name' => 'Extensión PDO MySQL',
                    'status' => $pdoAvailable,
                    'message' => $pdoAvailable ? 'Disponible' : 'No disponible (requerida)',
                    'details' => ''
                ];
                
                // Test 3: Base URL Configuration
                $tests[] = [
                    'name' => 'URL Base del Sistema',
                    'status' => true,
                    'message' => BASE_URL,
                    'details' => 'Esta es la URL base configurada automáticamente'
                ];
                
                // Test 4: Database Connection
                $dbStatus = false;
                $dbMessage = '';
                $dbDetails = '';
                
                try {
                    require_once __DIR__ . '/config/Database.php';
                    $db = Database::getInstance();
                    $conn = $db->getConnection();
                    
                    // Test query
                    $stmt = $conn->query("SELECT VERSION() as version");
                    $result = $stmt->fetch();
                    
                    $dbStatus = true;
                    $dbMessage = 'Conexión exitosa';
                    $dbDetails = "MySQL versión: " . $result['version'] . "<br>";
                    $dbDetails .= "Base de datos: " . DB_NAME;
                    
                    // Check if tables exist
                    $stmt = $conn->query("SHOW TABLES");
                    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    
                    if (count($tables) > 0) {
                        $dbDetails .= "<br>Tablas encontradas: " . count($tables);
                    } else {
                        $dbDetails .= "<br><strong>Advertencia:</strong> No se encontraron tablas. Ejecute el archivo sql/schema.sql";
                    }
                    
                } catch (Exception $e) {
                    $dbStatus = false;
                    $dbMessage = 'Error de conexión';
                    $dbDetails = $e->getMessage();
                }
                
                $tests[] = [
                    'name' => 'Conexión a Base de Datos',
                    'status' => $dbStatus,
                    'message' => $dbMessage,
                    'details' => $dbDetails
                ];
                
                // Test 5: File Permissions
                $writable = is_writable(__DIR__);
                $tests[] = [
                    'name' => 'Permisos de Escritura',
                    'status' => $writable,
                    'message' => $writable ? 'Directorio escribible' : 'Directorio no escribible',
                    'details' => 'Ruta: ' . __DIR__
                ];
                
                // Display results
                foreach ($tests as $test) {
                    $statusColor = $test['status'] ? 'green' : 'red';
                    $statusIcon = $test['status'] ? '✓' : '✗';
                    $bgColor = $test['status'] ? 'bg-green-50' : 'bg-red-50';
                    $borderColor = $test['status'] ? 'border-green-200' : 'border-red-200';
                    $textColor = $test['status'] ? 'text-green-800' : 'text-red-800';
                    
                    echo "<div class='mb-4 p-4 border-l-4 $bgColor $borderColor rounded'>";
                    echo "<div class='flex items-center mb-2'>";
                    echo "<span class='text-2xl mr-3' style='color: $statusColor'>$statusIcon</span>";
                    echo "<div class='flex-1'>";
                    echo "<h3 class='font-bold $textColor'>{$test['name']}</h3>";
                    echo "<p class='text-gray-700'>{$test['message']}</p>";
                    if (!empty($test['details'])) {
                        echo "<p class='text-sm text-gray-600 mt-1'>{$test['details']}</p>";
                    }
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                
                // Overall status
                $allPassed = array_reduce($tests, function($carry, $test) {
                    return $carry && $test['status'];
                }, true);
                
                if ($allPassed) {
                    echo "<div class='mt-6 p-4 bg-green-100 border-l-4 border-green-500 rounded'>";
                    echo "<p class='text-green-800 font-bold'>✓ Todos los tests pasaron correctamente</p>";
                    echo "<p class='text-green-700 mt-2'>El sistema está listo para usar.</p>";
                    echo "<a href='".BASE_URL."/' class='inline-block mt-4 px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition'>Ir al Sistema</a>";
                    echo "</div>";
                } else {
                    echo "<div class='mt-6 p-4 bg-red-100 border-l-4 border-red-500 rounded'>";
                    echo "<p class='text-red-800 font-bold'>✗ Algunos tests fallaron</p>";
                    echo "<p class='text-red-700 mt-2'>Por favor, revise los errores anteriores antes de continuar.</p>";
                    echo "</div>";
                }
                ?>
                
                <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                    <h3 class="font-bold text-blue-800">Información del Sistema</h3>
                    <ul class="text-sm text-blue-700 mt-2 space-y-1">
                        <li><strong>Nombre:</strong> <?php echo APP_NAME; ?></li>
                        <li><strong>Versión:</strong> <?php echo APP_VERSION; ?></li>
                        <li><strong>Zona Horaria:</strong> <?php echo date_default_timezone_get(); ?></li>
                        <li><strong>Hora del Servidor:</strong> <?php echo date('Y-m-d H:i:s'); ?></li>
                    </ul>
                </div>
                
                <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                    <h3 class="font-bold text-yellow-800">Instrucciones de Instalación</h3>
                    <ol class="text-sm text-yellow-700 mt-2 space-y-2 list-decimal list-inside">
                        <li>Edite <code class="bg-yellow-100 px-1 rounded">config/config.php</code> con sus credenciales de base de datos</li>
                        <li>Cree la base de datos ejecutando <code class="bg-yellow-100 px-1 rounded">sql/schema.sql</code></li>
                        <li>Cargue los datos de ejemplo con <code class="bg-yellow-100 px-1 rounded">sql/sample_data.sql</code></li>
                        <li>Acceda al sistema con usuario: <strong>admin</strong> y contraseña: <strong>admin123</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
