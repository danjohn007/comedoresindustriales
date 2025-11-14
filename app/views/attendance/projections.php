<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="<?php echo Router::url('/attendance'); ?>" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Asistencia
        </a>
    </div>
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-chart-line mr-2"></i> Proyecciones de Comensales
        </h1>
        <p class="text-gray-600">Cálculo automático basado en histórico y situaciones atípicas</p>
    </div>
    
    <!-- Calculator Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-calculator mr-2"></i> Calcular Nueva Proyección
        </h3>
        
        <div id="calculatorForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Comedor</label>
                <select id="calc_comedor" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Seleccione...</option>
                    <?php foreach ($comedores as $comedor): ?>
                    <option value="<?php echo $comedor['id']; ?>"><?php echo htmlspecialchars($comedor['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Turno</label>
                <select id="calc_turno" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Seleccione...</option>
                    <?php foreach ($turnos as $turno): ?>
                    <option value="<?php echo $turno['id']; ?>"><?php echo htmlspecialchars($turno['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                <input type="date" id="calc_fecha" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ajuste Manual</label>
                <input type="number" id="calc_ajuste" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Justificación del Ajuste</label>
            <input type="text" id="calc_justificacion" placeholder="Opcional - Solo si hay ajuste manual" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>
        
        <div class="mt-4 flex space-x-4">
            <button onclick="calculateProjection()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fas fa-calculator mr-2"></i> Calcular Proyección
            </button>
        </div>
        
        <!-- Result -->
        <div id="calculationResult" class="mt-6 hidden">
            <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                <h4 class="font-semibold text-blue-900 mb-2">Resultado del Cálculo:</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-blue-700">Promedio Histórico:</p>
                        <p class="text-blue-900 font-bold text-lg" id="result_historico">-</p>
                    </div>
                    <div>
                        <p class="text-blue-700">Impacto Atípico:</p>
                        <p class="text-blue-900 font-bold text-lg" id="result_atipico">-</p>
                    </div>
                    <div>
                        <p class="text-blue-700">Ajuste Manual:</p>
                        <p class="text-blue-900 font-bold text-lg" id="result_ajuste">-</p>
                    </div>
                    <div>
                        <p class="text-blue-700">Proyección Final:</p>
                        <p class="text-green-900 font-bold text-2xl" id="result_final">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Existing Projections -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-list mr-2"></i> Proyecciones Futuras
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Comedor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Turno</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Comensales</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Método</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Ajuste</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Justificación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projections)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No hay proyecciones futuras</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($projections as $proj): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo date('d/m/Y', strtotime($proj['fecha'])); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($proj['comedor']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($proj['turno']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 text-right">
                                <?php echo number_format($proj['comensales_proyectados']); ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $proj['metodo_calculo'] === 'historico' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'; ?>">
                                    <?php echo strtoupper(str_replace('_', ' ', $proj['metodo_calculo'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php if ($proj['ajuste_aplicado'] != 0): ?>
                                    <span class="<?php echo $proj['ajuste_aplicado'] > 0 ? 'text-green-600' : 'text-red-600'; ?> font-semibold">
                                        <?php echo $proj['ajuste_aplicado'] > 0 ? '+' : ''; ?><?php echo $proj['ajuste_aplicado']; ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?php echo htmlspecialchars(substr($proj['justificacion_ajuste'], 0, 50)); ?>
                                <?php if (strlen($proj['justificacion_ajuste']) > 50) echo '...'; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function calculateProjection() {
    const comedorId = document.getElementById('calc_comedor').value;
    const turnoId = document.getElementById('calc_turno').value;
    const fecha = document.getElementById('calc_fecha').value;
    const ajusteManual = document.getElementById('calc_ajuste').value;
    const justificacion = document.getElementById('calc_justificacion').value;
    
    if (!comedorId || !turnoId || !fecha) {
        alert('Por favor complete todos los campos requeridos');
        return;
    }
    
    const formData = new FormData();
    formData.append('comedor_id', comedorId);
    formData.append('turno_id', turnoId);
    formData.append('fecha', fecha);
    formData.append('ajuste_manual', ajusteManual);
    formData.append('justificacion', justificacion);
    
    fetch('<?php echo Router::url('/attendance/calculate-projection'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('result_historico').textContent = data.data.promedio_historico;
            document.getElementById('result_atipico').textContent = data.data.impacto_atipico;
            document.getElementById('result_ajuste').textContent = data.data.ajuste_manual;
            document.getElementById('result_final').textContent = data.data.comensales_proyectados;
            document.getElementById('calculationResult').classList.remove('hidden');
            
            alert('Proyección calculada y guardada correctamente');
            setTimeout(() => location.reload(), 2000);
        } else {
            alert('Error: ' + (data.error || 'No se pudo calcular la proyección'));
        }
    })
    .catch(error => {
        alert('Error al calcular proyección: ' + error);
    });
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
