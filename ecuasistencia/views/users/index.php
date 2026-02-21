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
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        h2 { margin-bottom: 20px; color: #333; }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            margin: 2px;
        }
        .action-buttons {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>
<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Gestión de Usuarios
</div>

<div class="container">

    <div class="page-header" style="background:linear-gradient(135deg,#2e7d32,#388e3c);">
        <div class="ph-icon">👥</div>
        <div>
            <h1>Gestión de Usuarios</h1>
            <p>Administración de docentes, estudiantes y representantes</p>
        </div>
    </div>
    
        <div class="card">
            <?php if(isset($_GET['created'])): ?>
                <div class="success">✓ Usuario creado correctamente</div>
            <?php endif; ?>
            <?php if(isset($_GET['updated'])): ?>
                <div class="success">✓ Usuario actualizado correctamente</div>
            <?php endif; ?>
            <?php if(isset($_GET['deleted'])): ?>
                <div class="success">✓ Usuario eliminado correctamente</div>
            <?php endif; ?>
            <?php if(isset($_GET['deactivated'])): ?>
                <div class="info">ℹ️ Usuario desactivado (tiene registros de asistencia)</div>
            <?php endif; ?>
            <?php if(isset($_GET['success'])): ?>
                <div class="success">✓ Rol asignado correctamente</div>
            <?php endif; ?>
            <?php if(isset($_GET['removed'])): ?>
                <div class="success">✓ Rol eliminado correctamente</div>
            <?php endif; ?>
            <?php if(isset($_GET['error']) && $_GET['error'] === 'has_assignments'): ?>
                <div class="error">✗ No se puede eliminar el rol docente porque tiene asignaciones activas. Elimine primero las asignaciones.</div>
            <?php endif; ?>
            <?php if(isset($_GET['error']) && $_GET['error'] === 'not_found'): ?>
                <div class="error">✗ Usuario no encontrado</div>
            <?php endif; ?>
            <?php if(isset($_GET['error']) && $_GET['error'] === 'self_delete'): ?>
                <div class="error">✗ No puedes eliminar tu propia cuenta</div>
            <?php endif; ?>
            <?php if(isset($_GET['error']) && $_GET['error'] === 'delete_failed'): ?>
                <div class="error">✗ Error al eliminar el usuario</div>
            <?php endif; ?>

            <div class="header-actions">
                <h2>👥 Gestión de Usuarios</h2>
                <a href="?action=create_user" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">
                    + Crear Usuario
                </a>
            </div>
            
            <!-- Filtro por Rol - Botones -->
            <?php
            $roleIcons = ['docente'=>'👨‍🏫','estudiante'=>'👨‍🎓','inspector'=>'👁','autoridad'=>'⚙️','representante'=>'👨‍👩‍👧'];
            $roleColors = [
                'docente'      => ['active'=>'background:#007bff;color:white;border-color:#007bff;',      'outline'=>'color:#007bff;border-color:#007bff;'],
                'estudiante'   => ['active'=>'background:#28a745;color:white;border-color:#28a745;',     'outline'=>'color:#28a745;border-color:#28a745;'],
                'inspector'    => ['active'=>'background:#fd7e14;color:white;border-color:#fd7e14;',     'outline'=>'color:#fd7e14;border-color:#fd7e14;'],
                'autoridad'    => ['active'=>'background:#6f42c1;color:white;border-color:#6f42c1;',     'outline'=>'color:#6f42c1;border-color:#6f42c1;'],
                'representante'=> ['active'=>'background:#20c997;color:white;border-color:#20c997;',     'outline'=>'color:#20c997;border-color:#20c997;'],
            ];
            $currentFilter = $_GET['filter_role'] ?? '';
            ?>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
                <a href="?action=users"
                   style="padding:7px 16px;border-radius:20px;text-decoration:none;font-size:13px;font-weight:600;border:2px solid;
                          <?= $currentFilter==='' ? 'background:#343a40;color:white;border-color:#343a40;' : 'color:#343a40;border-color:#343a40;background:white;' ?>">
                    👥 Todos
                </a>
                <?php foreach($roles as $role):
                    $rn  = $role['name'];
                    $ico = $roleIcons[$rn] ?? '👤';
                    $col = $roleColors[$rn] ?? ['active'=>'background:#007bff;color:white;border-color:#007bff;','outline'=>'color:#007bff;border-color:#007bff;'];
                    $isActive = ($currentFilter === $rn);
                ?>
                <a href="?action=users&filter_role=<?= $rn ?>"
                   style="padding:7px 16px;border-radius:20px;text-decoration:none;font-size:13px;font-weight:600;border:2px solid;
                          <?= $isActive ? $col['active'] : $col['outline'].'background:white;' ?>">
                    <?= $ico ?> <?= ucfirst($rn) ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Roles Actuales</th>
                        <th>Asignar Rol</th>
                        <th>Acciones</th>
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
                            <td colspan="7" style="text-align:center;">No hay usuarios con este rol</td>
                        </tr>
                    <?php else: ?>
                         <?php 
                        $counter = 1;
                        foreach($filteredUsers as $user): 
                        ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= $user['last_name'] . ' ' . $user['first_name'] ?></td>
                            <td><?= $user['email'] ?></td>
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
                            <td class="action-buttons">
                                <a href="?action=edit_user&id=<?= $user['id'] ?>" 
                                   class="btn-warning btn-sm" 
                                   style="text-decoration: none; padding: 5px 10px; border-radius: 4px;">
                                    ✏️ Editar
                                </a>
                                <form method="POST" action="?action=delete_user" style="display: inline;" onsubmit="return confirmDelete(event, '<?= $user['last_name'] . ' ' . $user['first_name'] ?>')">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn-danger btn-sm">
                                        🗑️ Eliminar
                                    </button>
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
    function confirmDelete(event, userName) {
        event.preventDefault();
        
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
        
        modalContent.innerHTML = `
            <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Eliminar Usuario</h3>
            <p style="margin: 0 0 20px 0; color: #666;">
                ¿Está seguro de eliminar al usuario <strong>${userName}</strong>?
            </p>
            <p style="margin: 0 0 20px 0; color: #666; font-size: 14px;">
                <strong>Nota:</strong> Si el usuario tiene asistencias registradas, será desactivado en lugar de eliminado.
            </p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="cancelDeleteBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Cancelar
                </button>
                <button type="button" id="confirmDeleteBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Sí, Eliminar
                </button>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        const form = event.target;
        
        document.getElementById('confirmDeleteBtn').onclick = function() {
            document.body.removeChild(modal);
            form.submit();
        };
        
        document.getElementById('cancelDeleteBtn').onclick = function() {
            document.body.removeChild(modal);
        };
        
        return false;
    }

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