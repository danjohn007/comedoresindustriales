<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Logo/Brand -->
        <div class="flex items-center justify-between p-4 border-b">
            <a href="<?php echo Router::url('/dashboard'); ?>" class="flex items-center space-x-2">
                <i class="fas fa-utensils text-blue-600 text-2xl"></i>
                <span class="text-xl font-bold text-gray-800">Comedores</span>
            </a>
            <button id="sidebar-close" class="lg:hidden text-gray-600 hover:text-gray-800">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- User Info -->
        <div class="p-4 border-b bg-gray-50">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                    <i class="fas fa-user"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                        <?php echo ucfirst($_SESSION['user_role'] ?? 'user'); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto p-4">
            <div class="space-y-1">
                <a href="<?php echo Router::url('/dashboard'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                
                <a href="<?php echo Router::url('/attendance'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Asistencia</span>
                </a>
                
                <a href="<?php echo Router::url('/situations'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                    <i class="fas fa-exclamation-triangle w-5"></i>
                    <span class="ml-3">Situaciones</span>
                </a>
                
                <a href="<?php echo Router::url('/production'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                    <i class="fas fa-clipboard-list w-5"></i>
                    <span class="ml-3">Producción</span>
                </a>
                
                <a href="<?php echo Router::url('/recipes'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                    <i class="fas fa-book w-5"></i>
                    <span class="ml-3">Recetas</span>
                </a>
                
                <a href="<?php echo Router::url('/reports'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span class="ml-3">Reportes</span>
                </a>
                
                <!-- Accesos Directos -->
                <div class="pt-4 mt-4 border-t">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                        Accesos Directos
                    </p>
                    
                    <?php if (in_array($_SESSION['user_role'], ['admin'])): ?>
                    <a href="<?php echo Router::url('/settings/users'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-users-cog w-5"></i>
                        <span class="ml-3">Usuarios</span>
                    </a>
                    
                    <a href="<?php echo Router::url('/settings/comedores'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-building w-5"></i>
                        <span class="ml-3">Comedores</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['user_role'], ['admin', 'chef'])): ?>
                    <a href="<?php echo Router::url('/settings/ingredients'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-carrot w-5"></i>
                        <span class="ml-3">Ingredientes</span>
                    </a>
                    <?php endif; ?>
                </div>
                
                <!-- Admin Section -->
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <div class="pt-4 mt-4 border-t">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                        Administración
                    </p>
                    <a href="<?php echo Router::url('/settings'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-cog w-5"></i>
                        <span class="ml-3">Configuración</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </nav>
        
        <!-- Bottom Actions -->
        <div class="p-4 border-t">
            <a href="<?php echo Router::url('/profile'); ?>" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition mb-2">
                <i class="fas fa-user-circle w-5"></i>
                <span class="ml-3">Mi Perfil</span>
            </a>
            <a href="<?php echo Router::url('/logout'); ?>" class="flex items-center px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="ml-3">Cerrar Sesión</span>
            </a>
        </div>
    </div>
</aside>

<!-- Top Bar (Mobile) -->
<div class="lg:hidden fixed top-0 left-0 right-0 bg-white shadow-md z-20">
    <div class="flex items-center justify-between p-4">
        <button id="sidebar-toggle" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-bars text-2xl"></i>
        </button>
        <a href="<?php echo Router::url('/dashboard'); ?>" class="flex items-center space-x-2">
            <i class="fas fa-utensils text-blue-600 text-xl"></i>
            <span class="text-lg font-bold text-gray-800">Comedores</span>
        </a>
        <div class="w-8"></div> <!-- Spacer for alignment -->
    </div>
</div>

<!-- Main Content Wrapper -->
<div class="lg:ml-64 pt-16 lg:pt-0">

<script>
    // Sidebar toggle for mobile
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    
    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    sidebarToggle?.addEventListener('click', openSidebar);
    sidebarClose?.addEventListener('click', closeSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);
    
    // Close sidebar on window resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });
</script>
