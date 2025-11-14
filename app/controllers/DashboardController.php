<?php
/**
 * Dashboard Controller (REQ-DASHBOARD-001)
 * Interactive control panel with projections vs real attendance
 */

class DashboardController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        
        // Get today's statistics
        $today = date('Y-m-d');
        
        // Total diners projected for today
        $stmt = $this->db->prepare("
            SELECT SUM(comensales_proyectados) as total
            FROM asistencia_diaria
            WHERE fecha = ?
        ");
        $stmt->execute([$today]);
        $todayProjected = $stmt->fetch()['total'] ?? 0;
        
        // Total diners real for today
        $stmt = $this->db->prepare("
            SELECT SUM(comensales_reales) as total
            FROM asistencia_diaria
            WHERE fecha = ? AND comensales_reales > 0
        ");
        $stmt->execute([$today]);
        $todayReal = $stmt->fetch()['total'] ?? 0;
        
        // Production orders pending
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM ordenes_produccion
            WHERE estado = 'pendiente'
        ");
        $stmt->execute();
        $pendingOrders = $stmt->fetch()['total'] ?? 0;
        
        // Active atypical situations
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM situaciones_atipicas
            WHERE fecha_inicio <= CURDATE() 
            AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())
        ");
        $stmt->execute();
        $activeSituations = $stmt->fetch()['total'] ?? 0;
        
        // Recent attendance records (last 7 days by shift)
        $stmt = $this->db->prepare("
            SELECT 
                a.fecha,
                t.nombre as turno,
                SUM(a.comensales_proyectados) as proyectados,
                SUM(a.comensales_reales) as reales,
                AVG(a.porcentaje_asistencia) as porcentaje
            FROM asistencia_diaria a
            JOIN turnos t ON a.turno_id = t.id
            WHERE a.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY a.fecha, t.nombre
            ORDER BY a.fecha DESC, t.nombre
            LIMIT 21
        ");
        $stmt->execute();
        $recentAttendance = $stmt->fetchAll();
        
        // Upcoming production orders
        $stmt = $this->db->prepare("
            SELECT 
                op.id,
                op.numero_orden,
                op.fecha_servicio,
                c.nombre as comedor,
                t.nombre as turno,
                r.nombre as receta,
                op.estado,
                op.comensales_proyectados
            FROM ordenes_produccion op
            JOIN comedores c ON op.comedor_id = c.id
            JOIN turnos t ON op.turno_id = t.id
            JOIN recetas r ON op.receta_id = r.id
            WHERE op.fecha_servicio >= CURDATE()
            ORDER BY op.fecha_servicio, t.hora_inicio
            LIMIT 10
        ");
        $stmt->execute();
        $upcomingOrders = $stmt->fetchAll();
        
        // Alerts: deviations greater than 10%
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
            WHERE a.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            AND a.comensales_reales > 0
            AND ABS(100 - a.porcentaje_asistencia) > 10
            ORDER BY a.fecha DESC, desviacion DESC
            LIMIT 5
        ");
        $stmt->execute();
        $alerts = $stmt->fetchAll();
        
        $data = [
            'title' => 'Dashboard - Sistema de Comedores',
            'user' => $user,
            'todayProjected' => $todayProjected,
            'todayReal' => $todayReal,
            'pendingOrders' => $pendingOrders,
            'activeSituations' => $activeSituations,
            'recentAttendance' => $recentAttendance,
            'upcomingOrders' => $upcomingOrders,
            'alerts' => $alerts
        ];
        
        $this->view('dashboard/index', $data);
    }
    
    public function stats() {
        $this->requireAuth();
        
        // Get statistics for charts
        $period = $_GET['period'] ?? '7days';
        
        switch ($period) {
            case '30days':
                $days = 30;
                break;
            case '90days':
                $days = 90;
                break;
            default:
                $days = 7;
        }
        
        // Attendance trend
        $stmt = $this->db->prepare("
            SELECT 
                fecha,
                SUM(comensales_proyectados) as proyectados,
                SUM(comensales_reales) as reales
            FROM asistencia_diaria
            WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY fecha
            ORDER BY fecha
        ");
        $stmt->execute([$days]);
        $attendanceTrend = $stmt->fetchAll();
        
        // By shift
        $stmt = $this->db->prepare("
            SELECT 
                t.nombre as turno,
                AVG(a.porcentaje_asistencia) as promedio,
                COUNT(*) as registros
            FROM asistencia_diaria a
            JOIN turnos t ON a.turno_id = t.id
            WHERE a.fecha >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            AND a.comensales_reales > 0
            GROUP BY t.nombre
        ");
        $stmt->execute([$days]);
        $byShift = $stmt->fetchAll();
        
        $this->json([
            'success' => true,
            'attendanceTrend' => $attendanceTrend,
            'byShift' => $byShift
        ]);
    }
}
