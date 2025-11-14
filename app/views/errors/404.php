<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="text-center">
            <div class="mb-8">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-9xl"></i>
            </div>
            
            <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Página no encontrada</h2>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                Lo sentimos, la página que estás buscando no existe o ha sido movida.
            </p>
            
            <a href="<?php echo BASE_URL; ?>/" class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-home mr-2"></i> Volver al inicio
            </a>
        </div>
    </div>
</body>
</html>
