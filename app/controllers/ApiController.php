<?php
/**
 * API Controller (REQ-API-001)
 * External system integration endpoints
 */

class ApiController extends Controller {
    
    private function validateApiToken() {
        $token = $_SERVER['HTTP_X_API_TOKEN'] ?? $_GET['token'] ?? $_POST['token'] ?? null;
        
        if (!$token) {
            $this->json(['error' => 'API token required'], 401);
        }
        
        $stmt = $this->db->prepare("
            SELECT * FROM api_tokens 
            WHERE token = ? AND activo = 1 
            AND (fecha_expiracion IS NULL OR fecha_expiracion >= CURDATE())
        ");
        $stmt->execute([$token]);
        $apiToken = $stmt->fetch();
        
        if (!$apiToken) {
            $this->json(['error' => 'Invalid or expired token'], 401);
        }
        
        // Update last use
        $stmt = $this->db->prepare("UPDATE api_tokens SET ultimo_uso = NOW() WHERE id = ?");
        $stmt->execute([$apiToken['id']]);
        
        return $apiToken;
    }
    
    public function syncAttendance() {
        $apiToken = $this->validateApiToken();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'POST method required'], 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Expected format: { "comedor_id": 1, "turno_id": 1, "fecha": "2024-01-01", "comensales": 150 }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO asistencia_diaria 
                (comedor_id, turno_id, fecha, comensales_proyectados, comensales_reales, porcentaje_asistencia)
                VALUES (?, ?, ?, 0, ?, 100)
                ON DUPLICATE KEY UPDATE
                comensales_reales = VALUES(comensales_reales),
                porcentaje_asistencia = (comensales_reales / comensales_proyectados) * 100
            ");
            
            $stmt->execute([
                $data['comedor_id'],
                $data['turno_id'],
                $data['fecha'],
                $data['comensales']
            ]);
            
            $this->json([
                'success' => true,
                'message' => 'Attendance synced successfully'
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getProjections() {
        $apiToken = $this->validateApiToken();
        
        $startDate = $_GET['start_date'] ?? $_POST['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? $_POST['end_date'] ?? date('Y-m-d', strtotime('+7 days'));
        
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.fecha,
                    c.nombre as comedor,
                    t.nombre as turno,
                    p.comensales_proyectados,
                    p.metodo_calculo
                FROM proyecciones p
                JOIN comedores c ON p.comedor_id = c.id
                JOIN turnos t ON p.turno_id = t.id
                WHERE p.fecha BETWEEN ? AND ?
                ORDER BY p.fecha, t.hora_inicio
            ");
            $stmt->execute([$startDate, $endDate]);
            $projections = $stmt->fetchAll();
            
            $this->json([
                'success' => true,
                'data' => $projections,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate
                ]
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function exportProduction() {
        $apiToken = $this->validateApiToken();
        
        $fecha = $_GET['fecha'] ?? $_POST['fecha'] ?? date('Y-m-d');
        
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    op.numero_orden,
                    op.fecha_servicio,
                    c.nombre as comedor,
                    t.nombre as turno,
                    r.nombre as receta,
                    op.porciones_calcular,
                    op.estado
                FROM ordenes_produccion op
                JOIN comedores c ON op.comedor_id = c.id
                JOIN turnos t ON op.turno_id = t.id
                JOIN recetas r ON op.receta_id = r.id
                WHERE op.fecha_servicio = ?
                ORDER BY t.hora_inicio
            ");
            $stmt->execute([$fecha]);
            $orders = $stmt->fetchAll();
            
            $this->json([
                'success' => true,
                'data' => $orders,
                'fecha' => $fecha
            ]);
            
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function syncEmployees() {
        $apiToken = $this->validateApiToken();
        
        // Endpoint for receiving employee updates from HR system
        $this->json([
            'success' => true,
            'message' => 'Employee sync endpoint ready',
            'info' => 'POST employee data in JSON format'
        ]);
    }
}
