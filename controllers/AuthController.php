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

                // Verificar si el docente es tutor (para navbar)
                $_SESSION['is_tutor'] = false;
                if (in_array('docente', $_SESSION['roles'])) {
                    require_once BASE_PATH . '/models/Attendance.php';
                    $db2 = new Database();
                    $attModel = new Attendance($db2);
                    $_SESSION['is_tutor'] = (bool)$attModel->getTutorCourseId($user['id']);
                }

                // Recordar credenciales si el usuario marcó el checkbox
                if (!empty($_POST['remember_me'])) {
                    setcookie('ec_remember_user', $_POST['email'],    time() + (30 * 24 * 3600), '/');
                    setcookie('ec_remember_pass', $_POST['password'], time() + (30 * 24 * 3600), '/');
                } else {
                    // Limpiar cookies si desmarcó el checkbox
                    setcookie('ec_remember_user', '', time() - 3600, '/');
                    setcookie('ec_remember_pass', '', time() - 3600, '/');
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
        $message = null;
        $error   = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateToken($_POST['csrf_token'] ?? '')) die('Token inválido');

            $email = Security::sanitize($_POST['email']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Ingresa un correo electrónico válido.';
            } else {
                $user = $this->userModel->findByEmail($email);

                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $this->userModel->setResetToken($email, $token);
                    $resetLink = BASE_URL . "/?action=reset&token={$token}";

                    $body = "
                        <div style='font-family:Arial,sans-serif;max-width:480px;margin:auto;padding:30px;border:1px solid #e0e0e0;border-radius:8px;'>
                            <h2 style='color:#1a237e;'>🔐 Restablecer contraseña</h2>
                            <p style='color:#444;margin:16px 0;'>Haz clic en el botón para crear una nueva contraseña.<br>Este enlace expira en <strong>1 hora</strong>.</p>
                            <a href='{$resetLink}' style='display:inline-block;padding:12px 28px;background:#1a237e;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;'>Restablecer contraseña</a>
                            <p style='color:#888;font-size:12px;margin-top:20px;'>Si no solicitaste este cambio, ignora este correo.</p>
                        </div>";

                    $sent = Mailer::send($email, 'Restablecer contraseña — EcuAsist', $body);
                    if (!$sent) {
                        error_log("[EcuAsist] Fallo al enviar email de reset a: {$email}");
                    }
                }
                // Siempre mismo mensaje (evita enumerar usuarios)
                $message = '✓ Si el correo está registrado, recibirás un enlace en los próximos minutos.';
            }
        }

        include BASE_PATH . '/views/auth/forgot.php';
    }

    public function resetPassword() {
        $token    = Security::sanitize($_GET['token'] ?? '');
        $error    = null;
        $success  = false;

        // Validar token antes de mostrar el formulario
        $user = $this->userModel->validateResetToken($token);

        if (!$token || !$user) {
            $error = 'El enlace es inválido o ha expirado. Solicita uno nuevo.';
            include BASE_PATH . '/views/auth/reset.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateToken($_POST['csrf_token'] ?? '')) die('Token inválido');

            $pass    = $_POST['password']         ?? '';
            $confirm = $_POST['password_confirm'] ?? '';

            if (strlen($pass) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres.';
            } elseif ($pass !== $confirm) {
                $error = 'Las contraseñas no coinciden.';
            } else {
                $this->userModel->resetPassword($user['id'], $pass);
                header('Location: ' . BASE_URL . '/?action=login&reset=1');
                exit;
            }
        }

        include BASE_PATH . '/views/auth/reset.php';
    }
}