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
        if (!isset($_SESSION['csrf_token'])) Security::generateToken();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
                die('Token inválido');
            }

            $password        = $_POST['password']        ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres.';
                include BASE_PATH . '/views/auth/register.php';
                return;
            }

            if ($password !== $passwordConfirm) {
                $error = 'Las contraseñas no coinciden.';
                include BASE_PATH . '/views/auth/register.php';
                return;
            }

            $dniRaw = trim(Security::sanitize($_POST['dni'] ?? ''));

            $data = [
                'institution_id' => 1,
                'username'   => Security::sanitize($_POST['username']),
                'email'      => Security::sanitize($_POST['email']),
                'password'   => $password,
                'first_name' => Security::sanitize($_POST['first_name']),
                'last_name'  => Security::sanitize($_POST['last_name']),
                'dni'        => $dniRaw !== '' ? $dniRaw : null,
                'phone'      => Security::sanitize($_POST['phone'] ?? '') ?: null
            ];

            if ($this->userModel->create($data)) {
                // Obtener el ID del usuario recién creado y asignar rol representante
                $db  = new Database();
                $row = $db->connect()->query(
                    "SELECT id FROM users WHERE username = '" . $data['username'] . "' LIMIT 1"
                )->fetch();
                if ($row) {
                    $this->userModel->assignRole($row['id'], 5);
                }
                header('Location: ' . BASE_URL . '/?action=login&registered=1');
                exit;
            } else {
                $error = 'No se pudo crear la cuenta. El usuario, correo o cédula ya existe.';
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

                // Verificar si el docente es tutor (para navbar)
                $_SESSION['is_tutor'] = false;
                if (in_array('docente', $_SESSION['roles'])) {
                    require_once BASE_PATH . '/models/Attendance.php';
                    $db = new Database();
                    $attModel = new Attendance($db);
                    $_SESSION['is_tutor'] = (bool)$attModel->getTutorCourseId($user['id']);
                }

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