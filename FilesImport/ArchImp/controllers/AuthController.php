<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/helpers/Mailer.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $db = new Database();
        $this->userModel = new User($db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
                die('Token inválido');
            }

            $data = [
                'institution_id' => 1,
                'username' => Security::sanitize($_POST['username']),
                'email' => Security::sanitize($_POST['email']),
                'password' => $_POST['password'],
                'first_name' => Security::sanitize($_POST['first_name']),
                'last_name' => Security::sanitize($_POST['last_name']),
                'dni' => Security::sanitize($_POST['dni'] ?? ''),
                'phone' => Security::sanitize($_POST['phone'] ?? '')
            ];

            if ($this->userModel->create($data)) {
                header('Location: ' . BASE_URL . '/?action=login&registered=1');
                exit;
            }
        }

        include BASE_PATH . '/views/auth/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
                die('Token inválido');
            }

            $emailOrUsername = Security::sanitize($_POST['email']);
            $password = $_POST['password'];

            // Buscar por email O username
            $user = $this->userModel->findByEmailOrUsername($emailOrUsername);

            if ($user && Security::verifyPassword($password, $user['password'])) {
                $_SESSION['user_id']        = $user['id'];
                $_SESSION['username']       = $user['username'];
                $_SESSION['first_name']     = $user['first_name'];
                $_SESSION['last_name']      = $user['last_name'];
                $_SESSION['institution_id'] = $user['institution_id'];
                $_SESSION['roles']          = $this->userModel->getUserRoles($user['id']);
                $_SESSION['is_superadmin']  = !empty($user['is_superadmin']);

                header('Location: ' . BASE_URL . '/?action=dashboard');
                exit;
            } else {
                $error = 'Credenciales incorrectas';
            }
        }

        include BASE_PATH . '/views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '/?action=login');
        exit;
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = Security::sanitize($_POST['email']);
            $user = $this->userModel->findByEmail($email);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $this->userModel->setResetToken($email, $token);

                $resetLink = BASE_URL . "/?action=reset&token={$token}";
                $body = "Haga clic aquí para restablecer su contraseña: <a href='{$resetLink}'>{$resetLink}</a>";
                
                Mailer::send($email, 'Restablecer contraseña', $body);
            }

            $message = 'Si el correo existe, recibirá un enlace de recuperación';
        }

        include BASE_PATH . '/views/auth/forgot.php';
    }

    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->userModel->validateResetToken($token);
            
            if ($user && $_POST['password'] === $_POST['password_confirm']) {
                $this->userModel->resetPassword($user['id'], $_POST['password']);
                header('Location: ' . BASE_URL . '/?action=login&reset=1');
                exit;
            }
        }

        include BASE_PATH . '/views/auth/reset.php';
    }
}