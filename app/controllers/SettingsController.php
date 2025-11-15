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
            // Handle logo file upload
            if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = PUBLIC_PATH . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileExtension = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];
                
                if (in_array($fileExtension, $allowedExtensions) && $_FILES['logo_file']['size'] <= 2097152) { // 2MB max
                    $fileName = 'logo_' . time() . '.' . $fileExtension;
                    $filePath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $filePath)) {
                        // Use BASE_URL to ensure correct path
                        $logoPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $uploadDir) . $fileName;
                        // Normalize path separators
                        $logoPath = str_replace('\\', '/', $logoPath);
                        $_POST['logo_sistema'] = $logoPath;
                    }
                }
            }
            
            foreach ($_POST as $key => $value) {
                if ($key === 'csrf_token' || $key === 'logo_file') continue;
                
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
            'users' => $users,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];
        
        unset($_SESSION['success'], $_SESSION['error']);
        
        $this->view('settings/users', $data);
    }
    
    public function createUser() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $rol = $_POST['rol'] ?? 'operativo';
        
        if (empty($username) || empty($email) || empty($password) || empty($nombreCompleto)) {
            $this->json(['error' => 'Todos los campos son requeridos'], 400);
        }
        
        if (strlen($password) < 6) {
            $this->json(['error' => 'La contraseña debe tener al menos 6 caracteres'], 400);
        }
        
        try {
            // Check if username or email already exists
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $this->json(['error' => 'El usuario o email ya existe'], 400);
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (username, email, password, nombre_completo, rol, activo)
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([$username, $email, $hashedPassword, $nombreCompleto, $rol]);
            
            $this->logAction('crear_usuario', 'usuarios', "Usuario creado: {$username}");
            $this->json(['success' => true, 'message' => 'Usuario creado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al crear usuario: ' . $e->getMessage()], 500);
        }
    }
    
    public function getUser($id) {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, nombre_completo, rol, activo, fecha_creacion, ultimo_acceso
                FROM usuarios WHERE id = ?
            ");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->json(['error' => 'Usuario no encontrado'], 404);
            }
            
            $this->json(['success' => true, 'data' => $user]);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al obtener usuario: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateUser() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = $_POST['id'] ?? 0;
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
        $rol = $_POST['rol'] ?? 'operativo';
        $activo = $_POST['activo'] ?? 1;
        
        if (!$id || empty($username) || empty($email) || empty($nombreCompleto)) {
            $this->json(['error' => 'Datos incompletos'], 400);
        }
        
        try {
            // Check if username or email already exists for another user
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$username, $email, $id]);
            if ($stmt->fetch()) {
                $this->json(['error' => 'El usuario o email ya existe'], 400);
            }
            
            $stmt = $this->db->prepare("
                UPDATE usuarios 
                SET username = ?, email = ?, nombre_completo = ?, rol = ?, activo = ?
                WHERE id = ?
            ");
            $stmt->execute([$username, $email, $nombreCompleto, $rol, $activo, $id]);
            
            $this->logAction('actualizar_usuario', 'usuarios', "Usuario actualizado: {$username}");
            $this->json(['success' => true, 'message' => 'Usuario actualizado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al actualizar usuario: ' . $e->getMessage()], 500);
        }
    }
    
    public function deleteUser() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = $_POST['id'] ?? 0;
        
        if (!$id) {
            $this->json(['error' => 'ID requerido'], 400);
        }
        
        // Prevent deleting current user
        if ($id == $_SESSION['user_id']) {
            $this->json(['error' => 'No puede eliminar su propio usuario'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->logAction('eliminar_usuario', 'usuarios', "Usuario eliminado: ID {$id}");
            $this->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al eliminar usuario: ' . $e->getMessage()], 500);
        }
    }
    
    public function comedores() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        $stmt = $this->db->query("SELECT * FROM comedores ORDER BY nombre");
        $comedores = $stmt->fetchAll();
        
        $data = [
            'title' => 'Gestión de Comedores',
            'comedores' => $comedores,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];
        
        unset($_SESSION['success'], $_SESSION['error']);
        
        $this->view('settings/comedores', $data);
    }
    
    public function createComedor() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $ciudad = trim($_POST['ciudad'] ?? 'Querétaro');
        $estado = trim($_POST['estado'] ?? 'Querétaro');
        $capacidadTotal = intval($_POST['capacidad_total'] ?? 0);
        $turnosActivos = $_POST['turnos_activos'] ?? 'matutino,vespertino,nocturno';
        
        if (empty($nombre) || empty($ubicacion) || $capacidadTotal <= 0) {
            $this->json(['error' => 'Todos los campos son requeridos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO comedores (nombre, ubicacion, ciudad, estado, capacidad_total, turnos_activos, activo)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([$nombre, $ubicacion, $ciudad, $estado, $capacidadTotal, $turnosActivos]);
            
            $this->logAction('crear_comedor', 'comedores', "Comedor creado: {$nombre}");
            $this->json(['success' => true, 'message' => 'Comedor creado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al crear comedor: ' . $e->getMessage()], 500);
        }
    }
    
    public function getComedor($id) {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM comedores WHERE id = ?");
            $stmt->execute([$id]);
            $comedor = $stmt->fetch();
            
            if (!$comedor) {
                $this->json(['error' => 'Comedor no encontrado'], 404);
            }
            
            $this->json(['success' => true, 'data' => $comedor]);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al obtener comedor: ' . $e->getMessage()], 500);
        }
    }
    
    public function updateComedor() {
        $this->requireAuth();
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $id = $_POST['id'] ?? 0;
        $nombre = trim($_POST['nombre'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $ciudad = trim($_POST['ciudad'] ?? 'Querétaro');
        $estado = trim($_POST['estado'] ?? 'Querétaro');
        $capacidadTotal = intval($_POST['capacidad_total'] ?? 0);
        $turnosActivos = $_POST['turnos_activos'] ?? 'matutino,vespertino,nocturno';
        $activo = $_POST['activo'] ?? 1;
        
        if (!$id || empty($nombre) || empty($ubicacion) || $capacidadTotal <= 0) {
            $this->json(['error' => 'Datos incompletos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE comedores 
                SET nombre = ?, ubicacion = ?, ciudad = ?, estado = ?, 
                    capacidad_total = ?, turnos_activos = ?, activo = ?
                WHERE id = ?
            ");
            $stmt->execute([$nombre, $ubicacion, $ciudad, $estado, $capacidadTotal, $turnosActivos, $activo, $id]);
            
            $this->logAction('actualizar_comedor', 'comedores', "Comedor actualizado: {$nombre}");
            $this->json(['success' => true, 'message' => 'Comedor actualizado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al actualizar comedor: ' . $e->getMessage()], 500);
        }
    }
    
    public function deleteComedor() {
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
            // Check if comedor is being used
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ordenes_produccion WHERE comedor_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                $this->json(['error' => 'No se puede eliminar. El comedor tiene órdenes de producción asociadas'], 400);
            }
            
            $stmt = $this->db->prepare("DELETE FROM comedores WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->logAction('eliminar_comedor', 'comedores', "Comedor eliminado: ID {$id}");
            $this->json(['success' => true, 'message' => 'Comedor eliminado correctamente']);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Error al eliminar comedor: ' . $e->getMessage()], 500);
        }
    }
    
    public function ingredients() {
        $this->requireAuth();
        $this->requireRole(['admin', 'chef']);
        
        $stmt = $this->db->query("SELECT * FROM ingredientes ORDER BY nombre");
        $ingredientes = $stmt->fetchAll();
        
        // Get suppliers for dropdown
        $stmt = $this->db->query("SELECT id, nombre FROM proveedores WHERE activo = 1 ORDER BY nombre");
        $proveedores = $stmt->fetchAll();
        
        $data = [
            'title' => 'Catálogo de Ingredientes',
            'ingredientes' => $ingredientes,
            'proveedores' => $proveedores
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
        $proveedorId = !empty($_POST['proveedor_id']) ? intval($_POST['proveedor_id']) : null;
        
        if (empty($nombre) || empty($unidadMedida)) {
            $this->json(['error' => 'Nombre y unidad de medida son requeridos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO ingredientes (nombre, unidad_medida, costo_unitario, proveedor_id, activo)
                VALUES (?, ?, ?, ?, 1)
            ");
            $stmt->execute([$nombre, $unidadMedida, $costoUnitario, $proveedorId]);
            
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
        $proveedorId = !empty($_POST['proveedor_id']) ? intval($_POST['proveedor_id']) : null;
        
        if (!$id || empty($nombre) || empty($unidadMedida)) {
            $this->json(['error' => 'Datos incompletos'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE ingredientes 
                SET nombre = ?, unidad_medida = ?, costo_unitario = ?, proveedor_id = ?
                WHERE id = ?
            ");
            $stmt->execute([$nombre, $unidadMedida, $costoUnitario, $proveedorId, $id]);
            
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
