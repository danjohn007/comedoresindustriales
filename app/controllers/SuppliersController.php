<?php
/**
 * Suppliers Controller
 * Manages suppliers (proveedores) for ingredients
 */

class SuppliersController extends Controller {
    
    public function index() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'chef']);
        
        $stmt = $this->db->query("
            SELECT 
                id,
                nombre,
                contacto,
                telefono,
                email,
                direccion,
                ciudad,
                activo,
                fecha_creacion
            FROM proveedores
            ORDER BY nombre
        ");
        $proveedores = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Proveedores',
            'proveedores' => $proveedores
        ];
        
        $this->view('suppliers/index', $data);
    }
    
    public function create() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $contacto = trim($_POST['contacto'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $ciudad = trim($_POST['ciudad'] ?? '');
        
        if (!$nombre) {
            $this->json(['error' => 'El nombre es requerido'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, ciudad)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nombre, $contacto, $telefono, $email, $direccion, $ciudad]);
            
            $this->logAction('crear_proveedor', 'proveedores', "Proveedor creado: {$nombre}");
            $this->json(['success' => true, 'message' => 'Proveedor creado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al crear: ' . $e->getMessage()], 500);
        }
    }
    
    public function get($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'chef']);
        
        $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id = ?");
        $stmt->execute([$id]);
        $proveedor = $stmt->fetch();
        
        if (!$proveedor) {
            $this->json(['error' => 'Proveedor no encontrado'], 404);
        }
        
        $this->json(['success' => true, 'proveedor' => $proveedor]);
    }
    
    public function update() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $contacto = trim($_POST['contacto'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $ciudad = trim($_POST['ciudad'] ?? '');
        
        if (!$id || !$nombre) {
            $this->json(['error' => 'ID y nombre son requeridos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE proveedores 
                SET nombre = ?, contacto = ?, telefono = ?, email = ?, direccion = ?, ciudad = ?
                WHERE id = ?
            ");
            $stmt->execute([$nombre, $contacto, $telefono, $email, $direccion, $ciudad, $id]);
            
            $this->logAction('actualizar_proveedor', 'proveedores', "Proveedor actualizado: {$nombre}");
            $this->json(['success' => true, 'message' => 'Proveedor actualizado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }
    
    public function toggle() {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if (!$id) {
            $this->json(['error' => 'ID es requerido'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE proveedores SET activo = NOT activo WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->logAction('toggle_proveedor', 'proveedores', "Estado de proveedor cambiado: ID {$id}");
            $this->json(['success' => true, 'message' => 'Estado cambiado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al cambiar estado: ' . $e->getMessage()], 500);
        }
    }
    
    public function delete() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if (!$id) {
            $this->json(['error' => 'ID es requerido'], 400);
        }
        
        try {
            // Check if supplier is used by any ingredient
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ingredientes WHERE proveedor = (SELECT nombre FROM proveedores WHERE id = ?)");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                $this->json(['error' => 'No se puede eliminar. El proveedor está asociado a ingredientes'], 400);
            }
            
            $stmt = $this->db->prepare("DELETE FROM proveedores WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->logAction('eliminar_proveedor', 'proveedores', "Proveedor eliminado: ID {$id}");
            $this->json(['success' => true, 'message' => 'Proveedor eliminado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }
}
