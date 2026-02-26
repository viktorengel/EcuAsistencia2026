<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/User.php';

class ProfileController {
    private $userModel;

    public function __construct() {
        Security::requireLogin();
        
        $db = new Database();
        $this->userModel = new User($db);
    }

    public function view() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $roles = $this->userModel->getUserRoles($_SESSION['user_id']);
        
        include BASE_PATH . '/views/profile/view.php';
    }

    public function edit() {
        $user = $this->userModel->findById($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => Security::sanitize($_POST['first_name']),
                'last_name' => Security::sanitize($_POST['last_name']),
                'phone' => Security::sanitize($_POST['phone']),
                'email' => Security::sanitize($_POST['email'])
            ];

            if ($this->userModel->updateProfile($_SESSION['user_id'], $data)) {
                header('Location: ?action=profile&success=1');
                exit;
            }
        }

        include BASE_PATH . '/views/profile/edit.php';
    }

    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            $user = $this->userModel->findById($_SESSION['user_id']);

            if (!Security::verifyPassword($currentPassword, $user['password'])) {
                $error = 'Contraseña actual incorrecta';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Las contraseñas no coinciden';
            } elseif (strlen($newPassword) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } else {
                if ($this->userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                    header('Location: ?action=profile&password_changed=1');
                    exit;
                }
            }
        }

        include BASE_PATH . '/views/profile/change_password.php';
    }
}