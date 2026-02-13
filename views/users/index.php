<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge { display: inline-block; background: #28a745; color: white; padding: 3px 8px; border-radius: 3px; font-size: 11px; margin: 2px; }
        select, button { padding: 8px 12px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Gestión de Usuarios</h1>
        <div>
            <a href="?action=dashboard">← Dashboard</a>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <?php if(isset($_GET['success'])): ?>
                <div class="success">✓ Rol asignado correctamente</div>
            <?php endif; ?>
            <?php if(isset($_GET['removed'])): ?>
                <div class="success">✓ Rol eliminado correctamente</div>
            <?php endif; ?>

            <h2>Usuarios Registrados</h2>
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
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['last_name'] . ' ' . $user['first_name'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['dni'] ?? '-' ?></td>
                        <td>
                            <?php if($user['roles']): ?>
                                <?php 
                                $userRoles = explode(',', $user['roles']);
                                $userRoleIds = $this->userModel->getUserRoleIds($user['id']);
                                foreach($userRoles as $index => $role): 
                                    $roleId = $userRoleIds[$index] ?? null;
                                ?>
                                    <span class="badge">
                                        <?= ucfirst($role) ?>
                                        <form method="POST" action="?action=remove_role" style="display: inline;" onsubmit="return confirm('¿Eliminar el rol <?= ucfirst($role) ?>?')">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="role_id" value="<?= $roleId ?>">
                                            <button type="submit" class="btn-remove-role">×</button>
                                        </form>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <em>Sin roles</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="?action=assign_role" style="display: inline-flex; gap: 5px;">
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
                </tbody>
            </table>
        </div>
        <style>
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
        </style>
    </div>
</body>
</html>