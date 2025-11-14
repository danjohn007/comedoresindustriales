<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-cog mr-2"></i> Configuración del Sistema
        </h1>
        <p class="text-gray-600">Administre las configuraciones globales del sistema</p>
    </div>
    
    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="<?php echo Router::url('/settings/update'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <?php
            $currentCategory = '';
            foreach ($settings as $setting):
                if ($currentCategory !== $setting['categoria']):
                    if ($currentCategory !== ''): ?>
                        </div>
                    <?php endif;
                    $currentCategory = $setting['categoria'];
                    ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">
                            <?php echo ucfirst(htmlspecialchars($setting['categoria'])); ?>
                        </h3>
                <?php endif; ?>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo htmlspecialchars($setting['nombre']); ?>
                        <?php if ($setting['descripcion']): ?>
                            <span class="text-xs text-gray-500 font-normal">
                                - <?php echo htmlspecialchars($setting['descripcion']); ?>
                            </span>
                        <?php endif; ?>
                    </label>
                    
                    <?php if (in_array($setting['tipo_dato'], ['text', 'number', 'email'])): ?>
                        <input 
                            type="<?php echo $setting['tipo_dato']; ?>" 
                            name="<?php echo htmlspecialchars($setting['clave']); ?>"
                            value="<?php echo htmlspecialchars($setting['valor']); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                    <?php elseif ($setting['tipo_dato'] === 'boolean'): ?>
                        <select 
                            name="<?php echo htmlspecialchars($setting['clave']); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="1" <?php echo $setting['valor'] == '1' ? 'selected' : ''; ?>>Activado</option>
                            <option value="0" <?php echo $setting['valor'] == '0' ? 'selected' : ''; ?>>Desactivado</option>
                        </select>
                    <?php elseif ($setting['tipo_dato'] === 'textarea'): ?>
                        <textarea 
                            name="<?php echo htmlspecialchars($setting['clave']); ?>"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        ><?php echo htmlspecialchars($setting['valor']); ?></textarea>
                    <?php else: ?>
                        <input 
                            type="text" 
                            name="<?php echo htmlspecialchars($setting['clave']); ?>"
                            value="<?php echo htmlspecialchars($setting['valor']); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if ($currentCategory !== ''): ?>
                </div>
            <?php endif; ?>
            
            <div class="mt-6 flex justify-end space-x-4">
                <a href="<?php echo Router::url('/dashboard'); ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                    <i class="fas fa-save mr-2"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
    
    <!-- Quick Links -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="<?php echo Router::url('/settings/users'); ?>" class="block bg-white rounded-lg shadow p-4 hover:shadow-lg transition border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Usuarios</h3>
                    <p class="text-sm text-gray-600">Gestionar usuarios del sistema</p>
                </div>
            </div>
        </a>
        
        <a href="<?php echo Router::url('/settings/comedores'); ?>" class="block bg-white rounded-lg shadow p-4 hover:shadow-lg transition border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <i class="fas fa-building text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Comedores</h3>
                    <p class="text-sm text-gray-600">Gestionar comedores</p>
                </div>
            </div>
        </a>
        
        <a href="<?php echo Router::url('/settings/ingredients'); ?>" class="block bg-white rounded-lg shadow p-4 hover:shadow-lg transition border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-full p-3 mr-4">
                    <i class="fas fa-carrot text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Ingredientes</h3>
                    <p class="text-sm text-gray-600">Catálogo de ingredientes</p>
                </div>
            </div>
        </a>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
