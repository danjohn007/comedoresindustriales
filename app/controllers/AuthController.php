<?php
/**
 * Authentication Controller
 * Handles user login, logout, and authentication
 */

class AuthController extends Controller {
    
    public function index() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        $this->redirect('/login');
    }
    
    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        $data = [
            'title' => 'Iniciar Sesión',
            'error' => $_SESSION['login_error'] ?? null
        ];
        
        unset($_SESSION['login_error']);
        
        $this->view('auth/login', $data);
    }
    
    public function doLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Por favor complete todos los campos';
            $this->redirect('/login');
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, password, email, nombre_completo, rol, activo 
                FROM usuarios 
                WHERE username = ? OR email = ?
            ");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                if ($user['activo'] != 1) {
                    $_SESSION['login_error'] = 'Usuario inactivo. Contacte al administrador';
                    $this->redirect('/login');
                }
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['nombre_completo'];
                $_SESSION['user_role'] = $user['rol'];
                
                // Update last access
                $updateStmt = $this->db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                // Log action
                $this->logAction('login', 'auth', 'Inicio de sesión exitoso');
                
                $this->redirect('/dashboard');
            } else {
                $_SESSION['login_error'] = 'Credenciales incorrectas';
                $this->redirect('/login');
            }
            
        } catch (Exception $e) {
            $_SESSION['login_error'] = 'Error al procesar la solicitud';
            $this->redirect('/login');
        }
    }
    
    public function logout() {
        $this->logAction('logout', 'auth', 'Cierre de sesión');
        
        // Destroy session
        session_destroy();
        
        $this->redirect('/login');
    }
}
