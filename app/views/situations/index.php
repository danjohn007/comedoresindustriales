<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/nav.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i> Situaciones Atípicas
            </h1>
            <p class="text-gray-600">Eventos que afectan la proyección de comensales</p>
        </div>
        
        <?php if (in_array($_SESSION['user_role'], ['admin', 'coordinador'])): ?>
        <a href="<?php echo Router::url('/situations/create'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Nueva Situación
        </a>
        <?php endif; ?>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
        <p class="text-green-700"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Active Situations -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 bg-red-50 border-b border-red-200">
            <h3 class="text-lg font-semibold text-red-800">
                <i class="fas fa-bell mr-2"></i> Situaciones Activas (<?php echo count($activeSituations); ?>)
            </h3>
        </div>
        
        <div class="p-6">
            <?php if (empty($activeSituations)): ?>
                <p class="text-gray-500 text-center py-4">No hay situaciones activas</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($activeSituations as $sit): ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition">
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full <?php 
                                $colors = [
                                    'contratacion' => 'bg-green-100 text-green-800',
                                    'despido' => 'bg-red-100 text-red-800',
                                    'incapacidad' => 'bg-yellow-100 text-yellow-800',
                                    'evento_especial' => 'bg-blue-100 text-blue-800',
                                    'dia_festivo' => 'bg-purple-100 text-purple-800',
                                    'otro' => 'bg-gray-100 text-gray-800'
                                ];
                                echo $colors[$sit['tipo']] ?? 'bg-gray-100 text-gray-800';
                            ?>">
                                <?php echo strtoupper(str_replace('_', ' ', $sit['tipo'])); ?>
                            </span>
                            
                            <span class="text-lg font-bold <?php echo $sit['impacto_comensales'] > 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo $sit['impacto_comensales'] > 0 ? '+' : ''; ?><?php echo $sit['impacto_comensales']; ?>
                            </span>
                        </div>
                        
                        <h4 class="font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($sit['comedor']); ?></h4>
                        <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($sit['descripcion']); ?></p>
                        
                        <div class="text-xs text-gray-500 space-y-1">
                            <p><i class="fas fa-calendar mr-1"></i> 
                                Desde: <?php echo date('d/m/Y', strtotime($sit['fecha_inicio'])); ?>
                                <?php if ($sit['fecha_fin']): ?>
                                    hasta <?php echo date('d/m/Y', strtotime($sit['fecha_fin'])); ?>
                                <?php endif; ?>
                            </p>
                            <?php if ($sit['turnos_afectados']): ?>
                            <p><i class="fas fa-clock mr-1"></i> Turnos: <?php echo htmlspecialchars($sit['turnos_afectados']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (in_array($_SESSION['user_role'], ['admin', 'coordinador'])): ?>
                        <div class="mt-3 flex space-x-2">
                            <a href="<?php echo Router::url('/situations/edit/' . $sit['id']); ?>" class="text-blue-600 hover:text-blue-700 text-sm">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Past Situations -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-history mr-2"></i> Situaciones Pasadas
            </h3>
        </div>
        
        <div class="p-6">
            <?php if (empty($pastSituations)): ?>
                <p class="text-gray-500 text-center py-4">No hay registros históricos</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tipo</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Comedor</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Período</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Impacto</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pastSituations as $sit): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                        <?php echo strtoupper(str_replace('_', ' ', $sit['tipo'])); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($sit['comedor']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <?php echo date('d/m/Y', strtotime($sit['fecha_inicio'])); ?>
                                    <?php if ($sit['fecha_fin']): ?>
                                        - <?php echo date('d/m/Y', strtotime($sit['fecha_fin'])); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold <?php echo $sit['impacto_comensales'] > 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $sit['impacto_comensales'] > 0 ? '+' : ''; ?><?php echo $sit['impacto_comensales']; ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <?php echo htmlspecialchars(substr($sit['descripcion'], 0, 60)); ?>
                                    <?php if (strlen($sit['descripcion']) > 60) echo '...'; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
