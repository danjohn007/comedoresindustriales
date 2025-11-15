<?php
/**
 * Production Orders Controller (REQ-PRODUCCION-001)
 * Manages production orders based on projections and recipes
 */

class ProductionController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $status = $_GET['status'] ?? 'all';
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d', strtotime('+7 days'));
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $query = "
            SELECT 
                op.id,
                op.numero_orden,
                op.fecha_servicio,
                c.nombre as comedor,
                t.nombre as turno,
                r.nombre as receta,
                ls.nombre as linea_servicio,
                op.comensales_proyectados,
                op.porciones_calcular,
                op.estado,
                op.fecha_creacion,
                u.nombre_completo as creado_por
            FROM ordenes_produccion op
            JOIN comedores c ON op.comedor_id = c.id
            JOIN turnos t ON op.turno_id = t.id
            JOIN recetas r ON op.receta_id = r.id
            JOIN lineas_servicio ls ON r.linea_servicio_id = ls.id
            LEFT JOIN usuarios u ON op.creado_por = u.id
            WHERE op.fecha_servicio BETWEEN ? AND ?
        ";
        
        $params = [$startDate, $endDate];
        
        if ($status !== 'all') {
            $query .= " AND op.estado = ?";
            $params[] = $status;
        }
        
        // Count total records
        $countQuery = "SELECT COUNT(*) as total FROM (" . $query . ") as subquery";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $totalRecords = $stmt->fetch()['total'];
        $totalPages = ceil($totalRecords / $perPage);
        
        $query .= " ORDER BY op.fecha_servicio, t.hora_inicio, r.nombre LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
        
        $stmt = $this->db->prepare($query);
        
        // Execute with params (LIMIT and OFFSET are now embedded in query)
        $stmt->execute($params);
        $orders = $stmt->fetchAll();
        
        $data = [
            'title' => 'Órdenes de Producción',
            'orders' => $orders,
            'filters' => [
                'status' => $status,
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'per_page' => $perPage
            ]
        ];
        
        $this->view('production/index', $data);
    }
    
    public function create() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'chef']);
        
        // Get comedores
        $stmt = $this->db->query("SELECT * FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        // Get turnos
        $stmt = $this->db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY hora_inicio");
        $turnos = $stmt->fetchAll();
        
        // Get recipes with service lines
        $stmt = $this->db->query("
            SELECT r.*, ls.nombre as linea_servicio
            FROM recetas r
            JOIN lineas_servicio ls ON r.linea_servicio_id = ls.id
            WHERE r.activo = 1
            ORDER BY ls.orden_visualizacion, r.nombre
        ");
        $recetas = $stmt->fetchAll();
        
        $data = [
            'title' => 'Nueva Orden de Producción',
            'comedores' => $comedores,
            'turnos' => $turnos,
            'recetas' => $recetas,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('production/create', $data);
    }
    
    public function store() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'chef']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/production/create');
        }
        
        $comedorId = $_POST['comedor_id'] ?? null;
        $turnoId = $_POST['turno_id'] ?? null;
        $fechaServicio = $_POST['fecha_servicio'] ?? null;
        $recetaId = $_POST['receta_id'] ?? null;
        $comensalesProyectados = $_POST['comensales_proyectados'] ?? 0;
        $observaciones = $_POST['observaciones'] ?? '';
        
        if (!$comedorId || !$turnoId || !$fechaServicio || !$recetaId || !$comensalesProyectados) {
            $_SESSION['error'] = 'Todos los campos son requeridos';
            $this->redirect('/production/create');
        }
        
        try {
            $this->db->beginTransaction();
            
            // Generate order number
            $numeroOrden = 'OP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Insert production order
            $stmt = $this->db->prepare("
                INSERT INTO ordenes_produccion 
                (numero_orden, comedor_id, turno_id, fecha_servicio, receta_id, 
                 comensales_proyectados, porciones_calcular, estado, observaciones, creado_por)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente', ?, ?)
            ");
            
            $stmt->execute([
                $numeroOrden, $comedorId, $turnoId, $fechaServicio, 
                $recetaId, $comensalesProyectados, $comensalesProyectados, 
                $observaciones, $_SESSION['user_id']
            ]);
            
            $ordenId = $this->db->lastInsertId();
            
            // Calculate and insert ingredients
            $this->calculateOrderIngredients($ordenId, $recetaId, $comensalesProyectados);
            
            $this->db->commit();
            
            $this->logAction('crear_orden_produccion', 'produccion', "Orden creada: {$numeroOrden}");
            $_SESSION['success'] = 'Orden de producción creada correctamente';
            $this->redirect('/production/view/' . $ordenId);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Error al crear orden: ' . $e->getMessage();
            $this->redirect('/production/create');
        }
    }
    
    private function calculateOrderIngredients($ordenId, $recetaId, $porciones) {
        // Get recipe with base portions
        $stmt = $this->db->prepare("SELECT porciones_base FROM recetas WHERE id = ?");
        $stmt->execute([$recetaId]);
        $receta = $stmt->fetch();
        
        $porcionesBase = $receta['porciones_base'];
        $factor = $porciones / $porcionesBase;
        
        // Get recipe ingredients
        $stmt = $this->db->prepare("
            SELECT ri.*, i.nombre, i.costo_unitario
            FROM receta_ingredientes ri
            JOIN ingredientes i ON ri.ingrediente_id = i.id
            WHERE ri.receta_id = ?
        ");
        $stmt->execute([$recetaId]);
        $ingredientes = $stmt->fetchAll();
        
        // Insert calculated ingredients
        $insertStmt = $this->db->prepare("
            INSERT INTO orden_ingredientes 
            (orden_produccion_id, ingrediente_id, cantidad_requerida, unidad, costo_estimado)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($ingredientes as $ing) {
            $cantidadRequerida = round($ing['cantidad'] * $factor, 3);
            $costoEstimado = round($cantidadRequerida * $ing['costo_unitario'], 2);
            
            $insertStmt->execute([
                $ordenId,
                $ing['ingrediente_id'],
                $cantidadRequerida,
                $ing['unidad'],
                $costoEstimado
            ]);
        }
    }
    
    public function viewOrder($id) {
        $this->requireAuth();
        
        // Get order details
        $stmt = $this->db->prepare("
            SELECT 
                op.*,
                c.nombre as comedor,
                t.nombre as turno,
                r.nombre as receta,
                r.descripcion as receta_descripcion,
                ls.nombre as linea_servicio,
                u.nombre_completo as creado_por
            FROM ordenes_produccion op
            JOIN comedores c ON op.comedor_id = c.id
            JOIN turnos t ON op.turno_id = t.id
            JOIN recetas r ON op.receta_id = r.id
            JOIN lineas_servicio ls ON r.linea_servicio_id = ls.id
            LEFT JOIN usuarios u ON op.creado_por = u.id
            WHERE op.id = ?
        ");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        
        if (!$order) {
            $_SESSION['error'] = 'Orden no encontrada';
            $this->redirect('/production');
        }
        
        // Get order ingredients
        $stmt = $this->db->prepare("
            SELECT 
                oi.*,
                i.nombre as ingrediente
            FROM orden_ingredientes oi
            JOIN ingredientes i ON oi.ingrediente_id = i.id
            WHERE oi.orden_produccion_id = ?
            ORDER BY i.nombre
        ");
        $stmt->execute([$id]);
        $ingredientes = $stmt->fetchAll();
        
        $data = [
            'title' => 'Orden de Producción - ' . $order['numero_orden'],
            'order' => $order,
            'ingredientes' => $ingredientes
        ];
        
        $this->view('production/view', $data);
    }
    
    public function edit($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'chef']);
        
        // Get order
        $stmt = $this->db->prepare("SELECT * FROM ordenes_produccion WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        
        if (!$order) {
            $_SESSION['error'] = 'Orden no encontrada';
            $this->redirect('/production');
        }
        
        // Get comedores, turnos, recetas
        $stmt = $this->db->query("SELECT * FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        $stmt = $this->db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY hora_inicio");
        $turnos = $stmt->fetchAll();
        
        $stmt = $this->db->query("
            SELECT r.*, ls.nombre as linea_servicio
            FROM recetas r
            JOIN lineas_servicio ls ON r.linea_servicio_id = ls.id
            WHERE r.activo = 1
            ORDER BY ls.orden_visualizacion, r.nombre
        ");
        $recetas = $stmt->fetchAll();
        
        $data = [
            'title' => 'Editar Orden de Producción',
            'order' => $order,
            'comedores' => $comedores,
            'turnos' => $turnos,
            'recetas' => $recetas,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('production/edit', $data);
    }
    
    public function update($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'chef']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/production');
        }
        
        $estado = $_POST['estado'] ?? 'pendiente';
        $observaciones = $_POST['observaciones'] ?? '';
        
        try {
            $stmt = $this->db->prepare("
                UPDATE ordenes_produccion 
                SET estado = ?, observaciones = ?
                WHERE id = ?
            ");
            
            $stmt->execute([$estado, $observaciones, $id]);
            
            $this->logAction('actualizar_orden_produccion', 'produccion', "Orden actualizada: ID {$id}");
            $_SESSION['success'] = 'Orden actualizada correctamente';
            $this->redirect('/production/view/' . $id);
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
            $this->redirect('/production/edit/' . $id);
        }
    }
    
    public function calculateIngredients() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $recetaId = $_POST['receta_id'] ?? null;
        $porciones = $_POST['porciones'] ?? 0;
        
        if (!$recetaId || !$porciones) {
            $this->json(['error' => 'Datos incompletos'], 400);
        }
        
        try {
            // Get recipe
            $stmt = $this->db->prepare("SELECT porciones_base FROM recetas WHERE id = ?");
            $stmt->execute([$recetaId]);
            $receta = $stmt->fetch();
            
            $porcionesBase = $receta['porciones_base'];
            $factor = $porciones / $porcionesBase;
            
            // Get ingredients
            $stmt = $this->db->prepare("
                SELECT 
                    i.nombre,
                    ri.cantidad,
                    ri.unidad,
                    i.costo_unitario,
                    ri.notas
                FROM receta_ingredientes ri
                JOIN ingredientes i ON ri.ingrediente_id = i.id
                WHERE ri.receta_id = ?
                ORDER BY i.nombre
            ");
            $stmt->execute([$recetaId]);
            $ingredientes = $stmt->fetchAll();
            
            $result = [];
            $costoTotal = 0;
            
            foreach ($ingredientes as $ing) {
                $cantidadRequerida = round($ing['cantidad'] * $factor, 3);
                $costo = round($cantidadRequerida * $ing['costo_unitario'], 2);
                $costoTotal += $costo;
                
                $result[] = [
                    'nombre' => $ing['nombre'],
                    'cantidad_base' => $ing['cantidad'],
                    'cantidad_requerida' => $cantidadRequerida,
                    'unidad' => $ing['unidad'],
                    'costo_unitario' => $ing['costo_unitario'],
                    'costo_total' => $costo,
                    'notas' => $ing['notas']
                ];
            }
            
            $this->json([
                'success' => true,
                'ingredientes' => $result,
                'costo_total' => $costoTotal,
                'porciones' => $porciones,
                'factor' => $factor
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function print($id) {
        $this->requireAuth();
        
        // Get order with all details (similar to view method)
        $stmt = $this->db->prepare("
            SELECT 
                op.*,
                c.nombre as comedor, c.ubicacion,
                t.nombre as turno,
                r.nombre as receta, r.descripcion as receta_descripcion,
                ls.nombre as linea_servicio
            FROM ordenes_produccion op
            JOIN comedores c ON op.comedor_id = c.id
            JOIN turnos t ON op.turno_id = t.id
            JOIN recetas r ON op.receta_id = r.id
            JOIN lineas_servicio ls ON r.linea_servicio_id = ls.id
            WHERE op.id = ?
        ");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        
        if (!$order) {
            $_SESSION['error'] = 'Orden no encontrada';
            $this->redirect('/production');
        }
        
        $stmt = $this->db->prepare("
            SELECT oi.*, i.nombre as ingrediente
            FROM orden_ingredientes oi
            JOIN ingredientes i ON oi.ingrediente_id = i.id
            WHERE oi.orden_produccion_id = ?
            ORDER BY i.nombre
        ");
        $stmt->execute([$id]);
        $ingredientes = $stmt->fetchAll();
        
        $data = [
            'title' => 'Imprimir Orden - ' . $order['numero_orden'],
            'order' => $order,
            'ingredientes' => $ingredientes
        ];
        
        $this->view('production/print', $data);
    }
}
