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
        <form method="POST" action="<?php echo Router::url('/settings/update'); ?>" enctype="multipart/form-data">
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
                        <?php echo htmlspecialchars($setting['descripcion'] ?? $setting['clave'] ?? ''); ?>
                    </label>
                    
                    <?php if (in_array($setting['clave'], ['color_primario', 'color_secundario'])): ?>
                        <!-- Color picker field -->
                        <div class="flex items-center space-x-3">
                            <input 
                                type="color" 
                                id="<?php echo htmlspecialchars($setting['clave']); ?>_picker"
                                value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                class="h-10 w-20 rounded cursor-pointer"
                                onchange="document.getElementById('<?php echo htmlspecialchars($setting['clave']); ?>').value = this.value"
                            >
                            <input 
                                type="text" 
                                id="<?php echo htmlspecialchars($setting['clave']); ?>"
                                name="<?php echo htmlspecialchars($setting['clave']); ?>"
                                value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                placeholder="#000000"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                onchange="document.getElementById('<?php echo htmlspecialchars($setting['clave']); ?>_picker').value = this.value"
                            >
                        </div>
                    <?php elseif ($setting['clave'] === 'logo_sistema'): ?>
                        <!-- File upload field for logo -->
                        <div class="space-y-2">
                            <?php if (!empty($setting['valor'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo htmlspecialchars($setting['valor']); ?>" alt="Logo actual" class="h-16 border rounded">
                            </div>
                            <?php endif; ?>
                            <input 
                                type="file" 
                                id="logo_file"
                                name="logo_file"
                                accept="image/*"
                                class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100"
                            >
                            <input type="hidden" name="<?php echo htmlspecialchars($setting['clave']); ?>" value="<?php echo htmlspecialchars($setting['valor']); ?>">
                            <p class="text-xs text-gray-500">Formatos soportados: JPG, PNG, SVG (máx. 2MB)</p>
                        </div>
                    <?php elseif (in_array($setting['tipo_dato'], ['text', 'number', 'email'])): ?>
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
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
