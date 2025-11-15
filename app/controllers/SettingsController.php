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
    
    public function createIngredient() {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $nombre = $_POST['nombre'] ?? '';
        $unidadMedida = $_POST['unidad_medida'] ?? '';
        $costoUnitario = $_POST['costo_unitario'] ?? 0;
        $proveedor = $_POST['proveedor'] ?? '';
        
        if (empty($nombre) || empty($unidadMedida)) {
            $this->json(['error' => 'Nombre y unidad de medida son requeridos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO ingredientes (nombre, unidad_medida, costo_unitario, proveedor, activo)
                VALUES (?, ?, ?, ?, 1)
            ");
            $stmt->execute([$nombre, $unidadMedida, $costoUnitario, $proveedor]);
            
            $this->logAction('crear_ingrediente', 'ingredientes', "Ingrediente creado: {$nombre}");
            $this->json(['success' => true, 'message' => 'Ingrediente creado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al crear ingrediente: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateIngredient() {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = $_POST['id'] ?? 0;
        $nombre = $_POST['nombre'] ?? '';
        $unidadMedida = $_POST['unidad_medida'] ?? '';
        $costoUnitario = $_POST['costo_unitario'] ?? 0;
        $proveedor = $_POST['proveedor'] ?? '';
        
        if (!$id || empty($nombre) || empty($unidadMedida)) {
            $this->json(['error' => 'Datos incompletos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE ingredientes 
                SET nombre = ?, unidad_medida = ?, costo_unitario = ?, proveedor = ?
                WHERE id = ?
            ");
            $stmt->execute([$nombre, $unidadMedida, $costoUnitario, $proveedor, $id]);
            
            $this->logAction('actualizar_ingrediente', 'ingredientes', "Ingrediente actualizado: {$nombre}");
            $this->json(['success' => true, 'message' => 'Ingrediente actualizado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al actualizar ingrediente: ' . $e->getMessage()], 500);
        }
    }
    
    public function getIngredient($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM ingredientes WHERE id = ?");
            $stmt->execute([$id]);
            $ingrediente = $stmt->fetch();
            
            if (!$ingrediente) {
                $this->json(['error' => 'Ingrediente no encontrado'], 404);
            }
            
            $this->json(['success' => true, 'data' => $ingrediente]);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al obtener ingrediente: ' . $e->getMessage()], 500);
        }
    }
    
    public function toggleIngredient() {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = $_POST['id'] ?? 0;
        $activo = $_POST['activo'] ?? 0;
        
        if (!$id) {
            $this->json(['error' => 'ID requerido'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE ingredientes SET activo = ? WHERE id = ?");
            $stmt->execute([$activo, $id]);
            
            $accion = $activo ? 'activado' : 'suspendido';
            $this->logAction('toggle_ingrediente', 'ingredientes', "Ingrediente {$accion}: ID {$id}");
            $this->json(['success' => true, 'message' => "Ingrediente {$accion} correctamente"]);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al actualizar estado: ' . $e->getMessage()], 500);
        }
    }
    
    public function deleteIngredient() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = $_POST['id'] ?? 0;
        
        if (!$id) {
            $this->json(['error' => 'ID requerido'], 400);
        }
        
        try {
            // Verificar si el ingrediente está en uso
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM receta_ingredientes WHERE ingrediente_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                $this->json(['error' => 'No se puede eliminar. El ingrediente está siendo usado en recetas'], 400);
            }
            
            $stmt = $this->db->prepare("DELETE FROM ingredientes WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->logAction('eliminar_ingrediente', 'ingredientes', "Ingrediente eliminado: ID {$id}");
            $this->json(['success' => true, 'message' => 'Ingrediente eliminado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al eliminar ingrediente: ' . $e->getMessage()], 500);
        }
    }
}
