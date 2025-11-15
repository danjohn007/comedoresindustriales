<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-exchange-alt mr-2"></i> Transacciones Financieras
            </h1>
            <p class="text-gray-600">Gestión de ingresos y egresos</p>
        </div>
        
        <div class="flex gap-2">
            <?php if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'cliente'): ?>
            <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Nueva Transacción
            </button>
            <?php endif; ?>
            <a href="<?php echo Router::url('/financial'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>
    
    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creado Por</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $trans): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo date('d/m/Y', strtotime($trans['fecha_transaccion'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($trans['comedor_nombre']); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?php echo htmlspecialchars($trans['concepto']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php echo htmlspecialchars($trans['categoria'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if ($trans['tipo'] === 'ingreso'): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-arrow-up mr-1"></i>Ingreso
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-arrow-down mr-1"></i>Egreso
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                            <?php 
                            $color = $trans['tipo'] === 'ingreso' ? 'text-green-600' : 'text-red-600';
                            echo "<span class='{$color}'>$" . number_format($trans['monto'], 2) . "</span>";
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php echo htmlspecialchars($trans['creado_por_nombre'] ?? 'Sistema'); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-exchange-alt text-gray-300 text-6xl mb-4"></i>
                            <p class="text-lg">No hay transacciones registradas</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Transaction Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Nueva Transacción</h2>
        <form id="createTransactionForm" onsubmit="createTransaction(event)">
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
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo</label>
                    <select name="tipo" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="ingreso">Ingreso</option>
                        <option value="egreso">Egreso</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Concepto</label>
                    <input type="text" name="concepto" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Categoría</label>
                    <input type="text" name="categoria" class="w-full px-3 py-2 border rounded-lg" placeholder="Ej: Ingredientes, Servicios, etc.">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Monto</label>
                    <input type="number" name="monto" step="0.01" min="0" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de Transacción</label>
                    <input type="date" name="fecha_transaccion" value="<?php echo date('Y-m-d'); ?>" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
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
    document.getElementById('createTransactionForm').reset();
}

async function createTransaction(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    try {
        const response = await fetch('<?php echo Router::url('/financial/transactions/create'); ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Transacción creada correctamente');
            location.reload();
        } else {
            alert(data.error || 'Error al crear transacción');
        }
    } catch (error) {
        alert('Error al procesar la solicitud');
    }
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
