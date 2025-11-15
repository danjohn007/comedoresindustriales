<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-truck mr-2"></i> Gestión de Proveedores
            </h1>
            <p class="text-gray-600">Administre los proveedores de ingredientes</p>
        </div>
        
        <?php if (in_array($_SESSION['user_role'], ['admin', 'coordinador'])): ?>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Nuevo Proveedor
        </button>
        <?php endif; ?>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
        <p class="text-green-700"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Suppliers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Contacto</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Teléfono</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Ciudad</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Estado</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($proveedores)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No hay proveedores registrados</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($proveedores as $proveedor): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                <?php echo htmlspecialchars($proveedor['nombre']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?php echo htmlspecialchars($proveedor['contacto'] ?? '-'); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?php echo htmlspecialchars($proveedor['telefono'] ?? '-'); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?php echo htmlspecialchars($proveedor['email'] ?? '-'); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?php echo htmlspecialchars($proveedor['ciudad'] ?? '-'); ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $proveedor['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $proveedor['activo'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <?php if (in_array($_SESSION['user_role'], ['admin', 'coordinador'])): ?>
                                    <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($proveedor)); ?>)" 
                                            class="text-blue-600 hover:text-blue-700" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="toggleSupplier(<?php echo $proveedor['id']; ?>)" 
                                            class="text-<?php echo $proveedor['activo'] ? 'yellow' : 'green'; ?>-600 hover:text-<?php echo $proveedor['activo'] ? 'yellow' : 'green'; ?>-700" 
                                            title="<?php echo $proveedor['activo'] ? 'Desactivar' : 'Activar'; ?>">
                                        <i class="fas fa-<?php echo $proveedor['activo'] ? 'ban' : 'check-circle'; ?>"></i>
                                    </button>
                                    <?php if (in_array($_SESSION['user_role'], ['admin'])): ?>
                                    <button onclick="deleteSupplier(<?php echo $proveedor['id']; ?>, '<?php echo htmlspecialchars($proveedor['nombre']); ?>')" 
                                            class="text-red-600 hover:text-red-700" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="supplierModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modalTitle" class="text-2xl font-bold text-gray-800">Nuevo Proveedor</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="supplierForm" class="space-y-4">
                <input type="hidden" id="supplierId" name="id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                    <input type="text" id="supplierNombre" name="nombre" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contacto</label>
                    <input type="text" id="supplierContacto" name="contacto"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                        <input type="tel" id="supplierTelefono" name="telefono"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="supplierEmail" name="email"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                    <input type="text" id="supplierDireccion" name="direccion"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad</label>
                    <input type="text" id="supplierCiudad" name="ciudad"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <button type="button" onclick="closeModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                        <i class="fas fa-save mr-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let isEditMode = false;

function openCreateModal() {
    isEditMode = false;
    document.getElementById('modalTitle').textContent = 'Nuevo Proveedor';
    document.getElementById('supplierForm').reset();
    document.getElementById('supplierId').value = '';
    document.getElementById('supplierModal').classList.remove('hidden');
}

function openEditModal(proveedor) {
    isEditMode = true;
    document.getElementById('modalTitle').textContent = 'Editar Proveedor';
    document.getElementById('supplierId').value = proveedor.id;
    document.getElementById('supplierNombre').value = proveedor.nombre || '';
    document.getElementById('supplierContacto').value = proveedor.contacto || '';
    document.getElementById('supplierTelefono').value = proveedor.telefono || '';
    document.getElementById('supplierEmail').value = proveedor.email || '';
    document.getElementById('supplierDireccion').value = proveedor.direccion || '';
    document.getElementById('supplierCiudad').value = proveedor.ciudad || '';
    document.getElementById('supplierModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('supplierModal').classList.add('hidden');
}

document.getElementById('supplierForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = isEditMode ? '<?php echo Router::url('/suppliers/update'); ?>' : '<?php echo Router::url('/suppliers/create'); ?>';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert(result.error || 'Error al guardar');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
});

async function toggleSupplier(id) {
    if (!confirm('¿Está seguro de cambiar el estado de este proveedor?')) return;
    
    const formData = new FormData();
    formData.append('id', id);
    
    try {
        const response = await fetch('<?php echo Router::url('/suppliers/toggle'); ?>', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert(result.error || 'Error al cambiar estado');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}

async function deleteSupplier(id, nombre) {
    if (!confirm(`¿Está seguro de eliminar el proveedor "${nombre}"?`)) return;
    
    const formData = new FormData();
    formData.append('id', id);
    
    try {
        const response = await fetch('<?php echo Router::url('/suppliers/delete'); ?>', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert(result.error || 'Error al eliminar');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
