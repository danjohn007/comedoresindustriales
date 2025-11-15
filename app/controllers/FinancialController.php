<?php
/**
 * Financial Controller
 * Handles financial management and reporting
 */

class FinancialController extends Controller {
    
    public function index() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        // Get financial summary
        $stmt = $this->db->query("
            SELECT 
                SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) as total_ingresos,
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as total_egresos,
                COUNT(*) as total_transacciones
            FROM transacciones_financieras
            WHERE MONTH(fecha_transaccion) = MONTH(CURRENT_DATE())
            AND YEAR(fecha_transaccion) = YEAR(CURRENT_DATE())
        ");
        $summary = $stmt->fetch();
        
        // Get recent transactions
        $stmt = $this->db->query("
            SELECT t.*, c.nombre as comedor_nombre
            FROM transacciones_financieras t
            LEFT JOIN comedores c ON t.comedor_id = c.id
            ORDER BY t.fecha_transaccion DESC, t.fecha_creacion DESC
            LIMIT 10
        ");
        $recentTransactions = $stmt->fetchAll();
        
        // Get budget status
        $stmt = $this->db->query("
            SELECT p.*, c.nombre as comedor_nombre
            FROM presupuestos p
            LEFT JOIN comedores c ON p.comedor_id = c.id
            WHERE p.anio = YEAR(CURRENT_DATE())
            AND p.mes = MONTH(CURRENT_DATE())
            ORDER BY c.nombre
        ");
        $budgets = $stmt->fetchAll();
        
        $data = [
            'title' => 'Módulo Financiero',
            'summary' => $summary,
            'recentTransactions' => $recentTransactions,
            'budgets' => $budgets
        ];
        
        $this->view('financial/index', $data);
    }
    
    public function transactions() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        $stmt = $this->db->query("
            SELECT t.*, c.nombre as comedor_nombre, u.nombre_completo as creado_por_nombre
            FROM transacciones_financieras t
            LEFT JOIN comedores c ON t.comedor_id = c.id
            LEFT JOIN usuarios u ON t.creado_por = u.id
            ORDER BY t.fecha_transaccion DESC, t.fecha_creacion DESC
        ");
        $transactions = $stmt->fetchAll();
        
        // Get comedores for filter
        $stmt = $this->db->query("SELECT id, nombre FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        // Get categorias for dropdown
        $stmt = $this->db->query("SELECT id, nombre, tipo FROM categorias_financieras WHERE activo = 1 ORDER BY tipo, nombre");
        $categorias = $stmt->fetchAll();
        
        $data = [
            'title' => 'Transacciones Financieras',
            'transactions' => $transactions,
            'comedores' => $comedores,
            'categorias' => $categorias
        ];
        
        $this->view('financial/transactions', $data);
    }
    
    public function budgets() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        $stmt = $this->db->query("
            SELECT p.*, c.nombre as comedor_nombre
            FROM presupuestos p
            LEFT JOIN comedores c ON p.comedor_id = c.id
            ORDER BY p.anio DESC, p.mes DESC, c.nombre
        ");
        $budgets = $stmt->fetchAll();
        
        // Get comedores for create/edit
        $stmt = $this->db->query("SELECT id, nombre FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        $data = [
            'title' => 'Presupuestos',
            'budgets' => $budgets,
            'comedores' => $comedores
        ];
        
        $this->view('financial/budgets', $data);
    }
    
    public function reports() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        $data = [
            'title' => 'Reportes Financieros'
        ];
        
        $this->view('financial/reports', $data);
    }
    
    public function createTransaction() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $comedorId = intval($_POST['comedor_id'] ?? 0);
        $tipo = $_POST['tipo'] ?? '';
        $concepto = trim($_POST['concepto'] ?? '');
        $monto = floatval($_POST['monto'] ?? 0);
        $categoriaId = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;
        $fechaTransaccion = $_POST['fecha_transaccion'] ?? date('Y-m-d');
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        if (!$comedorId || !$tipo || !$concepto || $monto <= 0) {
            $this->json(['error' => 'Todos los campos son requeridos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO transacciones_financieras 
                (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$comedorId, $tipo, $concepto, $monto, $categoriaId, $fechaTransaccion, $descripcion, $_SESSION['user_id']]);
            
            // Update budget if exists
            $this->updateBudgetSpent($comedorId, $fechaTransaccion, $tipo, $monto);
            
            $this->logAction('crear_transaccion', 'financiero', "Transacción creada: {$concepto}");
            $this->json(['success' => true, 'message' => 'Transacción creada correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al crear transacción: ' . $e->getMessage()], 500);
        }
    }
    
    public function createBudget() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $comedorId = intval($_POST['comedor_id'] ?? 0);
        $anio = intval($_POST['anio'] ?? date('Y'));
        $mes = intval($_POST['mes'] ?? date('n'));
        $presupuestoAsignado = floatval($_POST['presupuesto_asignado'] ?? 0);
        $notas = trim($_POST['notas'] ?? '');
        
        if (!$comedorId || !$anio || !$mes || $presupuestoAsignado <= 0) {
            $this->json(['error' => 'Todos los campos son requeridos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO presupuestos 
                (comedor_id, anio, mes, presupuesto_asignado, notas, creado_por)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$comedorId, $anio, $mes, $presupuestoAsignado, $notas, $_SESSION['user_id']]);
            
            $this->logAction('crear_presupuesto', 'financiero', "Presupuesto creado para comedor {$comedorId}");
            $this->json(['success' => true, 'message' => 'Presupuesto creado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al crear presupuesto: ' . $e->getMessage()], 500);
        }
    }
    
    public function recentMovements() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        // Get last 30 days transactions
        $stmt = $this->db->query("
            SELECT t.*, c.nombre as comedor_nombre, u.nombre_completo as creado_por_nombre,
                   cat.nombre as categoria_nombre
            FROM transacciones_financieras t
            LEFT JOIN comedores c ON t.comedor_id = c.id
            LEFT JOIN usuarios u ON t.creado_por = u.id
            LEFT JOIN categorias_financieras cat ON t.categoria_id = cat.id
            WHERE t.fecha_transaccion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ORDER BY t.fecha_transaccion DESC, t.fecha_creacion DESC
            LIMIT 100
        ");
        $movements = $stmt->fetchAll();
        
        $data = [
            'title' => 'Movimientos Recientes (últimos 30 días)',
            'movements' => $movements
        ];
        
        $this->view('financial/recent_movements', $data);
    }
    
    public function categories() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        $stmt = $this->db->query("
            SELECT * FROM categorias_financieras 
            ORDER BY tipo, nombre
        ");
        $categorias = $stmt->fetchAll();
        
        $data = [
            'title' => 'Catálogo de Categorías Financieras',
            'categorias' => $categorias
        ];
        
        $this->view('financial/categories', $data);
    }
    
    public function createCategory() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $tipo = $_POST['tipo'] ?? '';
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        if (!$nombre || !$tipo) {
            $this->json(['error' => 'Nombre y tipo son requeridos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO categorias_financieras (nombre, tipo, descripcion)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$nombre, $tipo, $descripcion]);
            
            $this->logAction('crear_categoria_financiera', 'financiero', "Categoría creada: {$nombre}");
            $this->json(['success' => true, 'message' => 'Categoría creada correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al crear categoría: ' . $e->getMessage()], 500);
        }
    }
    
    public function getCategory($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM categorias_financieras WHERE id = ?");
            $stmt->execute([$id]);
            $categoria = $stmt->fetch();
            
            if (!$categoria) {
                $this->json(['error' => 'Categoría no encontrada'], 404);
            }
            
            $this->json(['success' => true, 'data' => $categoria]);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al obtener categoría: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateCategory() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $tipo = $_POST['tipo'] ?? '';
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        if (!$id || !$nombre || !$tipo) {
            $this->json(['error' => 'Datos incompletos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE categorias_financieras 
                SET nombre = ?, tipo = ?, descripcion = ?
                WHERE id = ?
            ");
            $stmt->execute([$nombre, $tipo, $descripcion, $id]);
            
            $this->logAction('actualizar_categoria_financiera', 'financiero', "Categoría actualizada: {$nombre}");
            $this->json(['success' => true, 'message' => 'Categoría actualizada correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al actualizar categoría: ' . $e->getMessage()], 500);
        }
    }
    
    public function toggleCategory() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if (!$id) {
            $this->json(['error' => 'ID requerido'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE categorias_financieras SET activo = NOT activo WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->logAction('toggle_categoria_financiera', 'financiero', "Estado de categoría cambiado: ID {$id}");
            $this->json(['success' => true, 'message' => 'Estado cambiado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al cambiar estado: ' . $e->getMessage()], 500);
        }
    }
    
    private function updateBudgetSpent($comedorId, $fecha, $tipo, $monto) {
        try {
            $date = new DateTime($fecha);
            $anio = $date->format('Y');
            $mes = $date->format('n');
            
            // Check if budget exists
            $stmt = $this->db->prepare("
                SELECT id, presupuesto_gastado, presupuesto_asignado 
                FROM presupuestos 
                WHERE comedor_id = ? AND anio = ? AND mes = ?
            ");
            $stmt->execute([$comedorId, $anio, $mes]);
            $budget = $stmt->fetch();
            
            if ($budget) {
                // Update spent amount
                $adjustment = ($tipo === 'egreso') ? $monto : -$monto;
                $newSpent = $budget['presupuesto_gastado'] + $adjustment;
                $porcentaje = ($budget['presupuesto_asignado'] > 0) 
                    ? ($newSpent / $budget['presupuesto_asignado']) * 100 
                    : 0;
                
                $estado = 'activo';
                if ($porcentaje > 100) {
                    $estado = 'excedido';
                } elseif ($porcentaje >= 95) {
                    $estado = 'cerrado';
                }
                
                $stmt = $this->db->prepare("
                    UPDATE presupuestos 
                    SET presupuesto_gastado = ?, porcentaje_ejecutado = ?, estado = ?
                    WHERE id = ?
                ");
                $stmt->execute([$newSpent, $porcentaje, $estado, $budget['id']]);
            }
        } catch (Exception $e) {
            // Log error but don't fail the transaction
            error_log("Error updating budget: " . $e->getMessage());
        }
    }
    
    public function monthlyReport() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        $anio = $_GET['anio'] ?? date('Y');
        $mes = $_GET['mes'] ?? date('n');
        $comedorId = $_GET['comedor_id'] ?? null;
        
        // Get comedores for filter
        $stmt = $this->db->query("SELECT id, nombre FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        // Build report query
        $query = "
            SELECT 
                c.id as comedor_id,
                c.nombre as comedor,
                SUM(CASE WHEN t.tipo = 'ingreso' THEN t.monto ELSE 0 END) as total_ingresos,
                SUM(CASE WHEN t.tipo = 'egreso' THEN t.monto ELSE 0 END) as total_egresos,
                SUM(CASE WHEN t.tipo = 'ingreso' THEN t.monto ELSE -t.monto END) as balance,
                COUNT(t.id) as total_transacciones,
                p.presupuesto_asignado,
                p.presupuesto_gastado,
                p.porcentaje_ejecutado,
                p.estado as estado_presupuesto
            FROM comedores c
            LEFT JOIN transacciones_financieras t ON c.id = t.comedor_id 
                AND YEAR(t.fecha_transaccion) = ? 
                AND MONTH(t.fecha_transaccion) = ?
            LEFT JOIN presupuestos p ON c.id = p.comedor_id 
                AND p.anio = ? 
                AND p.mes = ?
            WHERE c.activo = 1
        ";
        
        $params = [$anio, $mes, $anio, $mes];
        
        if ($comedorId) {
            $query .= " AND c.id = ?";
            $params[] = $comedorId;
        }
        
        $query .= " GROUP BY c.id, c.nombre, p.presupuesto_asignado, p.presupuesto_gastado, p.porcentaje_ejecutado, p.estado
                    ORDER BY c.nombre";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $reportData = $stmt->fetchAll();
        
        // Calculate totals
        $totals = [
            'ingresos' => 0,
            'egresos' => 0,
            'balance' => 0,
            'presupuesto_asignado' => 0,
            'presupuesto_gastado' => 0
        ];
        
        foreach ($reportData as $row) {
            $totals['ingresos'] += $row['total_ingresos'];
            $totals['egresos'] += $row['total_egresos'];
            $totals['balance'] += $row['balance'];
            $totals['presupuesto_asignado'] += $row['presupuesto_asignado'] ?? 0;
            $totals['presupuesto_gastado'] += $row['presupuesto_gastado'] ?? 0;
        }
        
        $data = [
            'title' => 'Reporte Mensual Financiero',
            'reportData' => $reportData,
            'totals' => $totals,
            'comedores' => $comedores,
            'filters' => [
                'anio' => $anio,
                'mes' => $mes,
                'comedor_id' => $comedorId
            ]
        ];
        
        $this->view('financial/monthly_report', $data);
    }
    
    public function accountStatement() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $comedorId = $_GET['comedor_id'] ?? null;
        $tipo = $_GET['tipo'] ?? 'all';
        
        // Get comedores for filter
        $stmt = $this->db->query("SELECT id, nombre FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        // Build transactions query
        $query = "
            SELECT 
                t.id,
                t.fecha_transaccion,
                t.tipo,
                t.concepto,
                t.monto,
                t.descripcion,
                c.nombre as comedor,
                cat.nombre as categoria,
                u.nombre_completo as creado_por
            FROM transacciones_financieras t
            JOIN comedores c ON t.comedor_id = c.id
            LEFT JOIN categorias_financieras cat ON t.categoria_id = cat.id
            LEFT JOIN usuarios u ON t.creado_por = u.id
            WHERE t.fecha_transaccion BETWEEN ? AND ?
        ";
        
        $params = [$startDate, $endDate];
        
        if ($comedorId) {
            $query .= " AND t.comedor_id = ?";
            $params[] = $comedorId;
        }
        
        if ($tipo !== 'all') {
            $query .= " AND t.tipo = ?";
            $params[] = $tipo;
        }
        
        $query .= " ORDER BY t.fecha_transaccion DESC, t.fecha_creacion DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $transactions = $stmt->fetchAll();
        
        // Calculate totals
        $totals = [
            'ingresos' => 0,
            'egresos' => 0,
            'balance' => 0,
            'total_transacciones' => count($transactions)
        ];
        
        foreach ($transactions as $tx) {
            if ($tx['tipo'] === 'ingreso') {
                $totals['ingresos'] += $tx['monto'];
            } else if ($tx['tipo'] === 'egreso') {
                $totals['egresos'] += $tx['monto'];
            }
        }
        
        $totals['balance'] = $totals['ingresos'] - $totals['egresos'];
        
        $data = [
            'title' => 'Estado de Cuenta',
            'transactions' => $transactions,
            'totals' => $totals,
            'comedores' => $comedores,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'comedor_id' => $comedorId,
                'tipo' => $tipo
            ]
        ];
        
        $this->view('financial/account_statement', $data);
    }
    
    public function categoryAnalysis() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $comedorId = $_GET['comedor_id'] ?? null;
        
        // Get comedores for filter
        $stmt = $this->db->query("SELECT id, nombre FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        // Build analysis query
        $query = "
            SELECT 
                cat.id as categoria_id,
                cat.nombre as categoria,
                cat.tipo,
                COUNT(t.id) as cantidad_transacciones,
                SUM(t.monto) as total_monto,
                AVG(t.monto) as promedio_monto,
                MIN(t.monto) as monto_minimo,
                MAX(t.monto) as monto_maximo
            FROM categorias_financieras cat
            LEFT JOIN transacciones_financieras t ON cat.id = t.categoria_id
                AND t.fecha_transaccion BETWEEN ? AND ?
        ";
        
        $params = [$startDate, $endDate];
        
        if ($comedorId) {
            $query .= " AND t.comedor_id = ?";
            $params[] = $comedorId;
        }
        
        $query .= " WHERE cat.activo = 1
                    GROUP BY cat.id, cat.nombre, cat.tipo
                    ORDER BY cat.tipo, total_monto DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $categoryData = $stmt->fetchAll();
        
        // Separate by type and calculate totals
        $ingresos = [];
        $egresos = [];
        $totals = [
            'ingresos' => 0,
            'egresos' => 0,
            'balance' => 0
        ];
        
        foreach ($categoryData as $cat) {
            if ($cat['tipo'] === 'ingreso') {
                $ingresos[] = $cat;
                $totals['ingresos'] += $cat['total_monto'] ?? 0;
            } else {
                $egresos[] = $cat;
                $totals['egresos'] += $cat['total_monto'] ?? 0;
            }
        }
        
        $totals['balance'] = $totals['ingresos'] - $totals['egresos'];
        
        $data = [
            'title' => 'Análisis por Categoría',
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'totals' => $totals,
            'comedores' => $comedores,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'comedor_id' => $comedorId
            ]
        ];
        
        $this->view('financial/category_analysis', $data);
    }
}
