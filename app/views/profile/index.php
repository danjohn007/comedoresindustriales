<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-circle mr-2"></i> Mi Perfil
            </h1>
            <p class="text-gray-600">Información de tu cuenta</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded">
            <p class="text-green-700"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Profile Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8">
                <div class="flex items-center">
                    <div class="relative">
                        <?php if (!empty($user['imagen_perfil']) && file_exists(PUBLIC_PATH . $user['imagen_perfil'])): ?>
                            <img src="<?php echo Router::url($user['imagen_perfil']); ?>" 
                                 alt="Foto de perfil" 
                                 class="h-24 w-24 rounded-full object-cover border-4 border-white">
                        <?php else: ?>
                            <div class="h-24 w-24 bg-white rounded-full flex items-center justify-center text-blue-600 text-4xl">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <button type="button" onclick="document.getElementById('image-upload-modal').classList.remove('hidden')"
                                class="absolute bottom-0 right-0 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition">
                            <i class="fas fa-camera text-blue-600"></i>
                        </button>
                    </div>
                    <div class="ml-6 text-white">
                        <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($user['nombre_completo']); ?></h2>
                        <p class="text-blue-100">@<?php echo htmlspecialchars($user['username']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-gray-900 font-medium">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>
                            <?php echo htmlspecialchars($user['email']); ?>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Rol</label>
                        <p class="text-gray-900 font-medium">
                            <i class="fas fa-user-tag mr-2 text-gray-400"></i>
                            <?php 
                            $roles = [
                                'admin' => 'Administrador',
                                'coordinador' => 'Coordinador',
                                'chef' => 'Chef',
                                'operativo' => 'Operativo'
                            ];
                            echo $roles[$user['rol']] ?? ucfirst($user['rol']);
                            ?>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                        <p class="text-gray-900 font-medium">
                            <?php if ($user['activo']): ?>
                                <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Activo
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Inactivo
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Miembro desde</label>
                        <p class="text-gray-900 font-medium">
                            <i class="fas fa-calendar mr-2 text-gray-400"></i>
                            <?php echo date('d/m/Y', strtotime($user['fecha_creacion'])); ?>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Último acceso</label>
                        <p class="text-gray-900 font-medium">
                            <i class="fas fa-clock mr-2 text-gray-400"></i>
                            <?php echo $user['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) : 'Nunca'; ?>
                        </p>
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t flex justify-end space-x-4">
                    <a href="<?php echo Router::url('/dashboard'); ?>" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
                    </a>
                    <a href="<?php echo Router::url('/profile/change-password'); ?>" 
                       class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                        <i class="fas fa-key mr-2"></i> Cambiar Contraseña
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Upload Modal -->
<div id="image-upload-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-image mr-2"></i> Actualizar Foto de Perfil
            </h3>
            <button type="button" onclick="document.getElementById('image-upload-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="<?php echo Router::url('/profile/upload-image'); ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="px-6 py-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Selecciona una imagen
                    </label>
                    <input type="file" name="imagen_perfil" accept="image/jpeg,image/jpg,image/png,image/gif" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="mt-2 text-xs text-gray-500">
                        Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB
                    </p>
                </div>
                
                <?php if (!empty($user['imagen_perfil'])): ?>
                <div class="mb-4">
                    <p class="text-sm text-gray-700 mb-2">Imagen actual:</p>
                    <img src="<?php echo Router::url($user['imagen_perfil']); ?>" 
                         alt="Imagen actual" 
                         class="h-32 w-32 rounded-lg object-cover border border-gray-300">
                </div>
                <?php endif; ?>
            </div>
            
            <div class="px-6 py-4 border-t flex justify-between">
                <?php if (!empty($user['imagen_perfil'])): ?>
                <button type="button" onclick="deleteProfileImage()" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">
                    <i class="fas fa-trash mr-2"></i> Eliminar Imagen
                </button>
                <?php else: ?>
                <div></div>
                <?php endif; ?>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="document.getElementById('image-upload-modal').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        <i class="fas fa-upload mr-2"></i> Subir Imagen
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function deleteProfileImage() {
    if (confirm('¿Estás seguro de que deseas eliminar tu imagen de perfil?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo Router::url('/profile/delete-image'); ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?php echo $csrf_token; ?>';
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
