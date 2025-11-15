<?php
/**
 * Base Controller Class
 * All controllers extend from this class
 */

class Controller {
    protected $db;
    
    public function __construct() {
        require_once CONFIG_PATH . '/Database.php';
        $this->db = Database::getInstance()->getConnection();
        
        // Configure and start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            // Configure session parameters before starting session
            session_set_cookie_params([
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }
    
    /**
     * Render a view
     */
    protected function view($viewPath, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Build view file path
        $viewFile = APP_PATH . '/views/' . $viewPath . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: $viewFile");
        }
    }
    
    /**
     * Redirect to a URL
     */
    protected function redirect($path) {
        header('Location: ' . Router::url($path));
        exit;
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Check if user is logged in
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }
    
    /**
     * Check if user has specific role
     */
    protected function requireRole($roles) {
        $this->requireAuth();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        if (!in_array($_SESSION['user_role'], $roles)) {
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Check if current user has read-only permissions (cliente role)
     */
    protected function isReadOnly() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'cliente';
    }
    
    /**
     * Deny access for read-only users
     */
    protected function denyReadOnly() {
        if ($this->isReadOnly()) {
            $_SESSION['error'] = 'No tiene permisos para realizar esta acción';
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Get current user data
     */
    protected function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        $stmt = $this->db->prepare("SELECT id, username, email, nombre_completo, rol FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    
    /**
     * Log system action
     */
    protected function logAction($accion, $modulo, $descripcion = '') {
        $userId = $_SESSION['user_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $this->db->prepare("
            INSERT INTO logs_sistema (usuario_id, accion, modulo, descripcion, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([$userId, $accion, $modulo, $descripcion, $ipAddress, $userAgent]);
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
    }
    
    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
