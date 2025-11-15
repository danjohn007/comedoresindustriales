<?php
/**
 * Attendance Controller (REQ-ASIST-001, REQ-DATA-001)
 * Manages attendance records and projections
 */

class AttendanceController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        // Get all comedores
        $stmt = $this->db->query("SELECT * FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        // Get all turnos
        $stmt = $this->db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY hora_inicio");
        $turnos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Asistencia',
            'comedores' => $comedores,
            'turnos' => $turnos
        ];
        
        $this->view('attendance/index', $data);
    }
    
    public function history() {
        $this->requireAuth();
        
        $comedorId = $_GET['comedor'] ?? null;
        $turnoId = $_GET['turno'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        // Build query
        $query = "
            SELECT 
                a.id,
                a.fecha,
                c.nombre as comedor,
                t.nombre as turno,
                a.comensales_proyectados,
                a.comensales_reales,
                a.porcentaje_asistencia,
                a.observaciones,
                u.nombre_completo as registrado_por
            FROM asistencia_diaria a
            JOIN comedores c ON a.comedor_id = c.id
            JOIN turnos t ON a.turno_id = t.id
            LEFT JOIN usuarios u ON a.registrado_por = u.id
            WHERE a.fecha BETWEEN ? AND ?
        ";
        
        $params = [$startDate, $endDate];
        
        if ($comedorId) {
            $query .= " AND a.comedor_id = ?";
            $params[] = $comedorId;
        }
        
        if ($turnoId) {
            $query .= " AND a.turno_id = ?";
            $params[] = $turnoId;
        }
        
        // Count total records
        $countQuery = "SELECT COUNT(*) as total FROM (" . $query . ") as subquery";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $totalRecords = $stmt->fetch()['total'];
        $totalPages = ceil($totalRecords / $perPage);
        
        $query .= " ORDER BY a.fecha DESC, t.hora_inicio LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $records = $stmt->fetchAll();
        
        // Get comedores and turnos for filters
        $stmt = $this->db->query("SELECT * FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        $stmt = $this->db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY hora_inicio");
        $turnos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Historial de Asistencia',
            'records' => $records,
            'comedores' => $comedores,
            'turnos' => $turnos,
            'filters' => [
                'comedor' => $comedorId,
                'turno' => $turnoId,
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
        
        $this->view('attendance/history', $data);
    }
    
    public function recordForm() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'operativo']);
        
        // Get comedores
        $stmt = $this->db->query("SELECT * FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        // Get turnos
        $stmt = $this->db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY hora_inicio");
        $turnos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Registrar Asistencia',
            'comedores' => $comedores,
            'turnos' => $turnos,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('attendance/record', $data);
    }
    
    public function saveRecord() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'operativo']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/attendance/record');
        }
        
        $comedorId = $_POST['comedor_id'] ?? null;
        $turnoId = $_POST['turno_id'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $comensalesReales = $_POST['comensales_reales'] ?? 0;
        $observaciones = $_POST['observaciones'] ?? '';
        
        // Validation
        if (!$comedorId || !$turnoId || !$fecha) {
            $_SESSION['error'] = 'Todos los campos son requeridos';
            $this->redirect('/attendance/record');
        }
        
        try {
            // Get projection or create one
            $stmt = $this->db->prepare("
                SELECT comensales_proyectados 
                FROM proyecciones 
                WHERE comedor_id = ? AND turno_id = ? AND fecha = ?
            ");
            $stmt->execute([$comedorId, $turnoId, $fecha]);
            $projection = $stmt->fetch();
            
            $comensalesProyectados = $projection ? $projection['comensales_proyectados'] : $comensalesReales;
            
            // Calculate percentage
            $porcentaje = $comensalesProyectados > 0 ? ($comensalesReales / $comensalesProyectados) * 100 : 100;
            
            // Check if record exists
            $stmt = $this->db->prepare("
                SELECT id FROM asistencia_diaria 
                WHERE comedor_id = ? AND turno_id = ? AND fecha = ?
            ");
            $stmt->execute([$comedorId, $turnoId, $fecha]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update existing record
                $stmt = $this->db->prepare("
                    UPDATE asistencia_diaria 
                    SET comensales_reales = ?, 
                        porcentaje_asistencia = ?,
                        observaciones = ?,
                        registrado_por = ?,
                        fecha_registro = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$comensalesReales, $porcentaje, $observaciones, $_SESSION['user_id'], $existing['id']]);
                
                $this->logAction('actualizar_asistencia', 'asistencia', "Asistencia actualizada: {$fecha}");
                $_SESSION['success'] = 'Registro de asistencia actualizado correctamente';
            } else {
                // Insert new record
                $stmt = $this->db->prepare("
                    INSERT INTO asistencia_diaria 
                    (comedor_id, turno_id, fecha, comensales_proyectados, comensales_reales, porcentaje_asistencia, observaciones, registrado_por)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $comedorId, $turnoId, $fecha, $comensalesProyectados, 
                    $comensalesReales, $porcentaje, $observaciones, $_SESSION['user_id']
                ]);
                
                $this->logAction('crear_asistencia', 'asistencia', "Asistencia registrada: {$fecha}");
                $_SESSION['success'] = 'Registro de asistencia creado correctamente';
            }
            
            $this->redirect('/attendance/history');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al guardar el registro: ' . $e->getMessage();
            $this->redirect('/attendance/record');
        }
    }
    
    public function projections() {
        $this->requireAuth();
        
        // Get upcoming projections
        $stmt = $this->db->prepare("
            SELECT 
                p.id,
                p.fecha,
                c.nombre as comedor,
                t.nombre as turno,
                p.comensales_proyectados,
                p.metodo_calculo,
                p.ajuste_aplicado,
                p.justificacion_ajuste,
                u.nombre_completo as creado_por
            FROM proyecciones p
            JOIN comedores c ON p.comedor_id = c.id
            JOIN turnos t ON p.turno_id = t.id
            LEFT JOIN usuarios u ON p.creado_por = u.id
            WHERE p.fecha >= CURDATE()
            ORDER BY p.fecha, t.hora_inicio
            LIMIT 50
        ");
        $stmt->execute();
        $projections = $stmt->fetchAll();
        
        // Get comedores and turnos
        $stmt = $this->db->query("SELECT * FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        $stmt = $this->db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY hora_inicio");
        $turnos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Proyecciones de Comensales',
            'projections' => $projections,
            'comedores' => $comedores,
            'turnos' => $turnos,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('attendance/projections', $data);
    }
    
    public function calculateProjection() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $comedorId = $_POST['comedor_id'] ?? null;
        $turnoId = $_POST['turno_id'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $ajusteManual = $_POST['ajuste_manual'] ?? 0;
        $justificacion = $_POST['justificacion'] ?? '';
        
        if (!$comedorId || !$turnoId || !$fecha) {
            $this->json(['error' => 'Datos incompletos'], 400);
        }
        
        try {
            // Calculate historical average for the same day of week
            $dayOfWeek = date('w', strtotime($fecha));
            
            $stmt = $this->db->prepare("
                SELECT AVG(comensales_reales) as promedio
                FROM asistencia_diaria
                WHERE comedor_id = ? 
                AND turno_id = ?
                AND DAYOFWEEK(fecha) = ?
                AND fecha >= DATE_SUB(?, INTERVAL 90 DAY)
                AND comensales_reales > 0
            ");
            $stmt->execute([$comedorId, $turnoId, $dayOfWeek + 1, $fecha]);
            $result = $stmt->fetch();
            
            $promedioHistorico = round($result['promedio'] ?? 100);
            
            // Check for atypical situations
            $stmt = $this->db->prepare("
                SELECT SUM(impacto_comensales) as impacto_total
                FROM situaciones_atipicas
                WHERE comedor_id = ?
                AND fecha_inicio <= ?
                AND (fecha_fin IS NULL OR fecha_fin >= ?)
                AND (turnos_afectados IS NULL OR FIND_IN_SET(
                    (SELECT nombre FROM turnos WHERE id = ?), turnos_afectados
                ))
            ");
            $stmt->execute([$comedorId, $fecha, $fecha, $turnoId]);
            $impacto = $stmt->fetch();
            
            $impactoAtipico = $impacto['impacto_total'] ?? 0;
            
            // Final calculation
            $comensalesProyectados = $promedioHistorico + $impactoAtipico + $ajusteManual;
            
            // Save or update projection
            $stmt = $this->db->prepare("
                INSERT INTO proyecciones 
                (comedor_id, turno_id, fecha, comensales_proyectados, metodo_calculo, ajuste_aplicado, justificacion_ajuste, creado_por)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                comensales_proyectados = VALUES(comensales_proyectados),
                metodo_calculo = VALUES(metodo_calculo),
                ajuste_aplicado = VALUES(ajuste_aplicado),
                justificacion_ajuste = VALUES(justificacion_ajuste),
                creado_por = VALUES(creado_por)
            ");
            
            $metodo = $ajusteManual != 0 ? 'ajuste_manual' : 'historico';
            
            $stmt->execute([
                $comedorId, $turnoId, $fecha, $comensalesProyectados, 
                $metodo, $ajusteManual, $justificacion, $_SESSION['user_id']
            ]);
            
            // Also update asistencia_diaria if it doesn't have real count yet
            $stmt = $this->db->prepare("
                INSERT INTO asistencia_diaria 
                (comedor_id, turno_id, fecha, comensales_proyectados, comensales_reales, registrado_por)
                VALUES (?, ?, ?, ?, 0, ?)
                ON DUPLICATE KEY UPDATE
                comensales_proyectados = VALUES(comensales_proyectados)
            ");
            $stmt->execute([$comedorId, $turnoId, $fecha, $comensalesProyectados, $_SESSION['user_id']]);
            
            $this->logAction('calcular_proyeccion', 'proyecciones', "Proyección calculada para {$fecha}");
            
            $this->json([
                'success' => true,
                'data' => [
                    'promedio_historico' => $promedioHistorico,
                    'impacto_atipico' => $impactoAtipico,
                    'ajuste_manual' => $ajusteManual,
                    'comensales_proyectados' => $comensalesProyectados
                ]
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
