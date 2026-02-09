<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Role.php';

class UserController {
    private $userModel;
    private $roleModel;

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $db = new Database();
        $this->userModel = new User($db);
        $this->roleModel = new Role($db);
    }

    public function index() {
        $users = $this->userModel->getAll();
        $roles = $this->roleModel->getAll();
        include BASE_PATH . '/views/users/index.php';
    }

    public function assignRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)$_POST['user_id'];
            $roleId = (int)$_POST['role_id'];
            
            $this->userModel->assignRole($userId, $roleId);
            header('Location: ?action=users&success=1');
            exit;
        }
    }

    public function removeRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)$_POST['user_id'];
            $roleId = (int)$_POST['role_id'];
            
            $this->userModel->removeRole($userId, $roleId);
            header('Location: ?action=users&removed=1');
            exit;
        }
    }
}