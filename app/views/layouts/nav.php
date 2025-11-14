<nav class="bg-white shadow-lg border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <a href="<?php echo Router::url('/dashboard'); ?>" class="text-2xl font-bold text-blue-700">
                    <i class="fas fa-utensils"></i> Comedores
                </a>
            </div>
            
            <div class="hidden md:flex space-x-6">
                <a href="<?php echo Router::url('/dashboard'); ?>" class="text-gray-700 hover:text-blue-600 transition flex items-center">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
                
                <a href="<?php echo Router::url('/attendance'); ?>" class="text-gray-700 hover:text-blue-600 transition flex items-center">
                    <i class="fas fa-users mr-2"></i> Asistencia
                </a>
                
                <a href="<?php echo Router::url('/situations'); ?>" class="text-gray-700 hover:text-blue-600 transition flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Situaciones
                </a>
                
                <a href="<?php echo Router::url('/production'); ?>" class="text-gray-700 hover:text-blue-600 transition flex items-center">
                    <i class="fas fa-clipboard-list mr-2"></i> Producción
                </a>
                
                <a href="<?php echo Router::url('/recipes'); ?>" class="text-gray-700 hover:text-blue-600 transition flex items-center">
                    <i class="fas fa-book mr-2"></i> Recetas
                </a>
                
                <a href="<?php echo Router::url('/reports'); ?>" class="text-gray-700 hover:text-blue-600 transition flex items-center">
                    <i class="fas fa-chart-bar mr-2"></i> Reportes
                </a>
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="<?php echo Router::url('/settings'); ?>" class="text-gray-700 hover:text-blue-600 transition flex items-center">
                    <i class="fas fa-cog mr-2"></i> Configuración
                </a>
                <?php endif; ?>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="hidden md:block">
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-user-circle mr-1"></i>
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
                    </span>
                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                        <?php echo strtoupper($_SESSION['user_role'] ?? 'user'); ?>
                    </span>
                </div>
                
                <a href="<?php echo Router::url('/logout'); ?>" class="text-red-600 hover:text-red-700 transition">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </div>
        </div>
        
        <!-- Mobile menu button -->
        <div class="md:hidden">
            <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden pb-4">
        <a href="<?php echo Router::url('/dashboard'); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-home mr-2"></i> Dashboard
        </a>
        <a href="<?php echo Router::url('/attendance'); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-users mr-2"></i> Asistencia
        </a>
        <a href="<?php echo Router::url('/situations'); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-exclamation-triangle mr-2"></i> Situaciones
        </a>
        <a href="<?php echo Router::url('/production'); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-clipboard-list mr-2"></i> Producción
        </a>
        <a href="<?php echo Router::url('/recipes'); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-book mr-2"></i> Recetas
        </a>
        <a href="<?php echo Router::url('/reports'); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
            <i class="fas fa-chart-bar mr-2"></i> Reportes
        </a>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
