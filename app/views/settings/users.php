<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-users mr-2"></i> Gestión de Usuarios
            </h1>
            <p class="text-gray-600">Administrar usuarios del sistema</p>
        </div>
        
        <div class="flex gap-2">
            <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Nuevo Usuario
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
    
    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Usuario
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rol
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Último Acceso
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($user['nombre_completo'] ?? ''); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($user['username'] ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">
                                <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $rolColors = [
                                'admin' => 'bg-purple-100 text-purple-800',
                                'coordinador' => 'bg-blue-100 text-blue-800',
                                'chef' => 'bg-green-100 text-green-800',
                                'operativo' => 'bg-gray-100 text-gray-800'
                            ];
                            $colorClass = $rolColors[$user['rol']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $colorClass; ?>">
                                <?php echo strtoupper($user['rol']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($user['activo']): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $user['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) : 'Nunca'; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewUser(<?php echo $user['id']; ?>)" class="text-blue-600 hover:text-blue-900 mr-3" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editUser(<?php echo $user['id']; ?>)" class="text-green-600 hover:text-green-900 mr-3" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" class="text-red-600 hover:text-red-900" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                            <p class="text-gray-500 text-lg">No hay usuarios registrados</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create User Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Nuevo Usuario</h2>
        <form id="createUserForm" onsubmit="createUser(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Usuario</label>
                    <input type="text" name="username" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Contraseña</label>
                    <input type="password" name="password" required minlength="6" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre Completo</label>
                    <input type="text" name="nombre_completo" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Rol</label>
                    <select name="rol" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="operativo">Operativo</option>
                        <option value="chef">Chef</option>
                        <option value="coordinador">Coordinador</option>
                        <option value="admin">Admin</option>
                        <option value="cliente">Cliente</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Crear</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Editar Usuario</h2>
        <form id="editUserForm" onsubmit="updateUser(event)">
            <input type="hidden" name="id" id="edit_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Usuario</label>
                    <input type="text" name="username" id="edit_username" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" id="edit_email" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre Completo</label>
                    <input type="text" name="nombre_completo" id="edit_nombre_completo" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Rol</label>
                    <select name="rol" id="edit_rol" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="operativo">Operativo</option>
                        <option value="chef">Chef</option>
                        <option value="coordinador">Coordinador</option>
                        <option value="admin">Admin</option>
                        <option value="cliente">Cliente</option>
                    </select>
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

<!-- View User Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Detalles del Usuario</h2>
        <div id="viewUserContent" class="space-y-3"></div>
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
    document.getElementById('createUserForm').reset();
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    document.getElementById('viewModal').classList.remove('flex');
}

async function createUser(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('<?php echo Router::url('/settings/users/create'); ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Usuario creado correctamente');
            location.reload();
        } else {
            alert(data.error || 'Error al crear usuario');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}

async function editUser(id) {
    try {
        const response = await fetch(`<?php echo Router::url('/settings/users/get'); ?>/${id}`);
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('edit_id').value = data.data.id;
            document.getElementById('edit_username').value = data.data.username;
            document.getElementById('edit_email').value = data.data.email;
            document.getElementById('edit_nombre_completo').value = data.data.nombre_completo;
            document.getElementById('edit_rol').value = data.data.rol;
            document.getElementById('edit_activo').value = data.data.activo;
            
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        } else {
            alert(data.error || 'Error al cargar usuario');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}

async function updateUser(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('<?php echo Router::url('/settings/users/update'); ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Usuario actualizado correctamente');
            location.reload();
        } else {
            alert(data.error || 'Error al actualizar usuario');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}

async function viewUser(id) {
    try {
        const response = await fetch(`<?php echo Router::url('/settings/users/get'); ?>/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const user = data.data;
            const rolColors = {
                'admin': 'bg-purple-100 text-purple-800',
                'coordinador': 'bg-blue-100 text-blue-800',
                'chef': 'bg-green-100 text-green-800',
                'operativo': 'bg-gray-100 text-gray-800',
                'cliente': 'bg-yellow-100 text-yellow-800'
            };
            const colorClass = rolColors[user.rol] || 'bg-gray-100 text-gray-800';
            
            const content = `
                <div class="border-b pb-2"><strong>Usuario:</strong> ${user.username}</div>
                <div class="border-b pb-2"><strong>Email:</strong> ${user.email}</div>
                <div class="border-b pb-2"><strong>Nombre:</strong> ${user.nombre_completo}</div>
                <div class="border-b pb-2"><strong>Rol:</strong> <span class="px-2 py-1 text-xs rounded-full ${colorClass}">${user.rol.toUpperCase()}</span></div>
                <div class="border-b pb-2"><strong>Estado:</strong> ${user.activo ? '<span class="text-green-600">Activo</span>' : '<span class="text-red-600">Inactivo</span>'}</div>
                <div class="border-b pb-2"><strong>Fecha de creación:</strong> ${new Date(user.fecha_creacion).toLocaleString('es-MX')}</div>
                <div><strong>Último acceso:</strong> ${user.ultimo_acceso ? new Date(user.ultimo_acceso).toLocaleString('es-MX') : 'Nunca'}</div>
            `;
            
            document.getElementById('viewUserContent').innerHTML = content;
            document.getElementById('viewModal').classList.remove('hidden');
            document.getElementById('viewModal').classList.add('flex');
        } else {
            alert(data.error || 'Error al cargar usuario');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}

async function deleteUser(id, username) {
    if (!confirm(`¿Está seguro de eliminar el usuario "${username}"?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id', id);
    
    try {
        const response = await fetch('<?php echo Router::url('/settings/users/delete'); ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Usuario eliminado correctamente');
            location.reload();
        } else {
            alert(data.error || 'Error al eliminar usuario');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
