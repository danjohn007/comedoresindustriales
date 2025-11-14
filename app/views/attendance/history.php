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
            <i class="fas fa-history mr-2"></i> Historial de Asistencia
        </h1>
        <p class="text-gray-600">Registros históricos de asistencia por comedor y turno</p>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="<?php echo Router::url('/attendance/history'); ?>" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Comedor</label>
                <select name="comedor" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todos</option>
                    <?php foreach ($comedores as $comedor): ?>
                    <option value="<?php echo $comedor['id']; ?>" <?php echo $filters['comedor'] == $comedor['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($comedor['nombre']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Turno</label>
                <select name="turno" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todos</option>
                    <?php foreach ($turnos as $turno): ?>
                    <option value="<?php echo $turno['id']; ?>" <?php echo $filters['turno'] == $turno['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($turno['nombre']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="start_date" value="<?php echo $filters['start_date']; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="end_date" value="<?php echo $filters['end_date']; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
    
    <!-- Results -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Comedor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Turno</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Proyectados</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Reales</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">% Asistencia</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron registros con los filtros seleccionados</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($records as $record): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo date('d/m/Y', strtotime($record['fecha'])); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($record['comedor']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($record['turno']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($record['comensales_proyectados']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($record['comensales_reales']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <?php 
                                    $percentage = $record['porcentaje_asistencia'];
                                    $colorClass = 'text-green-600';
                                    $bgClass = 'bg-green-100';
                                    if ($percentage < 90) {
                                        $colorClass = 'text-red-600';
                                        $bgClass = 'bg-red-100';
                                    } elseif ($percentage < 95) {
                                        $colorClass = 'text-yellow-600';
                                        $bgClass = 'bg-yellow-100';
                                    }
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $colorClass . ' ' . $bgClass; ?>">
                                    <?php echo number_format($percentage, 1); ?>%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?php echo htmlspecialchars(substr($record['observaciones'], 0, 50)); ?>
                                <?php if (strlen($record['observaciones']) > 50) echo '...'; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
