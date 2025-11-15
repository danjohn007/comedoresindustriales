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
    
    public function viewRecipe($id) {
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
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/recipes/create');
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $lineaServicioId = intval($_POST['linea_servicio_id'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $porcionesBase = intval($_POST['porciones_base'] ?? 100);
        $tiempoPreparacion = intval($_POST['tiempo_preparacion'] ?? 0);
        $ingredientes = $_POST['ingredientes'] ?? [];
        
        // Validar ingredientes mínimos
        if (count($ingredientes) < 2) {
            $_SESSION['error'] = 'Debe agregar al menos 2 ingredientes a la receta';
            $this->redirect('/recipes/create');
        }
        
        if (!$nombre || !$lineaServicioId) {
            $_SESSION['error'] = 'Nombre y línea de servicio son requeridos';
            $this->redirect('/recipes/create');
        }
        
        try {
            $this->db->beginTransaction();
            
            // Crear receta
            $stmt = $this->db->prepare("
                INSERT INTO recetas (nombre, linea_servicio_id, descripcion, porciones_base, tiempo_preparacion)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nombre, $lineaServicioId, $descripcion, $porcionesBase, $tiempoPreparacion ?: null]);
            
            $recetaId = $this->db->lastInsertId();
            
            // Agregar ingredientes
            $stmtIng = $this->db->prepare("
                INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad, notas)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            foreach ($ingredientes as $ing) {
                if (!empty($ing['ingrediente_id']) && !empty($ing['cantidad'])) {
                    $stmtIng->execute([
                        $recetaId,
                        $ing['ingrediente_id'],
                        $ing['cantidad'],
                        $ing['unidad'] ?? 'kg',
                        $ing['notas'] ?? ''
                    ]);
                }
            }
            
            $this->db->commit();
            
            $this->logAction('crear_receta', 'recetas', "Receta creada: {$nombre}");
            $_SESSION['success'] = 'Receta creada correctamente con ' . count($ingredientes) . ' ingredientes';
            $this->redirect('/recipes/view/' . $recetaId);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Error al crear receta: ' . $e->getMessage();
            $this->redirect('/recipes/create');
        }
    }
    
    public function edit($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        // Get recipe
        $stmt = $this->db->prepare("SELECT * FROM recetas WHERE id = ?");
        $stmt->execute([$id]);
        $receta = $stmt->fetch();
        
        if (!$receta) {
            $_SESSION['error'] = 'Receta no encontrada';
            $this->redirect('/recipes');
        }
        
        // Get lineas de servicio
        $stmt = $this->db->query("SELECT * FROM lineas_servicio WHERE activo = 1 ORDER BY orden_visualizacion");
        $lineas = $stmt->fetchAll();
        
        // Get all ingredients
        $stmt = $this->db->query("SELECT * FROM ingredientes WHERE activo = 1 ORDER BY nombre");
        $ingredientes = $stmt->fetchAll();
        
        // Get recipe ingredients
        $stmt = $this->db->prepare("
            SELECT ri.*, i.nombre as ingrediente_nombre
            FROM receta_ingredientes ri
            JOIN ingredientes i ON ri.ingrediente_id = i.id
            WHERE ri.receta_id = ?
            ORDER BY i.nombre
        ");
        $stmt->execute([$id]);
        $recetaIngredientes = $stmt->fetchAll();
        
        $data = [
            'title' => 'Editar Receta',
            'receta' => $receta,
            'lineas' => $lineas,
            'ingredientes' => $ingredientes,
            'recetaIngredientes' => $recetaIngredientes,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('recipes/edit', $data);
    }
    
    public function update($id) {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/recipes/edit/' . $id);
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $lineaServicioId = intval($_POST['linea_servicio_id'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $porcionesBase = intval($_POST['porciones_base'] ?? 100);
        $tiempoPreparacion = intval($_POST['tiempo_preparacion'] ?? 0);
        $ingredientesExistentes = $_POST['ingredientes_existentes'] ?? [];
        $ingredientesNuevos = $_POST['ingredientes_nuevos'] ?? [];
        $ingredientesEliminar = $_POST['ingredientes_eliminar'] ?? [];
        
        if (!$nombre || !$lineaServicioId) {
            $_SESSION['error'] = 'Nombre y línea de servicio son requeridos';
            $this->redirect('/recipes/edit/' . $id);
        }
        
        try {
            $this->db->beginTransaction();
            
            // Update recipe
            $stmt = $this->db->prepare("
                UPDATE recetas 
                SET nombre = ?, linea_servicio_id = ?, descripcion = ?, 
                    porciones_base = ?, tiempo_preparacion = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $nombre, $lineaServicioId, $descripcion, 
                $porcionesBase, $tiempoPreparacion ?: null, $id
            ]);
            
            // Delete marked ingredients
            if (!empty($ingredientesEliminar)) {
                $stmtDel = $this->db->prepare("DELETE FROM receta_ingredientes WHERE id = ? AND receta_id = ?");
                foreach ($ingredientesEliminar as $ingId) {
                    $stmtDel->execute([$ingId, $id]);
                }
            }
            
            // Update existing ingredients
            $stmtUpdate = $this->db->prepare("
                UPDATE receta_ingredientes 
                SET ingrediente_id = ?, cantidad = ?, unidad = ?, notas = ?
                WHERE id = ? AND receta_id = ?
            ");
            
            foreach ($ingredientesExistentes as $ingId => $ing) {
                if (!empty($ing['ingrediente_id']) && !empty($ing['cantidad'])) {
                    $stmtUpdate->execute([
                        $ing['ingrediente_id'],
                        $ing['cantidad'],
                        $ing['unidad'] ?? 'kg',
                        $ing['notas'] ?? '',
                        $ingId,
                        $id
                    ]);
                }
            }
            
            // Add new ingredients
            $stmtInsert = $this->db->prepare("
                INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad, notas)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            foreach ($ingredientesNuevos as $ing) {
                if (!empty($ing['ingrediente_id']) && !empty($ing['cantidad'])) {
                    $stmtInsert->execute([
                        $id,
                        $ing['ingrediente_id'],
                        $ing['cantidad'],
                        $ing['unidad'] ?? 'kg',
                        $ing['notas'] ?? ''
                    ]);
                }
            }
            
            $this->db->commit();
            
            $this->logAction('actualizar_receta', 'recetas', "Receta actualizada: {$nombre}");
            $_SESSION['success'] = 'Receta e ingredientes actualizados correctamente';
            $this->redirect('/recipes/view/' . $id);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
            $this->redirect('/recipes/edit/' . $id);
        }
    }
}
