<?php
/**
 * Atypical Situations Controller (REQ-AJUSTES-001)
 * Manages atypical situations that affect attendance
 */

class SituationsController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        // Get active situations
        $stmt = $this->db->prepare("
            SELECT 
                s.id,
                s.tipo,
                s.fecha_inicio,
                s.fecha_fin,
                s.impacto_comensales,
                s.descripcion,
                s.turnos_afectados,
                c.nombre as comedor,
                u.nombre_completo as creado_por
            FROM situaciones_atipicas s
            JOIN comedores c ON s.comedor_id = c.id
            LEFT JOIN usuarios u ON s.creado_por = u.id
            WHERE s.fecha_inicio <= CURDATE() 
            AND (s.fecha_fin IS NULL OR s.fecha_fin >= CURDATE())
            ORDER BY s.fecha_inicio DESC
        ");
        $stmt->execute();
        $activeSituations = $stmt->fetchAll();
        
        // Get past situations
        $stmt = $this->db->prepare("
            SELECT 
                s.id,
                s.tipo,
                s.fecha_inicio,
                s.fecha_fin,
                s.impacto_comensales,
                s.descripcion,
                s.turnos_afectados,
                c.nombre as comedor,
                u.nombre_completo as creado_por
            FROM situaciones_atipicas s
            JOIN comedores c ON s.comedor_id = c.id
            LEFT JOIN usuarios u ON s.creado_por = u.id
            WHERE s.fecha_fin < CURDATE()
            ORDER BY s.fecha_inicio DESC
            LIMIT 20
        ");
        $stmt->execute();
        $pastSituations = $stmt->fetchAll();
        
        $data = [
            'title' => 'Situaciones Atípicas',
            'activeSituations' => $activeSituations,
            'pastSituations' => $pastSituations
        ];
        
        $this->view('situations/index', $data);
    }
    
    public function create() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        // Get comedores
        $stmt = $this->db->query("SELECT * FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        // Get turnos
        $stmt = $this->db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY hora_inicio");
        $turnos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Registrar Situación Atípica',
            'comedores' => $comedores,
            'turnos' => $turnos,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('situations/create', $data);
    }
    
    public function store() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/situations/create');
        }
        
        $comedorId = $_POST['comedor_id'] ?? null;
        $tipo = $_POST['tipo'] ?? null;
        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;
        $impacto = $_POST['impacto_comensales'] ?? 0;
        $descripcion = $_POST['descripcion'] ?? '';
        $turnosAfectados = isset($_POST['turnos_afectados']) ? implode(',', $_POST['turnos_afectados']) : '';
        
        // Validation
        if (!$comedorId || !$tipo || !$fechaInicio || !$impacto || !$descripcion) {
            $_SESSION['error'] = 'Todos los campos son requeridos';
            $this->redirect('/situations/create');
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO situaciones_atipicas 
                (comedor_id, tipo, fecha_inicio, fecha_fin, impacto_comensales, descripcion, turnos_afectados, creado_por)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $comedorId, $tipo, $fechaInicio, 
                $fechaFin ?: null, $impacto, $descripcion, 
                $turnosAfectados, $_SESSION['user_id']
            ]);
            
            $this->logAction('crear_situacion', 'situaciones', "Situación creada: {$tipo}");
            $_SESSION['success'] = 'Situación atípica registrada correctamente';
            $this->redirect('/situations');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al guardar: ' . $e->getMessage();
            $this->redirect('/situations/create');
        }
    }
    
    public function edit($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        // Get situation
        $stmt = $this->db->prepare("SELECT * FROM situaciones_atipicas WHERE id = ?");
        $stmt->execute([$id]);
        $situation = $stmt->fetch();
        
        if (!$situation) {
            $_SESSION['error'] = 'Situación no encontrada';
            $this->redirect('/situations');
        }
        
        // Get comedores
        $stmt = $this->db->query("SELECT * FROM comedores WHERE activo = 1 ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        // Get turnos
        $stmt = $this->db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY hora_inicio");
        $turnos = $stmt->fetchAll();
        
        $data = [
            'title' => 'Editar Situación Atípica',
            'situation' => $situation,
            'comedores' => $comedores,
            'turnos' => $turnos,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('situations/edit', $data);
    }
    
    public function update($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/situations');
        }
        
        $comedorId = $_POST['comedor_id'] ?? null;
        $tipo = $_POST['tipo'] ?? null;
        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;
        $impacto = $_POST['impacto_comensales'] ?? 0;
        $descripcion = $_POST['descripcion'] ?? '';
        $turnosAfectados = isset($_POST['turnos_afectados']) ? implode(',', $_POST['turnos_afectados']) : '';
        
        try {
            $stmt = $this->db->prepare("
                UPDATE situaciones_atipicas 
                SET comedor_id = ?, tipo = ?, fecha_inicio = ?, fecha_fin = ?, 
                    impacto_comensales = ?, descripcion = ?, turnos_afectados = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $comedorId, $tipo, $fechaInicio, 
                $fechaFin ?: null, $impacto, $descripcion, 
                $turnosAfectados, $id
            ]);
            
            $this->logAction('actualizar_situacion', 'situaciones', "Situación actualizada: ID {$id}");
            $_SESSION['success'] = 'Situación actualizada correctamente';
            $this->redirect('/situations');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
            $this->redirect('/situations/edit/' . $id);
        }
    }
    
    public function delete($id) {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/situations');
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM situaciones_atipicas WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->logAction('eliminar_situacion', 'situaciones', "Situación eliminada: ID {$id}");
            $_SESSION['success'] = 'Situación eliminada correctamente';
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar: ' . $e->getMessage();
        }
        
        $this->redirect('/situations');
    }
}
