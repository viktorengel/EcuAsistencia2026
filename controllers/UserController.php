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
            $filter = isset($_GET['filter_role']) ? '&filter_role=' . $_GET['filter_role'] : '';
            header('Location: ?action=users&success=1' . $filter);
            exit;
        }
    }

    public function removeRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)$_POST['user_id'];
            $roleId = (int)$_POST['role_id'];
            
            // Verificar si el rol es "docente"
            $db = new Database();
            $stmt = $db->connect()->prepare("SELECT name FROM roles WHERE id = :role_id");
            $stmt->execute([':role_id' => $roleId]);
            $role = $stmt->fetch();
            
            if ($role && $role['name'] === 'docente') {
                // Eliminar asignaciones de materias
                $stmt = $db->connect()->prepare("DELETE FROM teacher_assignments WHERE teacher_id = :user_id");
                $stmt->execute([':user_id' => $userId]);
            }
            
            $this->userModel->removeRole($userId, $roleId);
            $filter = isset($_GET['filter_role']) ? '&filter_role=' . $_GET['filter_role'] : '';
            header('Location: ?action=users&removed=1' . $filter);
            exit;
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $roles = $this->roleModel->getAll();
            include BASE_PATH . '/views/users/create.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            // Validaciones
            if (empty($_POST['username'])) {
                $errors[] = "El nombre de usuario es obligatorio";
            }
            if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email inválido";
            }
            if (empty($_POST['password']) || strlen($_POST['password']) < 6) {
                $errors[] = "La contraseña debe tener al menos 6 caracteres";
            }
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $errors[] = "Las contraseñas no coinciden";
            }
            if (empty($_POST['first_name'])) {
                $errors[] = "El nombre es obligatorio";
            }
            if (empty($_POST['last_name'])) {
                $errors[] = "El apellido es obligatorio";
            }

            // Verificar username único
            if ($this->userModel->findByUsername($_POST['username'])) {
                $errors[] = "El nombre de usuario ya está registrado";
            }

            // Verificar email único
            if ($this->userModel->findByEmail($_POST['email'])) {
                $errors[] = "El email ya está registrado";
            }

            if (!empty($errors)) {
                $roles = $this->roleModel->getAll();
                include BASE_PATH . '/views/users/create.php';
                return;
            }

            // Crear usuario
            $userData = [
                'institution_id' => $_SESSION['institution_id'],
                'username' => Security::sanitize($_POST['username']),
                'email' => Security::sanitize($_POST['email']),
                'password' => $_POST['password'],
                'first_name' => Security::sanitize($_POST['first_name']),
                'last_name' => Security::sanitize($_POST['last_name']),
                'dni' => !empty($_POST['dni']) ? Security::sanitize($_POST['dni']) : null,
                'phone' => !empty($_POST['phone']) ? Security::sanitize($_POST['phone']) : null
            ];

            if ($this->userModel->create($userData)) {
                // Obtener ID del usuario recién creado
                $newUserId = $this->userModel->db->lastInsertId();

                // Asignar roles si se seleccionaron
                if (!empty($_POST['roles'])) {
                    foreach ($_POST['roles'] as $roleId) {
                        $this->userModel->assignRole($newUserId, (int)$roleId);
                    }
                }

                header('Location: ?action=users&created=1');
                exit;
            } else {
                $errors[] = "Error al crear el usuario";
                $roles = $this->roleModel->getAll();
                include BASE_PATH . '/views/users/create.php';
            }
        }
    }

    public function edit() {
        $userId = (int)$_GET['id'];
        $user = $this->userModel->findById($userId);

        if (!$user) {
            header('Location: ?action=users&error=not_found');
            exit;
        }

        // Verificar que pertenece a la misma institución
        if ($user['institution_id'] != $_SESSION['institution_id']) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $roles = $this->roleModel->getAll();
            $userRoles = $this->userModel->getUserRoleIds($userId);
            include BASE_PATH . '/views/users/edit.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            // Validaciones
            if (empty($_POST['username'])) {
                $errors[] = "El nombre de usuario es obligatorio";
            }
            if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email inválido";
            }
            if (empty($_POST['first_name'])) {
                $errors[] = "El nombre es obligatorio";
            }
            if (empty($_POST['last_name'])) {
                $errors[] = "El apellido es obligatorio";
            }

            // Verificar username único (excepto el actual)
            $existingUser = $this->userModel->findByUsername($_POST['username']);
            if ($existingUser && $existingUser['id'] != $userId) {
                $errors[] = "El nombre de usuario ya está registrado";
            }

            // Verificar email único (excepto el actual)
            $existingEmail = $this->userModel->findByEmail($_POST['email']);
            if ($existingEmail && $existingEmail['id'] != $userId) {
                $errors[] = "El email ya está registrado";
            }

            // Si hay nueva contraseña, validarla
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    $errors[] = "La contraseña debe tener al menos 6 caracteres";
                }
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    $errors[] = "Las contraseñas no coinciden";
                }
            }

            if (!empty($errors)) {
                $roles = $this->roleModel->getAll();
                $userRoles = $this->userModel->getUserRoleIds($userId);
                include BASE_PATH . '/views/users/edit.php';
                return;
            }

            // Actualizar usuario
            $updateData = [
                'id' => $userId,
                'username' => Security::sanitize($_POST['username']),
                'email' => Security::sanitize($_POST['email']),
                'first_name' => Security::sanitize($_POST['first_name']),
                'last_name' => Security::sanitize($_POST['last_name']),
                'dni' => !empty($_POST['dni']) ? Security::sanitize($_POST['dni']) : null,
                'phone' => !empty($_POST['phone']) ? Security::sanitize($_POST['phone']) : null
            ];

            // Solo actualizar contraseña si se proporcionó una nueva
            if (!empty($_POST['password'])) {
                $updateData['password'] = $_POST['password'];
            }

            if ($this->userModel->update($updateData)) {
                header('Location: ?action=users&updated=1');
                exit;
            } else {
                $errors[] = "Error al actualizar el usuario";
                $roles = $this->roleModel->getAll();
                $userRoles = $this->userModel->getUserRoleIds($userId);
                include BASE_PATH . '/views/users/edit.php';
            }
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)$_POST['user_id'];
            $user = $this->userModel->findById($userId);

            if (!$user) {
                header('Location: ?action=users&error=not_found');
                exit;
            }

            // Verificar que pertenece a la misma institución
            if ($user['institution_id'] != $_SESSION['institution_id']) {
                die('Acceso denegado');
            }

            // No permitir eliminar al propio usuario
            if ($userId == $_SESSION['user_id']) {
                header('Location: ?action=users&error=self_delete');
                exit;
            }

            // Verificar si tiene asistencias registradas
            $db = new Database();
            $stmt = $db->connect()->prepare("SELECT COUNT(*) as count FROM attendances WHERE student_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                // Soft delete - desactivar en lugar de eliminar
                if ($this->userModel->deactivate($userId)) {
                    header('Location: ?action=users&deactivated=1');
                    exit;
                }
            } else {
                // Hard delete - eliminar completamente
                if ($this->userModel->delete($userId)) {
                    header('Location: ?action=users&deleted=1');
                    exit;
                }
            }

            header('Location: ?action=users&error=delete_failed');
            exit;
        }
    }
}