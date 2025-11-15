<?php
/**
 * Reports Controller (REQ-REPORTES-001)
 * Generates deviation and effectiveness reports
 */

class ReportsController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $data = [
            'title' => 'Centro de Reportes'
        ];
        
        $this->view('reports/index', $data);
    }
    
    public function attendance() {
        $this->requireAuth();
        
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $stmt = $this->db->prepare("
            SELECT 
                c.nombre as comedor,
                t.nombre as turno,
                COUNT(*) as total_registros,
                AVG(a.porcentaje_asistencia) as promedio_asistencia,
                SUM(a.comensales_proyectados) as total_proyectados,
                SUM(a.comensales_reales) as total_reales
            FROM asistencia_diaria a
            JOIN comedores c ON a.comedor_id = c.id
            JOIN turnos t ON a.turno_id = t.id
            WHERE a.fecha BETWEEN ? AND ?
            AND a.comensales_reales > 0
            GROUP BY c.id, t.id
            ORDER BY c.nombre, t.nombre
        ");
        $stmt->execute([$startDate, $endDate]);
        $reportData = $stmt->fetchAll();
        
        $data = [
            'title' => 'Reporte de Asistencia',
            'reportData' => $reportData,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
        
        $this->view('reports/attendance', $data);
    }
    
    public function deviation() {
        $this->requireAuth();
        
        $stmt = $this->db->prepare("
            SELECT 
                a.fecha,
                c.nombre as comedor,
                t.nombre as turno,
                a.comensales_proyectados,
                a.comensales_reales,
                a.porcentaje_asistencia,
                ABS(100 - a.porcentaje_asistencia) as desviacion
            FROM asistencia_diaria a
            JOIN comedores c ON a.comedor_id = c.id
            JOIN turnos t ON a.turno_id = t.id
            WHERE a.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            AND a.comensales_reales > 0
            AND ABS(100 - a.porcentaje_asistencia) > 5
            ORDER BY desviacion DESC, a.fecha DESC
            LIMIT 50
        ");
        $stmt->execute();
        $deviations = $stmt->fetchAll();
        
        $data = [
            'title' => 'Reporte de Desviaciones',
            'deviations' => $deviations
        ];
        
        $this->view('reports/deviation', $data);
    }
    
    public function production() {
        $this->requireAuth();
        
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $stmt = $this->db->prepare("
            SELECT 
                op.numero_orden,
                op.fecha_servicio,
                c.nombre as comedor,
                r.nombre as receta,
                op.comensales_proyectados,
                op.estado,
                COALESCE(SUM(oi.costo_estimado), 0) as costo_total
            FROM ordenes_produccion op
            JOIN comedores c ON op.comedor_id = c.id
            JOIN recetas r ON op.receta_id = r.id
            LEFT JOIN orden_ingredientes oi ON op.id = oi.orden_produccion_id
            WHERE op.fecha_servicio BETWEEN ? AND ?
            GROUP BY op.id
            ORDER BY op.fecha_servicio DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        $productionData = $stmt->fetchAll();
        
        $data = [
            'title' => 'Reporte de Producción',
            'productionData' => $productionData,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
        
        $this->view('reports/production', $data);
    }
    
    public function costs() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        $data = [
            'title' => 'Reporte de Costos'
        ];
        
        $this->view('reports/costs', $data);
    }
    
    public function generate() {
        $this->requireAuth();
        
        $reportType = $_POST['report_type'] ?? '';
        
        // Implementation for generating custom reports
        $_SESSION['info'] = 'Generador de reportes personalizados disponible';
        $this->redirect('/reports');
    }
    
    public function exportData() {
        $this->requireAuth();
        
        $data = [
            'title' => 'Exportar Datos - Generador Personalizado'
        ];
        
        $this->view('reports/export', $data);
    }
    
    public function export($type) {
        $this->requireAuth();
        
        // Implementation for exporting reports to Excel/PDF
        $_SESSION['info'] = 'Funcionalidad de exportación disponible';
        $this->redirect('/reports');
    }
}
