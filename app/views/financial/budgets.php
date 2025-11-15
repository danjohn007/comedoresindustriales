<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-wallet mr-2"></i> Presupuestos
            </h1>
            <p class="text-gray-600">Gestión de presupuestos por comedor</p>
        </div>
        
        <div class="flex gap-2">
            <?php if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cliente'): ?>
            <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Nuevo Presupuesto
            </button>
            <?php endif; ?>
            <a href="<?php echo Router::url('/financial'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>
    
    <!-- Budgets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (!empty($budgets)): ?>
            <?php foreach ($budgets as $budget): ?>
            <?php 
            $porcentaje = $budget['porcentaje_ejecutado'] ?? 0;
            $colorClass = 'text-green-600';
            $bgClass = 'bg-green-50 border-green-200';
            if ($porcentaje > 100) {
                $colorClass = 'text-red-600';
                $bgClass = 'bg-red-50 border-red-200';
            } elseif ($porcentaje > 90) {
                $colorClass = 'text-yellow-600';
                $bgClass = 'bg-yellow-50 border-yellow-200';
            }
            
            $monthNames = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            ?>
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-2 <?php echo $bgClass; ?>">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">
                            <?php echo htmlspecialchars($budget['comedor_nombre']); ?>
                        </h3>
                        <p class="text-sm text-gray-600">
                            <?php echo $monthNames[$budget['mes']] . ' ' . $budget['anio']; ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <?php if ($budget['estado'] === 'activo'): ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activo</span>
                        <?php elseif ($budget['estado'] === 'excedido'): ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Excedido</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Cerrado</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Asignado:</span>
                        <span class="text-sm font-semibold text-gray-900">
                            $<?php echo number_format($budget['presupuesto_asignado'], 2); ?>
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Gastado:</span>
                        <span class="text-sm font-semibold <?php echo $colorClass; ?>">
                            $<?php echo number_format($budget['presupuesto_gastado'], 2); ?>
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Disponible:</span>
                        <span class="text-sm font-semibold text-gray-900">
                            $<?php echo number_format($budget['presupuesto_asignado'] - $budget['presupuesto_gastado'], 2); ?>
                        </span>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-xs text-gray-600">Ejecución</span>
                            <span class="text-xs font-medium <?php echo $colorClass; ?>">
                                <?php echo number_format($porcentaje, 1); ?>%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full <?php 
                                if ($porcentaje > 100) echo 'bg-red-500';
                                elseif ($porcentaje > 90) echo 'bg-yellow-500';
                                else echo 'bg-green-500';
                            ?>" style="width: <?php echo min($porcentaje, 100); ?>%"></div>
                        </div>
                    </div>
                    
                    <?php if (!empty($budget['notas'])): ?>
                    <div class="mt-3 pt-3 border-t">
                        <p class="text-xs text-gray-600"><?php echo htmlspecialchars($budget['notas']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <i class="fas fa-wallet text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No hay presupuestos registrados</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Budget Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Nuevo Presupuesto</h2>
        <form id="createBudgetForm" onsubmit="createBudget(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Comedor</label>
                    <select name="comedor_id" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Seleccione un comedor</option>
                        <?php foreach ($comedores as $comedor): ?>
                        <option value="<?php echo $comedor['id']; ?>">
                            <?php echo htmlspecialchars($comedor['nombre']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Año</label>
                        <input type="number" name="anio" value="<?php echo date('Y'); ?>" min="2020" max="2099" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Mes</label>
                        <select name="mes" required class="w-full px-3 py-2 border rounded-lg">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($i == date('n')) ? 'selected' : ''; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Presupuesto Asignado</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" name="presupuesto_asignado" step="0.01" min="0" required class="w-full pl-8 pr-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Notas</label>
                    <textarea name="notas" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Observaciones opcionales"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Crear</button>
            </div>
        </form>
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
    document.getElementById('createBudgetForm').reset();
}

async function createBudget(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('<?php echo Router::url('/financial/budgets/create'); ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Presupuesto creado correctamente');
            location.reload();
        } else {
            alert(data.error || 'Error al crear presupuesto');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
