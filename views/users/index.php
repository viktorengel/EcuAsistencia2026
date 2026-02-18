<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Gestión de Usuarios
</div>

<div class="container-wide">

    <!-- Mensajes flash -->
    <?php if(isset($_GET['created'])): ?>
        <div class="alert alert-success">✓ Usuario creado correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['updated'])): ?>
        <div class="alert alert-success">✓ Usuario actualizado correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['deleted'])): ?>
        <div class="alert alert-success">✓ Usuario eliminado correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['deactivated'])): ?>
        <div class="alert alert-info">ℹ️ Usuario desactivado (tiene registros de asistencia)</div>
    <?php endif; ?>
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ Rol asignado correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['removed'])): ?>
        <div class="alert alert-success">✓ Rol eliminado correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['error']) && $_GET['error'] === 'has_assignments'): ?>
        <div class="alert alert-danger">✗ No se puede eliminar el rol docente porque tiene asignaciones activas.</div>
    <?php endif; ?>
    <?php if(isset($_GET['error']) && $_GET['error'] === 'not_found'): ?>
        <div class="alert alert-danger">✗ Usuario no encontrado</div>
    <?php endif; ?>
    <?php if(isset($_GET['error']) && $_GET['error'] === 'self_delete'): ?>
        <div class="alert alert-danger">✗ No puedes eliminar tu propia cuenta</div>
    <?php endif; ?>
    <?php if(isset($_GET['error']) && $_GET['error'] === 'delete_failed'): ?>
        <div class="alert alert-danger">✗ Error al eliminar el usuario</div>
    <?php endif; ?>

    <!-- Header -->
    <div class="page-header">
        <div class="ph-icon">👥</div>
        <div>
            <h1>Gestión de Usuarios</h1>
            <p>Administra usuarios, roles y permisos del sistema</p>
        </div>
        <div class="ph-actions">
            <a href="?action=create_user" class="btn btn-success">+ Crear Usuario</a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters">
        <h3>🔍 Filtrar usuarios</h3>
        <?php if(isset($_GET['filter_role']) && $_GET['filter_role'] != ''): ?>
        <div class="filter-banner">
            <span>⚠️ Filtro activo: <strong><?= ucfirst(htmlspecialchars($_GET['filter_role'])) ?></strong></span>
            <a href="?action=users">✕ Limpiar</a>
        </div>
        <?php endif; ?>
        <form method="GET" action="">
            <input type="hidden" name="action" value="users">
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Filtrar por Rol</label>
                    <select name="filter_role" onchange="this.form.submit()">
                        <option value="">— Todos los roles —</option>
                        <?php foreach($roles as $role): ?>
                            <option value="<?= $role['name'] ?>" <?= (isset($_GET['filter_role']) && $_GET['filter_role'] == $role['name']) ? 'selected' : '' ?>>
                                <?= ucfirst($role['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div class="table-wrap">
        <?php
        $filteredUsers = $users;
        if(isset($_GET['filter_role']) && $_GET['filter_role'] != '') {
            $filterRole = $_GET['filter_role'];
            $filteredUsers = array_filter($users, function($user) use ($filterRole) {
                if($user['roles']) {
                    return in_array($filterRole, explode(',', $user['roles']));
                }
                return false;
            });
        }
        ?>
        <div class="table-info">
            <span>📋 <strong><?= count($filteredUsers) ?></strong> usuarios encontrados</span>
        </div>

        <?php if(empty($filteredUsers)): ?>
            <div class="empty-state">
                <div class="icon">👥</div>
                <p>No hay usuarios con este filtro</p>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Asignar Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; foreach($filteredUsers as $user): ?>
                <tr>
                    <td style="color:#999;"><?= $counter++ ?></td>
                    <td><strong><?= htmlspecialchars($user['last_name'] . ' ' . $user['first_name']) ?></strong></td>
                    <td style="color:#666;"><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <?php if($user['roles']):
                            $db2 = new Database();
                            $stmtR = $db2->connect()->prepare("SELECT r.id, r.name FROM roles r INNER JOIN user_roles ur ON r.id = ur.role_id WHERE ur.user_id = :uid ORDER BY r.name");
                            $stmtR->execute([':uid' => $user['id']]);
                            $userRolesData = $stmtR->fetchAll(PDO::FETCH_ASSOC);
                            foreach($userRolesData as $rd): ?>
                            <span class="badge badge-green" style="margin:2px;">
                                <?= ucfirst($rd['name']) ?>
                                <form method="POST" action="?action=remove_role<?= isset($_GET['filter_role']) ? '&filter_role='.$_GET['filter_role'] : '' ?>" style="display:inline;" onsubmit="return confirmRemoveRole(event,'<?= ucfirst($rd['name']) ?>','<?= htmlspecialchars($user['last_name'].' '.$user['first_name']) ?>')">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="role_id" value="<?= $rd['id'] ?>">
                                    <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;padding:0 0 0 4px;font-size:13px;line-height:1;" title="Quitar rol">×</button>
                                </form>
                            </span>
                        <?php endforeach;
                        else: ?>
                            <em style="color:#bbb;">Sin roles</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" action="?action=assign_role<?= isset($_GET['filter_role']) ? '&filter_role='.$_GET['filter_role'] : '' ?>" style="display:inline-flex;gap:6px;align-items:center;">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="role_id" required style="padding:5px 8px;border:1px solid #ccc;border-radius:6px;font-size:0.82rem;">
                                <option value="">Seleccionar...</option>
                                <?php foreach($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= ucfirst($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Asignar</button>
                        </form>
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="?action=edit_user&id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                        <form method="POST" action="?action=delete_user" style="display:inline;" onsubmit="return confirmDelete(event,'<?= htmlspecialchars($user['last_name'].' '.$user['first_name']) ?>')">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">🗑️ Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Eliminar Usuario -->
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box">
        <h3 style="color:#dc3545;">⚠️ Eliminar Usuario</h3>
        <p>¿Está seguro de eliminar a <strong id="modalDeleteName"></strong>?<br>
        <span style="font-size:0.82rem;color:#888;">Si tiene asistencias registradas, será desactivado en lugar de eliminado.</span></p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalDelete')">Cancelar</button>
            <button class="btn btn-danger" id="modalDeleteConfirm">Sí, Eliminar</button>
        </div>
    </div>
</div>

<!-- Modal Quitar Rol -->
<div class="modal-overlay" id="modalRemoveRole">
    <div class="modal-box">
        <h3 style="color:#dc3545;">⚠️ Eliminar Rol</h3>
        <p>¿Está seguro de eliminar el rol <strong id="modalRoleName"></strong> del usuario <strong id="modalRoleUser"></strong>?</p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalRemoveRole')">Cancelar</button>
            <button class="btn btn-danger" id="modalRemoveRoleConfirm">Sí, Eliminar</button>
        </div>
    </div>
</div>

<script>
function closeModal(id) { document.getElementById(id).classList.remove('on'); }

function confirmDelete(event, userName) {
    event.preventDefault();
    document.getElementById('modalDeleteName').textContent = userName;
    var form = event.target;
    document.getElementById('modalDeleteConfirm').onclick = function() { form.submit(); };
    document.getElementById('modalDelete').classList.add('on');
    return false;
}

function confirmRemoveRole(event, roleName, userName) {
    event.preventDefault();
    document.getElementById('modalRoleName').textContent = roleName;
    document.getElementById('modalRoleUser').textContent = userName;
    var form = event.target;
    document.getElementById('modalRemoveRoleConfirm').onclick = function() { form.submit(); };
    document.getElementById('modalRemoveRole').classList.add('on');
    return false;
}

// Cerrar modal al hacer click fuera
document.querySelectorAll('.modal-overlay').forEach(function(m) {
    m.addEventListener('click', function(e) { if(e.target === m) closeModal(m.id); });
});
</script>

</body>
</html>
