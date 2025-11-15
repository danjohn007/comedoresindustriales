<?php
/**
 * Profile Controller
 * User profile management and password change
 */

class ProfileController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $stmt = $this->db->prepare("
            SELECT id, username, email, nombre_completo, rol, activo, 
                   fecha_creacion, ultimo_acceso, imagen_perfil
            FROM usuarios
            WHERE id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $_SESSION['error'] = 'Usuario no encontrado';
            $this->redirect('/dashboard');
        }
        
        $data = [
            'title' => 'Mi Perfil',
            'user' => $user,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('profile/index', $data);
    }
    
    public function changePassword() {
        $this->requireAuth();
        
        $data = [
            'title' => 'Cambiar Contraseña',
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('profile/change-password', $data);
    }
    
    public function updatePassword() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile/change-password');
        }
        
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validations
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'Todos los campos son requeridos';
            $this->redirect('/profile/change-password');
        }
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Las contraseñas nuevas no coinciden';
            $this->redirect('/profile/change-password');
        }
        
        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
            $this->redirect('/profile/change-password');
        }
        
        try {
            // Verify current password
            $stmt = $this->db->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = 'La contraseña actual es incorrecta';
                $this->redirect('/profile/change-password');
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                UPDATE usuarios 
                SET password = ?
                WHERE id = ?
            ");
            $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
            
            $this->logAction('cambiar_contraseña', 'perfil', 'Contraseña actualizada');
            $_SESSION['success'] = 'Contraseña actualizada correctamente';
            $this->redirect('/profile');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al actualizar contraseña: ' . $e->getMessage();
            $this->redirect('/profile/change-password');
        }
    }
    
    public function uploadImage() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['imagen_perfil']) || $_FILES['imagen_perfil']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['error'] = 'No se seleccionó ningún archivo';
            $this->redirect('/profile');
        }
        
        $file = $_FILES['imagen_perfil'];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Error al subir el archivo';
            $this->redirect('/profile');
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $_SESSION['error'] = 'Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG y GIF';
            $this->redirect('/profile');
        }
        
        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'El archivo es demasiado grande. Tamaño máximo: 5MB';
            $this->redirect('/profile');
        }
        
        try {
            // Create uploads directory if it doesn't exist
            $uploadDir = PUBLIC_PATH . '/uploads/profiles';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;
            
            // Get current image to delete it
            $stmt = $this->db->prepare("SELECT imagen_perfil FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                $_SESSION['error'] = 'Error al guardar el archivo';
                $this->redirect('/profile');
            }
            
            // Update database
            $imageUrl = '/uploads/profiles/' . $filename;
            $stmt = $this->db->prepare("
                UPDATE usuarios 
                SET imagen_perfil = ?
                WHERE id = ?
            ");
            $stmt->execute([$imageUrl, $_SESSION['user_id']]);
            
            // Delete old image if exists
            if ($user && $user['imagen_perfil']) {
                $oldFile = PUBLIC_PATH . $user['imagen_perfil'];
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }
            
            $this->logAction('actualizar_imagen_perfil', 'perfil', 'Imagen de perfil actualizada');
            $_SESSION['success'] = 'Imagen de perfil actualizada correctamente';
            $this->redirect('/profile');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al actualizar imagen: ' . $e->getMessage();
            $this->redirect('/profile');
        }
    }
    
    public function deleteImage() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }
        
        try {
            // Get current image
            $stmt = $this->db->prepare("SELECT imagen_perfil FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            // Update database
            $stmt = $this->db->prepare("
                UPDATE usuarios 
                SET imagen_perfil = NULL
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            
            // Delete file if exists
            if ($user && $user['imagen_perfil']) {
                $oldFile = PUBLIC_PATH . $user['imagen_perfil'];
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }
            
            $this->logAction('eliminar_imagen_perfil', 'perfil', 'Imagen de perfil eliminada');
            $_SESSION['success'] = 'Imagen de perfil eliminada correctamente';
            $this->redirect('/profile');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar imagen: ' . $e->getMessage();
            $this->redirect('/profile');
        }
    }
}
