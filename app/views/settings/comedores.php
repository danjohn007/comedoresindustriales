<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-building mr-2"></i> Gestión de Comedores
            </h1>
            <p class="text-gray-600">Administrar comedores del sistema</p>
        </div>
        
        <div class="flex gap-2">
            <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Nuevo Comedor
            </button>
            <a href="<?php echo Router::url('/dashboard'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
            </a>
        </div>
    </div>
    
    <?php if (isset($success)): ?>
    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded">
        <p class="text-green-700"><?php echo htmlspecialchars($success); ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
        <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Comedores Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (!empty($comedores)): ?>
            <?php foreach ($comedores as $comedor): ?>
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            <?php echo htmlspecialchars($comedor['nombre'] ?? ''); ?>
                        </h3>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <?php echo htmlspecialchars($comedor['ubicacion'] ?? ''); ?>
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fas fa-city mr-1"></i>
                            <?php echo htmlspecialchars($comedor['ciudad'] ?? ''); ?>, <?php echo htmlspecialchars($comedor['estado'] ?? ''); ?>
                        </p>
                    </div>
                    <?php if ($comedor['activo']): ?>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Activo
                        </span>
                    <?php else: ?>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Inactivo
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="border-t pt-4 mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-users mr-1"></i> Capacidad
                        </span>
                        <span class="text-sm font-semibold text-gray-800">
                            <?php echo number_format($comedor['capacidad_total'] ?? 0); ?> comensales
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="text-sm text-gray-600 block mb-1">
                            <i class="fas fa-clock mr-1"></i> Turnos Activos
                        </span>
                        <div class="flex flex-wrap gap-1">
                            <?php 
                            $turnos = explode(',', $comedor['turnos_activos'] ?? '');
                            foreach ($turnos as $turno):
                            ?>
                            <span class="px-2 py-1 text-xs rounded bg-blue-50 text-blue-700">
                                <?php echo htmlspecialchars(trim($turno)); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2 mt-4">
                    <button onclick="viewComedor(<?php echo $comedor['id']; ?>)" class="text-blue-600 hover:text-blue-900 px-3 py-1 text-sm" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="editComedor(<?php echo $comedor['id']; ?>)" class="text-green-600 hover:text-green-900 px-3 py-1 text-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteComedor(<?php echo $comedor['id']; ?>, '<?php echo htmlspecialchars($comedor['nombre']); ?>')" class="text-red-600 hover:text-red-900 px-3 py-1 text-sm" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <i class="fas fa-building text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No hay comedores registrados</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Comedor Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Nuevo Comedor</h2>
        <form id="createComedorForm" onsubmit="createComedor(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre</label>
                    <input type="text" name="nombre" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Ubicación</label>
                    <input type="text" name="ubicacion" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Ciudad</label>
                    <input type="text" name="ciudad" value="Querétaro" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Estado</label>
                    <input type="text" name="estado" value="Querétaro" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Capacidad Total</label>
                    <input type="number" name="capacidad_total" required min="1" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Turnos Activos</label>
                    <input type="text" name="turnos_activos" value="matutino,vespertino,nocturno" required class="w-full px-3 py-2 border rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Separados por comas</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Crear</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Comedor Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Editar Comedor</h2>
        <form id="editComedorForm" onsubmit="updateComedor(event)">
            <input type="hidden" name="id" id="edit_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre</label>
                    <input type="text" name="nombre" id="edit_nombre" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Ubicación</label>
                    <input type="text" name="ubicacion" id="edit_ubicacion" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Ciudad</label>
                    <input type="text" name="ciudad" id="edit_ciudad" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Estado</label>
                    <input type="text" name="estado" id="edit_estado" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Capacidad Total</label>
                    <input type="number" name="capacidad_total" id="edit_capacidad_total" required min="1" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Turnos Activos</label>
                    <input type="text" name="turnos_activos" id="edit_turnos_activos" required class="w-full px-3 py-2 border rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Separados por comas</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Estado</label>
                    <select name="activo" id="edit_activo" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- View Comedor Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Detalles del Comedor</h2>
        <div id="viewComedorContent" class="space-y-3"></div>
        <div class="flex justify-end mt-6">
            <button onclick="closeViewModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Cerrar</button>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    document.getElementById('createModal').classList.add('flex');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createModal').classList.remove('flex');
    document.getElementById('createComedorForm').reset();
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    document.getElementById('viewModal').classList.remove('flex');
}

async function createComedor(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('<?php echo Router::url('/settings/comedores/create'); ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Comedor creado correctamente');
            location.reload();
        } else {
            alert(data.error || 'Error al crear comedor');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}

async function editComedor(id) {
    try {
        const response = await fetch(`<?php echo Router::url('/settings/comedores/get'); ?>/${id}`);
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('edit_id').value = data.data.id;
            document.getElementById('edit_nombre').value = data.data.nombre;
            document.getElementById('edit_ubicacion').value = data.data.ubicacion;
            document.getElementById('edit_ciudad').value = data.data.ciudad;
            document.getElementById('edit_estado').value = data.data.estado;
            document.getElementById('edit_capacidad_total').value = data.data.capacidad_total;
            document.getElementById('edit_turnos_activos').value = data.data.turnos_activos;
            document.getElementById('edit_activo').value = data.data.activo;
            
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        } else {
            alert(data.error || 'Error al cargar comedor');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}

async function updateComedor(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('<?php echo Router::url('/settings/comedores/update'); ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Comedor actualizado correctamente');
            location.reload();
        } else {
            alert(data.error || 'Error al actualizar comedor');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}

async function viewComedor(id) {
    try {
        const response = await fetch(`<?php echo Router::url('/settings/comedores/get'); ?>/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const comedor = data.data;
            
            const content = `
                <div class="border-b pb-2"><strong>Nombre:</strong> ${comedor.nombre}</div>
                <div class="border-b pb-2"><strong>Ubicación:</strong> ${comedor.ubicacion}</div>
                <div class="border-b pb-2"><strong>Ciudad:</strong> ${comedor.ciudad}</div>
                <div class="border-b pb-2"><strong>Estado:</strong> ${comedor.estado}</div>
                <div class="border-b pb-2"><strong>Capacidad:</strong> ${comedor.capacidad_total} comensales</div>
                <div class="border-b pb-2"><strong>Turnos Activos:</strong> ${comedor.turnos_activos}</div>
                <div class="border-b pb-2"><strong>Estado:</strong> ${comedor.activo ? '<span class="text-green-600">Activo</span>' : '<span class="text-red-600">Inactivo</span>'}</div>
                <div><strong>Fecha de creación:</strong> ${new Date(comedor.fecha_creacion).toLocaleString('es-MX')}</div>
            `;
            
            document.getElementById('viewComedorContent').innerHTML = content;
            document.getElementById('viewModal').classList.remove('hidden');
            document.getElementById('viewModal').classList.add('flex');
        } else {
            alert(data.error || 'Error al cargar comedor');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}

async function deleteComedor(id, nombre) {
    if (!confirm(`¿Está seguro de eliminar el comedor "${nombre}"?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id', id);
    
    try {
        const response = await fetch('<?php echo Router::url('/settings/comedores/delete'); ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Comedor eliminado correctamente');
            location.reload();
        } else {
            alert(data.error || 'Error al eliminar comedor');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
