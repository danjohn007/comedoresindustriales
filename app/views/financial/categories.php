<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-tags mr-2"></i> Catálogo de Categorías Financieras
            </h1>
            <p class="text-gray-600">Gestión de categorías de ingresos y egresos</p>
        </div>
        
        <div class="flex space-x-2">
            <button onclick="showAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Agregar Categoría
            </button>
            <a href="<?php echo Router::url('/financial'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>
    
    <!-- Categories by Type -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Income Categories -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b bg-green-50">
                <h2 class="text-xl font-bold text-green-800">
                    <i class="fas fa-arrow-up mr-2"></i> Categorías de Ingresos
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <?php 
                    $ingresos = array_filter($categorias, function($cat) { return $cat['tipo'] === 'ingreso'; });
                    if (!empty($ingresos)): 
                    ?>
                        <?php foreach ($ingresos as $cat): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($cat['nombre']); ?></div>
                                <?php if (!empty($cat['descripcion'])): ?>
                                    <div class="text-sm text-gray-600"><?php echo htmlspecialchars($cat['descripcion']); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if ($cat['activo']): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activo</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactivo</span>
                                <?php endif; ?>
                                <button onclick="editCategory(<?php echo $cat['id']; ?>)" class="text-blue-600 hover:text-blue-900 px-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="toggleCategory(<?php echo $cat['id']; ?>)" class="text-yellow-600 hover:text-yellow-900 px-2" title="Cambiar estado">
                                    <i class="fas fa-<?php echo $cat['activo'] ? 'pause' : 'play'; ?>"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No hay categorías de ingresos</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Expense Categories -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b bg-red-50">
                <h2 class="text-xl font-bold text-red-800">
                    <i class="fas fa-arrow-down mr-2"></i> Categorías de Egresos
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <?php 
                    $egresos = array_filter($categorias, function($cat) { return $cat['tipo'] === 'egreso'; });
                    if (!empty($egresos)): 
                    ?>
                        <?php foreach ($egresos as $cat): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($cat['nombre']); ?></div>
                                <?php if (!empty($cat['descripcion'])): ?>
                                    <div class="text-sm text-gray-600"><?php echo htmlspecialchars($cat['descripcion']); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if ($cat['activo']): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activo</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactivo</span>
                                <?php endif; ?>
                                <button onclick="editCategory(<?php echo $cat['id']; ?>)" class="text-blue-600 hover:text-blue-900 px-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="toggleCategory(<?php echo $cat['id']; ?>)" class="text-yellow-600 hover:text-yellow-900 px-2" title="Cambiar estado">
                                    <i class="fas fa-<?php echo $cat['activo'] ? 'pause' : 'play'; ?>"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No hay categorías de egresos</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="categoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-2xl font-bold text-gray-800">Agregar Categoría</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="categoryForm" onsubmit="return false;" class="space-y-4">
                <input type="hidden" id="category_id" name="id">
                
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de la Categoría *
                    </label>
                    <input type="text" id="nombre" name="nombre" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo *
                    </label>
                    <select id="tipo" name="tipo" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccione...</option>
                        <option value="ingreso">Ingreso</option>
                        <option value="egreso">Egreso</option>
                    </select>
                </div>
                
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea id="descripcion" name="descripcion" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="pt-4 flex justify-end space-x-4">
                    <button type="button" onclick="closeModal()" 
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="button" onclick="saveCategory()" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                        <i class="fas fa-save mr-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const baseUrl = '<?php echo Router::url(''); ?>';
    
    function showAddModal() {
        document.getElementById('modalTitle').textContent = 'Agregar Categoría';
        document.getElementById('categoryForm').reset();
        document.getElementById('category_id').value = '';
        document.getElementById('categoryModal').classList.remove('hidden');
    }
    
    async function editCategory(id) {
        try {
            const response = await fetch(baseUrl + '/financial/categories/get/' + id);
            const result = await response.json();
            
            if (result.success) {
                const cat = result.data;
                document.getElementById('modalTitle').textContent = 'Editar Categoría';
                document.getElementById('category_id').value = cat.id;
                document.getElementById('nombre').value = cat.nombre;
                document.getElementById('tipo').value = cat.tipo;
                document.getElementById('descripcion').value = cat.descripcion || '';
                document.getElementById('categoryModal').classList.remove('hidden');
            } else {
                alert('Error: ' + result.error);
            }
        } catch (error) {
            alert('Error al cargar categoría: ' + error.message);
        }
    }
    
    async function saveCategory() {
        const form = document.getElementById('categoryForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const id = document.getElementById('category_id').value;
        const formData = new FormData(form);
        
        const endpoint = id ? '/financial/categories/update' : '/financial/categories/create';
        
        try {
            const response = await fetch(baseUrl + endpoint, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                closeModal();
                location.reload();
            } else {
                alert('Error: ' + result.error);
            }
        } catch (error) {
            alert('Error al guardar categoría: ' + error.message);
        }
    }
    
    async function toggleCategory(id) {
        if (!confirm('¿Está seguro de cambiar el estado de esta categoría?')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('id', id);
        
        try {
            const response = await fetch(baseUrl + '/financial/categories/toggle', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert('Error: ' + result.error);
            }
        } catch (error) {
            alert('Error al cambiar estado: ' + error.message);
        }
    }
    
    function closeModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
