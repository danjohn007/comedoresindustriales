<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-chart-line mr-2"></i> Dashboard de Control
        </h1>
        <p class="text-gray-600">Panel de control y monitoreo en tiempo real</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Today Projected -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Proyección Hoy</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo number_format($todayProjected); ?></p>
                    <p class="text-xs text-gray-500 mt-1">comensales</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-calculator text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Today Real -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Asistencia Real</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo number_format($todayReal); ?></p>
                    <p class="text-xs text-gray-500 mt-1">comensales hoy</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-users text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Pending Orders -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Órdenes Pendientes</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $pendingOrders; ?></p>
                    <p class="text-xs text-gray-500 mt-1">órdenes de producción</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-4">
                    <i class="fas fa-clipboard-list text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Active Situations -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Situaciones Activas</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $activeSituations; ?></p>
                    <p class="text-xs text-gray-500 mt-1">eventos atípicos</p>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alerts Section -->
    <?php if (!empty($alerts)): ?>
    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-red-800 mb-4">
            <i class="fas fa-bell mr-2"></i> Alertas de Desviación (>10%)
        </h3>
        <div class="space-y-3">
            <?php foreach ($alerts as $alert): ?>
            <div class="bg-white rounded p-4 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-800">
                        <?php echo htmlspecialchars($alert['comedor']); ?> - <?php echo htmlspecialchars($alert['turno']); ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        Fecha: <?php echo date('d/m/Y', strtotime($alert['fecha'])); ?> | 
                        Proyectado: <?php echo $alert['comensales_proyectados']; ?> | 
                        Real: <?php echo $alert['comensales_reales']; ?>
                    </p>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                        <?php echo number_format($alert['desviacion'], 1); ?>% desviación
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Quick Access Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <?php if (in_array($_SESSION['user_role'], ['admin'])): ?>
        <a href="<?php echo Router::url('/settings/users'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-4 mr-4">
                    <i class="fas fa-users-cog text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 text-lg">Usuarios</h3>
                    <p class="text-sm text-gray-600">Gestionar usuarios del sistema</p>
                </div>
            </div>
        </a>
        
        <a href="<?php echo Router::url('/settings/comedores'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-4 mr-4">
                    <i class="fas fa-building text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 text-lg">Comedores</h3>
                    <p class="text-sm text-gray-600">Gestionar comedores</p>
                </div>
            </div>
        </a>
        <?php endif; ?>
        
        <?php if (in_array($_SESSION['user_role'], ['admin', 'chef'])): ?>
        <a href="<?php echo Router::url('/settings/ingredients'); ?>" class="block bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="bg-orange-100 rounded-full p-4 mr-4">
                    <i class="fas fa-carrot text-orange-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 text-lg">Ingredientes</h3>
                    <p class="text-sm text-gray-600">Catálogo de ingredientes</p>
                </div>
            </div>
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Charts and Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Attendance Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-bar mr-2"></i> Asistencia Últimos 7 Días
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
        
        <!-- Upcoming Production Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-calendar-alt mr-2"></i> Próximas Órdenes de Producción
            </h3>
            <div class="overflow-auto" style="max-height: 400px;">
                <?php if (empty($upcomingOrders)): ?>
                    <p class="text-gray-500 text-center py-4">No hay órdenes programadas</p>
                <?php else: ?>
                    <?php foreach ($upcomingOrders as $order): ?>
                    <div class="mb-3 p-3 border border-gray-200 rounded hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800">
                                <?php echo htmlspecialchars($order['numero_orden']); ?>
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded
                                <?php 
                                    echo $order['estado'] === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                         ($order['estado'] === 'en_proceso' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); 
                                ?>">
                                <?php echo strtoupper($order['estado']); ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-calendar mr-1"></i> <?php echo date('d/m/Y', strtotime($order['fecha_servicio'])); ?>
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-clock mr-1"></i> <?php echo htmlspecialchars($order['turno']); ?>
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-utensils mr-1"></i> <?php echo htmlspecialchars($order['receta']); ?>
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-users mr-1"></i> <?php echo $order['comensales_proyectados']; ?> comensales
                        </p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Attendance Table -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-list mr-2"></i> Registro de Asistencia Reciente
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Turno</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Proyectados</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Reales</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">% Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentAttendance)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">No hay registros</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentAttendance as $record): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo date('d/m/Y', strtotime($record['fecha'])); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800">
                                <?php echo htmlspecialchars($record['turno']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($record['proyectados']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 text-right">
                                <?php echo number_format($record['reales']); ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <?php 
                                    $percentage = $record['porcentaje'];
                                    $colorClass = 'text-green-600';
                                    if ($percentage < 90) $colorClass = 'text-red-600';
                                    elseif ($percentage < 95) $colorClass = 'text-yellow-600';
                                ?>
                                <span class="font-semibold <?php echo $colorClass; ?>">
                                    <?php echo number_format($percentage, 1); ?>%
                                </span>
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
// Attendance Chart
<?php
$chartDates = [];
$chartProjected = [];
$chartReal = [];

// Group by date
$groupedData = [];
foreach ($recentAttendance as $record) {
    $date = $record['fecha'];
    if (!isset($groupedData[$date])) {
        $groupedData[$date] = ['proyectados' => 0, 'reales' => 0];
    }
    $groupedData[$date]['proyectados'] += $record['proyectados'];
    $groupedData[$date]['reales'] += $record['reales'];
}

foreach ($groupedData as $date => $data) {
    $chartDates[] = date('d/m', strtotime($date));
    $chartProjected[] = $data['proyectados'];
    $chartReal[] = $data['reales'];
}
?>

const ctx = document.getElementById('attendanceChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_reverse($chartDates)); ?>,
            datasets: [
                {
                    label: 'Proyectados',
                    data: <?php echo json_encode(array_reverse($chartProjected)); ?>,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Reales',
                    data: <?php echo json_encode(array_reverse($chartReal)); ?>,
                    backgroundColor: 'rgba(34, 197, 94, 0.5)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
