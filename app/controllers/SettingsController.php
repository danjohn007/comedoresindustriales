<?php
/**
 * Settings Controller (REQ-CONFIG-001)
 * System configuration management
 */

class SettingsController extends Controller {
    
    public function index() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        $stmt = $this->db->query("
            SELECT * FROM configuracion_sistema 
            ORDER BY categoria, clave
        ");
        $settings = $stmt->fetchAll();
        
        $data = [
            'title' => 'Configuración del Sistema',
            'settings' => $settings,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('settings/index', $data);
    }
    
    public function update() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings');
        }
        
        try {
            foreach ($_POST as $key => $value) {
                if ($key === 'csrf_token') continue;
                
                $stmt = $this->db->prepare("
                    UPDATE configuracion_sistema 
                    SET valor = ?, modificado_por = ?
                    WHERE clave = ?
                ");
                $stmt->execute([$value, $_SESSION['user_id'], $key]);
            }
            
            $this->logAction('actualizar_configuracion', 'configuracion', 'Configuración actualizada');
            $_SESSION['success'] = 'Configuración actualizada correctamente';
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
        }
        
        $this->redirect('/settings');
    }
    
    public function users() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        $stmt = $this->db->query("
            SELECT id, username, email, nombre_completo, rol, activo, fecha_creacion, ultimo_acceso
            FROM usuarios
            ORDER BY nombre_completo
        ");
        $users = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Usuarios',
            'users' => $users
        ];
        
        $this->view('settings/users', $data);
    }
    
    public function comedores() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        $stmt = $this->db->query("SELECT * FROM comedores ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Comedores',
            'comedores' => $comedores
        ];
        
        $this->view('settings/comedores', $data);
    }
    
    public function ingredients() {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        $stmt = $this->db->query("SELECT * FROM ingredientes ORDER BY nombre");
        $ingredientes = $stmt->fetchAll();
        
        $data = [
            'title' => 'Catálogo de Ingredientes',
            'ingredientes' => $ingredientes
        ];
        
        $this->view('settings/ingredients', $data);
    }
}
