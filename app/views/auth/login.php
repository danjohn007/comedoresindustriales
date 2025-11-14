<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 px-4 py-12">
    <div class="max-w-md w-full">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-blue-600 rounded-full mb-4">
                <i class="fas fa-utensils text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                Sistema de Gestión
            </h1>
            <p class="text-gray-600">
                Comedores Industriales - Querétaro
            </p>
        </div>
        
        <!-- Login Card -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">
                Iniciar Sesión
            </h2>
            
            <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo Router::url('/login'); ?>" class="space-y-6">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-1"></i> Usuario o Email
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Ingrese su usuario o email"
                    >
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1"></i> Contraseña
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Ingrese su contraseña"
                    >
                </div>
                
                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                </button>
            </form>
        </div>
        
        <!-- Demo Credentials Info -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="font-semibold text-blue-900 mb-2">
                <i class="fas fa-info-circle mr-1"></i> Credenciales de Prueba
            </h3>
            <div class="text-sm text-blue-800 space-y-1">
                <p><strong>Usuario:</strong> admin</p>
                <p><strong>Contraseña:</strong> admin123</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>&copy; <?php echo date('Y'); ?> Sistema de Comedores Industriales</p>
            <p class="mt-1">Estado de Querétaro, México</p>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
