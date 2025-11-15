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
                   fecha_creacion, ultimo_acceso
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
            'user' => $user
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
}
