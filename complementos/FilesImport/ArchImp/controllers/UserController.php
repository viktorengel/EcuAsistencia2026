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
            if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
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

            // Verificar email único (solo si se ingresó)
            if (!empty($_POST['email']) && $this->userModel->findByEmail($_POST['email'])) {
                $errors[] = "El email ya está registrado";
            }

            // Validar cédula ecuatoriana (si no es extranjero y hay valor)
            $esExtranjero = !empty($_POST['es_extranjero']);
            $dniValue     = null;
            if (!$esExtranjero && !empty($_POST['dni'])) {
                $ced = preg_replace('/\D/', '', $_POST['dni']);
                if (!self::validarCedulaEcuador($ced)) {
                    $errors[] = "Cédula ecuatoriana inválida";
                } else {
                    $dniValue = $ced;
                }
            } elseif ($esExtranjero && !empty($_POST['passport'])) {
                $dniValue = Security::sanitize($_POST['passport']);
            }

            // Validar teléfono Ecuador
            $phoneValue = null;
            if (!empty($_POST['phone'])) {
                $tel = preg_replace('/[\s\-]/', '', $_POST['phone']);
                if (!preg_match('/^09\d{8}$/', $tel) && !preg_match('/^0[2-7]\d{7}$/', $tel)) {
                    $errors[] = "Teléfono inválido. Celular: 09XXXXXXXX · Fijo: 0XXXXXXXX";
                } else {
                    $phoneValue = $tel;
                }
            }

            if (!empty($errors)) {
                $roles = $this->roleModel->getAll();
                include BASE_PATH . '/views/users/create.php';
                return;
            }

            // Crear usuario
            $userData = [
                'institution_id' => $_SESSION['institution_id'],
                'username'   => Security::sanitize($_POST['username']),
                'email'      => !empty($_POST['email']) ? Security::sanitize($_POST['email']) : null,
                'password'   => $_POST['password'],
                'first_name' => Security::sanitize($_POST['first_name']),
                'last_name'  => Security::sanitize($_POST['last_name']),
                'dni'        => $dniValue,
                'phone'      => $phoneValue,
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

            // No permitir eliminar al administrador del sistema
            $roles = $this->userModel->getUserRoles($userId);
            if (in_array('administrador', $roles)) {
                $f = !empty($_GET['filter_role']) ? '&filter_role='.$_GET['filter_role'] : '';
                header('Location: ?action=users&error=admin_protected'.$f);
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
                    $f = !empty($_GET['filter_role']) ? '&filter_role='.$_GET['filter_role'] : '';
                    header('Location: ?action=users&deactivated=1'.$f);
                    exit;
                }
            } else {
                // Hard delete - eliminar completamente
                if ($this->userModel->delete($userId)) {
                    $f = !empty($_GET['filter_role']) ? '&filter_role='.$_GET['filter_role'] : '';
                    header('Location: ?action=users&deleted=1'.$f);
                    exit;
                }
            }

            $f = !empty($_GET['filter_role']) ? '&filter_role='.$_GET['filter_role'] : '';
            header('Location: ?action=users&error=delete_failed'.$f);
            exit;
        }
    }

    // ── Validación cédula Ecuador (algoritmo oficial) ─────────────────────
    public static function validarCedulaEcuador(string $cedula): bool {
        if (!preg_match('/^\d{10}$/', $cedula)) return false;
        $prov = (int)substr($cedula, 0, 2);
        if ($prov < 1 || $prov > 24) return false;
        $coef = [2,1,2,1,2,1,2,1,2];
        $suma = 0;
        for ($i = 0; $i < 9; $i++) {
            $r = (int)$cedula[$i] * $coef[$i];
            $suma += $r >= 10 ? $r - 9 : $r;
        }
        $residuo = $suma % 10;
        $digVer  = $residuo === 0 ? 0 : 10 - $residuo;
        return $digVer === (int)$cedula[9];
    }


    private function validateDniInput(string $raw, int $excludeId = 0): array {
        $raw = trim($raw);
        if ($raw === '') return ['value' => null, 'error' => null, 'warning' => null];

        $soloNumeros = ctype_digit($raw);
        $warning = null;

        if ($soloNumeros) {
            // Cédula Ecuador — guarda siempre, pero avisa si es inválida
            if (strlen($raw) !== 10) {
                return ['value' => null, 'error' => 'La cédula debe tener exactamente 10 dígitos', 'warning' => null];
            }
            $dniValue = $raw;
            if (!self::validarCedulaEcuador($raw)) {
                $warning = 'cedula_invalida'; // se guarda pero con marca
            }
        } else {
            // Pasaporte: formato estricto (esto sí bloquea, es formato puro)
            $upper = strtoupper($raw);
            if (!preg_match('/^[A-Z0-9]{4,12}$/', $upper)) {
                return ['value' => null, 'error' => 'Pasaporte inválido: solo letras mayúsculas y números, entre 4 y 12 caracteres, sin espacios', 'warning' => null];
            }
            $dniValue = $upper;
        }

        // Unicidad — error duro, no se puede duplicar
        if ($this->userModel->findByDni($dniValue, $excludeId)) {
            $tipo = $soloNumeros ? 'cédula' : 'número de pasaporte';
            return ['value' => null, 'error' => "Este $tipo ya está registrado en otro usuario", 'warning' => null];
        }

        return ['value' => $dniValue, 'error' => null, 'warning' => $warning];
    }

    // ── Crear usuario desde modal ─────────────────────────────────────────
    public function createFromModal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?action=users'); exit; }

        $errors = [];
        if (empty($_POST['username']))                                          $errors[] = "El usuario es obligatorio";
        if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido";
        if (empty($_POST['password']) || strlen($_POST['password']) < 6)       $errors[] = "La contraseña debe tener al menos 6 caracteres";
        if ($_POST['password'] !== $_POST['confirm_password'])                  $errors[] = "Las contraseñas no coinciden";
        if (empty($_POST['first_name']))                                        $errors[] = "El nombre es obligatorio";
        if (empty($_POST['last_name']))                                         $errors[] = "El apellido es obligatorio";
        if ($this->userModel->findByUsername($_POST['username']))               $errors[] = "El usuario ya está registrado";
        if (!empty($_POST['email']) && $this->userModel->findByEmail($_POST['email'])) $errors[] = "El email ya está registrado";

        $dniResult = $this->validateDniInput($_POST['dni'] ?? '');
        if ($dniResult['error']) $errors[] = $dniResult['error'];
        $dniValue = $dniResult['value'];

        $phoneValue = null;
        if (!empty($_POST['phone'])) {
            $t = preg_replace('/[\s\-]/', '', $_POST['phone']);
            if (!preg_match('/^09\d{8}$/', $t) && !preg_match('/^0[2-7]\d{7}$/', $t)) $errors[] = "Teléfono inválido";
            else $phoneValue = $t;
        }

        if (!empty($errors)) {
            $_SESSION['modal_create_errors'] = $errors;
            $_SESSION['modal_create_data']   = $_POST;
            $_SESSION['open_modal']          = 'create';
            $f = !empty($_GET['filter_role']) ? '&filter_role='.$_GET['filter_role'] : '';
            header('Location: ?action=users'.$f); exit;
        }

        $userData = [
            'institution_id' => $_SESSION['institution_id'],
            'username'   => Security::sanitize($_POST['username']),
            'email'      => !empty($_POST['email']) ? Security::sanitize($_POST['email']) : null,
            'password'   => $_POST['password'],
            'first_name' => Security::sanitize($_POST['first_name']),
            'last_name'  => Security::sanitize($_POST['last_name']),
            'dni'        => $dniValue,
            'phone'      => $phoneValue,
        ];

        if ($this->userModel->create($userData)) {
            $newId = $this->userModel->db->lastInsertId();
            $selectedRoles = [];
            if (!empty($_POST['roles'])) {
                foreach ($_POST['roles'] as $rid) {
                    $this->userModel->assignRole($newId, (int)$rid);
                    // Obtener nombre del rol
                    $stmtR = $this->userModel->db->prepare("SELECT name FROM roles WHERE id = :id");
                    $stmtR->execute([':id' => (int)$rid]);
                    $rName = $stmtR->fetchColumn();
                    if ($rName) $selectedRoles[] = $rName;
                }
            }
            // Mantener filtro activo: si el filtro actual coincide con algún rol asignado → mantenerlo
            // Si no coincide → cambiar al primer rol asignado; si no hay roles → sin filtro
            $currentFilter = $_GET['filter_role'] ?? '';
            if ($currentFilter !== '' && in_array($currentFilter, $selectedRoles)) {
                $filter = '&filter_role=' . $currentFilter;
            } elseif (!empty($selectedRoles)) {
                $filter = '&filter_role=' . $selectedRoles[0];
            } else {
                $filter = '';
            }
            $warnParam = ($dniResult['warning'] === 'cedula_invalida') ? '&dni_warn=1' : '';
            header('Location: ?action=users&created=1' . $filter . $warnParam); exit;
        }

        $_SESSION['modal_create_errors'] = ['Error al crear el usuario'];
        $_SESSION['modal_create_data']   = $_POST;
        $_SESSION['open_modal']          = 'create';
        $f = !empty($_GET['filter_role']) ? '&filter_role='.$_GET['filter_role'] : '';
        header('Location: ?action=users'.$f); exit;
    }

    // ── Editar usuario desde modal ────────────────────────────────────────
    public function editFromModal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?action=users'); exit; }

        $userId = (int)$_POST['user_id'];
        $user   = $this->userModel->findById($userId);
        if (!$user || $user['institution_id'] != $_SESSION['institution_id']) { header('Location: ?action=users&error=not_found'); exit; }

        $errors = [];
        if (empty($_POST['username']))                                          $errors[] = "El usuario es obligatorio";
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido";
        if (empty($_POST['first_name']))                                        $errors[] = "El nombre es obligatorio";
        if (empty($_POST['last_name']))                                         $errors[] = "El apellido es obligatorio";

        $existing = $this->userModel->findByUsername($_POST['username']);
        if ($existing && $existing['id'] != $userId) $errors[] = "El usuario ya está registrado";
        $existingEmail = $this->userModel->findByEmail($_POST['email']);
        if ($existingEmail && $existingEmail['id'] != $userId) $errors[] = "El email ya está registrado";

        if (!empty($_POST['password'])) {
            if (strlen($_POST['password']) < 6)               $errors[] = "La contraseña debe tener al menos 6 caracteres";
            if ($_POST['password'] !== $_POST['confirm_password']) $errors[] = "Las contraseñas no coinciden";
        }

        $dniResult = $this->validateDniInput($_POST['dni'] ?? '', $userId);
        if ($dniResult['error']) $errors[] = $dniResult['error'];
        $dniValue = $dniResult['value'];

        if (!empty($errors)) {
            $_SESSION['modal_edit_errors'] = $errors;
            $_SESSION['modal_edit_data']   = array_merge($_POST, ['user_id' => $userId]);
            $_SESSION['open_modal']        = 'edit';
            header('Location: ?action=users'); exit;
        }

        $updateData = [
            'id'         => $userId,
            'username'   => Security::sanitize($_POST['username']),
            'email'      => Security::sanitize($_POST['email']),
            'first_name' => Security::sanitize($_POST['first_name']),
            'last_name'  => Security::sanitize($_POST['last_name']),
            'dni'        => $dniValue,
            'phone'      => !empty($_POST['phone']) ? Security::sanitize($_POST['phone']) : null,
        ];
        if (!empty($_POST['password'])) $updateData['password'] = $_POST['password'];

        if ($this->userModel->update($updateData)) {
            $warnParam = ($dniResult['warning'] === 'cedula_invalida') ? '&dni_warn=1' : '';
            header('Location: ?action=users&updated=1' . $warnParam); exit;
        }

        $_SESSION['modal_edit_errors'] = ['Error al actualizar el usuario'];
        $_SESSION['modal_edit_data']   = array_merge($_POST, ['user_id' => $userId]);
        $_SESSION['open_modal']        = 'edit';
        header('Location: ?action=users'); exit;
    }

}