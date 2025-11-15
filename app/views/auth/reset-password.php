<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 px-4 py-12">
    <div class="max-w-md w-full">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-blue-600 rounded-full mb-4">
                <i class="fas fa-lock text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                Nueva Contraseña
            </h1>
            <p class="text-gray-600">
                Ingrese su nueva contraseña
            </p>
        </div>
        
        <!-- Reset Password Card -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo Router::url('/reset-password'); ?>" class="space-y-6">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
                
                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1"></i> Nueva Contraseña
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        minlength="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Mínimo 6 caracteres"
                    >
                </div>
                
                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-1"></i> Confirmar Contraseña
                    </label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                        minlength="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Repita la contraseña"
                    >
                </div>
                
                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105"
                >
                    <i class="fas fa-check mr-2"></i> Restablecer Contraseña
                </button>
                
                <!-- Back to Login Link -->
                <div class="text-center mt-4">
                    <a href="<?php echo Router::url('/login'); ?>" class="text-sm text-gray-600 hover:text-gray-800 hover:underline">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al inicio de sesión
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>&copy; <?php echo date('Y'); ?> Sistema de Comedores Industriales</p>
            <p class="mt-1">Estado de Querétaro, México</p>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
