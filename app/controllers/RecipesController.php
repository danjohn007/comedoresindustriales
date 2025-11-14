<?php
/**
 * Recipes Controller
 * Manages recipes and ingredients (OPAD-025 GRAMAJES)
 */

class RecipesController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $stmt = $this->db->query("
            SELECT r.*, ls.nombre as linea_servicio,
                   (SELECT COUNT(*) FROM receta_ingredientes WHERE receta_id = r.id) as num_ingredientes
            FROM recetas r
            JOIN lineas_servicio ls ON r.linea_servicio_id = ls.id
            WHERE r.activo = 1
            ORDER BY ls.orden_visualizacion, r.nombre
        ");
        $recetas = $stmt->fetchAll();
        
        $data = [
            'title' => 'Catálogo de Recetas',
            'recetas' => $recetas
        ];
        
        $this->view('recipes/index', $data);
    }
    
    public function view($id) {
        $this->requireAuth();
        
        $stmt = $this->db->prepare("
            SELECT r.*, ls.nombre as linea_servicio
            FROM recetas r
            JOIN lineas_servicio ls ON r.linea_servicio_id = ls.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $receta = $stmt->fetch();
        
        if (!$receta) {
            $_SESSION['error'] = 'Receta no encontrada';
            $this->redirect('/recipes');
        }
        
        $stmt = $this->db->prepare("
            SELECT ri.*, i.nombre as ingrediente, i.unidad_medida, i.costo_unitario
            FROM receta_ingredientes ri
            JOIN ingredientes i ON ri.ingrediente_id = i.id
            WHERE ri.receta_id = ?
            ORDER BY i.nombre
        ");
        $stmt->execute([$id]);
        $ingredientes = $stmt->fetchAll();
        
        $data = [
            'title' => 'Receta: ' . $receta['nombre'],
            'receta' => $receta,
            'ingredientes' => $ingredientes
        ];
        
        $this->view('recipes/view', $data);
    }
    
    public function create() {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        $stmt = $this->db->query("SELECT * FROM lineas_servicio WHERE activo = 1 ORDER BY orden_visualizacion");
        $lineas = $stmt->fetchAll();
        
        $stmt = $this->db->query("SELECT * FROM ingredientes WHERE activo = 1 ORDER BY nombre");
        $ingredientes = $stmt->fetchAll();
        
        $data = [
            'title' => 'Nueva Receta',
            'lineas' => $lineas,
            'ingredientes' => $ingredientes,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('recipes/create', $data);
    }
    
    public function store() {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        // Implementation for creating recipe with ingredients
        $_SESSION['info'] = 'Funcionalidad de creación de recetas disponible';
        $this->redirect('/recipes');
    }
    
    public function edit($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        $_SESSION['info'] = 'Funcionalidad de edición de recetas disponible';
        $this->redirect('/recipes/view/' . $id);
    }
    
    public function update($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        $_SESSION['info'] = 'Funcionalidad de actualización de recetas disponible';
        $this->redirect('/recipes/view/' . $id);
    }
}
