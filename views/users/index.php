<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            margin: 2px;
            position: relative;
        }
        .btn-remove-role {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            padding: 0;
            margin-left: 5px;
            line-height: 1;
        }
        .btn-remove-role:hover {
            color: #ff0000;
        }
        select, button { padding: 8px 12px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        h2 { margin-bottom: 20px; color: #333; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <?php if(isset($_GET['success'])): ?>
                <div class="success">✓ Rol asignado correctamente</div>
            <?php endif; ?>
            <?php if(isset($_GET['removed'])): ?>
                <div class="success">✓ Rol eliminado correctamente</div>
            <?php endif; ?>
            <?php if(isset($_GET['error']) && $_GET['error'] === 'has_assignments'): ?>
                <div class="error">✗ No se puede eliminar el rol docente porque tiene asignaciones activas. Elimine primero las asignaciones.</div>
            <?php endif; ?>

            <h2>Usuarios Registrados</h2>
            
            <!-- Filtro por Rol -->
            <form method="GET" action="" style="margin-bottom: 20px;">
                <input type="hidden" name="action" value="users">
                <div style="display: flex; gap: 15px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Filtrar por Rol</label>
                        <select name="filter_role" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            <option value="">Todos los roles</option>
                            <?php foreach($roles as $role): ?>
                                <option value="<?= $role['name'] ?>" <?= (isset($_GET['filter_role']) && $_GET['filter_role'] == $role['name']) ? 'selected' : '' ?>>
                                    <?= ucfirst($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if(isset($_GET['filter_role']) && $_GET['filter_role'] != ''): ?>
                    <div>
                        <a href="?action=users" style="padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">
                            Limpiar
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Cédula</th>
                        <th>Roles Actuales</th>
                        <th>Asignar Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Aplicar filtro
                    $filteredUsers = $users;
                    
                    if(isset($_GET['filter_role']) && $_GET['filter_role'] != '') {
                        $filterRole = $_GET['filter_role'];
                        $filteredUsers = array_filter($users, function($user) use ($filterRole) {
                            if($user['roles']) {
                                $userRoles = explode(',', $user['roles']);
                                return in_array($filterRole, $userRoles);
                            }
                            return false;
                        });
                    }
                    ?>
                    
                    <?php if(empty($filteredUsers)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">No hay usuarios con este rol</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($filteredUsers as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= $user['last_name'] . ' ' . $user['first_name'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['dni'] ?? '-' ?></td>
                            <td>
                                <?php if($user['roles']): ?>
                                    <?php 
                                    // Obtener IDs de roles con nombres
                                    $db = new Database();
                                    $sqlRoles = "SELECT r.id, r.name 
                                                FROM roles r 
                                                INNER JOIN user_roles ur ON r.id = ur.role_id 
                                                WHERE ur.user_id = :user_id
                                                ORDER BY r.name";
                                    $stmtRoles = $db->connect()->prepare($sqlRoles);
                                    $stmtRoles->execute([':user_id' => $user['id']]);
                                    $userRolesData = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach($userRolesData as $roleData): 
                                    ?>
                                        <span class="badge">
                                            <?= ucfirst($roleData['name']) ?>
                                            <form method="POST" action="?action=remove_role<?= isset($_GET['filter_role']) ? '&filter_role=' . $_GET['filter_role'] : '' ?>" style="display: inline;" onsubmit="return confirmRemoveRole(event, '<?= ucfirst($roleData['name']) ?>', '<?= $user['last_name'] . ' ' . $user['first_name'] ?>')">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <input type="hidden" name="role_id" value="<?= $roleData['id'] ?>">
                                                <button type="submit" class="btn-remove-role">×</button>
                                            </form>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <em>Sin roles</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" action="?action=assign_role<?= isset($_GET['filter_role']) ? '&filter_role=' . $_GET['filter_role'] : '' ?>" style="display: inline-flex; gap: 5px;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="role_id" required style="padding: 5px;">
                                        <option value="">Seleccionar...</option>
                                        <?php foreach($roles as $role): ?>
                                            <option value="<?= $role['id'] ?>"><?= ucfirst($role['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" style="padding: 5px 10px;">Asignar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function confirmRemoveRole(event, roleName, userName) {
        event.preventDefault();
        
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
        
        modalContent.innerHTML = `
            <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Eliminar Rol</h3>
            <p style="margin: 0 0 20px 0; color: #666;">
                ¿Está seguro de eliminar el rol <strong>${roleName}</strong> del usuario <strong>${userName}</strong>?
            </p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="cancelRemoveRoleBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Cancelar
                </button>
                <button type="button" id="confirmRemoveRoleBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Sí, Eliminar
                </button>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        const form = event.target;
        
        document.getElementById('confirmRemoveRoleBtn').onclick = function() {
            document.body.removeChild(modal);
            form.submit();
        };
        
        document.getElementById('cancelRemoveRoleBtn').onclick = function() {
            document.body.removeChild(modal);
        };
        
        return false;
    }
    </script>
</body>
</html>