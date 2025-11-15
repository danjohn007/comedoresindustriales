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
    
    public function forgotPassword() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        $data = [
            'title' => 'Recuperar Contraseña',
            'success' => $_SESSION['recovery_success'] ?? null,
            'error' => $_SESSION['recovery_error'] ?? null
        ];
        
        unset($_SESSION['recovery_success'], $_SESSION['recovery_error']);
        
        $this->view('auth/forgot-password', $data);
    }
    
    public function sendPasswordReset() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/forgot-password');
        }
        
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            $_SESSION['recovery_error'] = 'Por favor ingrese su correo electrónico';
            $this->redirect('/forgot-password');
        }
        
        try {
            // Check if user exists
            $stmt = $this->db->prepare("SELECT id, email, nombre_completo FROM usuarios WHERE email = ? AND activo = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Generate token
                $token = bin2hex(random_bytes(32));
                $expira_en = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store token
                $stmt = $this->db->prepare("
                    INSERT INTO password_resets (email, token, expira_en) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$email, $token, $expira_en]);
                
                // Send email
                $resetLink = Router::url('/reset-password?token=' . $token);
                $this->sendPasswordResetEmail($user['email'], $user['nombre_completo'], $resetLink);
                
                $this->logAction('solicitar_recuperacion', 'auth', 'Solicitud de recuperación de contraseña: ' . $email);
            }
            
            // Always show success message (security best practice)
            $_SESSION['recovery_success'] = 'Si el correo está registrado, recibirá un enlace de recuperación';
            $this->redirect('/forgot-password');
            
        } catch (Exception $e) {
            $_SESSION['recovery_error'] = 'Error al procesar la solicitud';
            $this->redirect('/forgot-password');
        }
    }
    
    public function resetPassword() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['login_error'] = 'Token inválido';
            $this->redirect('/login');
        }
        
        // Verify token
        try {
            $stmt = $this->db->prepare("
                SELECT email FROM password_resets 
                WHERE token = ? AND expira_en > NOW() AND usado = 0
            ");
            $stmt->execute([$token]);
            $reset = $stmt->fetch();
            
            if (!$reset) {
                $_SESSION['login_error'] = 'Token inválido o expirado';
                $this->redirect('/login');
            }
            
            $data = [
                'title' => 'Restablecer Contraseña',
                'token' => $token,
                'error' => $_SESSION['reset_error'] ?? null
            ];
            
            unset($_SESSION['reset_error']);
            
            $this->view('auth/reset-password', $data);
            
        } catch (Exception $e) {
            $_SESSION['login_error'] = 'Error al procesar la solicitud';
            $this->redirect('/login');
        }
    }
    
    public function doResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($token) || empty($password) || empty($confirmPassword)) {
            $_SESSION['reset_error'] = 'Por favor complete todos los campos';
            $this->redirect('/reset-password?token=' . $token);
        }
        
        if ($password !== $confirmPassword) {
            $_SESSION['reset_error'] = 'Las contraseñas no coinciden';
            $this->redirect('/reset-password?token=' . $token);
        }
        
        if (strlen($password) < 6) {
            $_SESSION['reset_error'] = 'La contraseña debe tener al menos 6 caracteres';
            $this->redirect('/reset-password?token=' . $token);
        }
        
        try {
            // Verify token
            $stmt = $this->db->prepare("
                SELECT email FROM password_resets 
                WHERE token = ? AND expira_en > NOW() AND usado = 0
            ");
            $stmt->execute([$token]);
            $reset = $stmt->fetch();
            
            if (!$reset) {
                $_SESSION['login_error'] = 'Token inválido o expirado';
                $this->redirect('/login');
            }
            
            // Update password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $reset['email']]);
            
            // Mark token as used
            $stmt = $this->db->prepare("UPDATE password_resets SET usado = 1 WHERE token = ?");
            $stmt->execute([$token]);
            
            $this->logAction('restablecer_password', 'auth', 'Contraseña restablecida: ' . $reset['email']);
            
            $_SESSION['login_error'] = 'Contraseña restablecida exitosamente. Por favor inicie sesión';
            $this->redirect('/login');
            
        } catch (Exception $e) {
            $_SESSION['reset_error'] = 'Error al restablecer la contraseña';
            $this->redirect('/reset-password?token=' . $token);
        }
    }
    
    private function sendPasswordResetEmail($to, $name, $resetLink) {
        try {
            // Get email configuration
            $stmt = $this->db->query("SELECT * FROM configuracion_correo WHERE activo = 1 LIMIT 1");
            $config = $stmt->fetch();
            
            if (!$config) {
                return false;
            }
            
            $subject = 'Recuperación de Contraseña - Sistema Comedores';
            $message = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; background-color: #f3f4f6; }
                        .button { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>Recuperación de Contraseña</h1>
                        </div>
                        <div class='content'>
                            <p>Hola {$name},</p>
                            <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
                            <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
                            <p style='text-align: center;'>
                                <a href='{$resetLink}' class='button'>Restablecer Contraseña</a>
                            </p>
                            <p>O copia y pega este enlace en tu navegador:</p>
                            <p style='word-break: break-all;'>{$resetLink}</p>
                            <p><strong>Este enlace expirará en 1 hora.</strong></p>
                            <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " Sistema de Comedores Industriales - Querétaro</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "From: {$config['from_name']} <{$config['from_email']}>\r\n";
            
            // Use mail() function (requires PHP mail configuration)
            return mail($to, $subject, $message, $headers);
            
        } catch (Exception $e) {
            error_log("Error sending password reset email: " . $e->getMessage());
            return false;
        }
    }
}
