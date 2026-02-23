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
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 13px; }
        th { background: #f8f9fa; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #555; }
        .badge { display:inline-block; background:#28a745; color:white; padding:3px 8px; border-radius:3px; font-size:11px; margin:2px; position:relative; }
        .btn-remove-role { background:none; border:none; color:white; font-size:16px; font-weight:bold; cursor:pointer; padding:0; margin-left:5px; line-height:1; }
        .btn-remove-role:hover { color:#ff6b6b; }
        select, button { padding:8px 12px; border:1px solid #ddd; border-radius:4px; }
        button { background:#007bff; color:white; border:none; cursor:pointer; }
        button:hover { background:#0056b3; }
        .btn-danger  { background:#dc3545; } .btn-danger:hover  { background:#c82333; }
        .btn-success { background:#28a745; } .btn-success:hover { background:#218838; }
        .btn-warning { background:#ffc107; color:#212529; } .btn-warning:hover { background:#e0a800; }
        .success { background:#d4edda; color:#155724; padding:10px; border-radius:4px; margin-bottom:15px; }
        .error   { background:#f8d7da; color:#721c24; padding:10px; border-radius:4px; margin-bottom:15px; }
        .info    { background:#d1ecf1; color:#0c5460; padding:10px; border-radius:4px; margin-bottom:15px; }
        .btn-sm  { padding:5px 10px; font-size:12px; margin:2px; }
        .action-buttons { white-space:nowrap; }

        /* ── Buscador ── */
        .search-bar {
            display: flex; gap: 10px; align-items: center; margin-bottom: 16px;
        }
        .search-bar input {
            flex: 1; padding: 9px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px;
            font-size: 13px; outline: none; transition: border-color .2s;
        }
        .search-bar input:focus { border-color: #1a237e; }
        #searchClear { display:none; background:#f5f5f5; color:#555; border:1px solid #ddd; border-radius:8px; padding:9px 12px; cursor:pointer; font-size:13px; }

        /* ── Modal base ── */
        .modal-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(0,0,0,.55); z-index:9999;
            align-items:center; justify-content:center;
        }
        .modal-overlay.active { display:flex; }
        .modal-box {
            background:#fff; border-radius:12px;
            width:90%; max-width:620px; max-height:92vh;
            display:flex; flex-direction:column;
            box-shadow:0 10px 40px rgba(0,0,0,.25);
            animation:modalIn .18s ease;
        }
        @keyframes modalIn { from{transform:translateY(-16px);opacity:0} to{transform:translateY(0);opacity:1} }
        .modal-header {
            padding:20px 24px 14px; border-bottom:1px solid #f0f0f0;
            display:flex; justify-content:space-between; align-items:flex-start; flex-shrink:0;
        }
        .modal-header h3 { margin:0; font-size:16px; }
        .modal-header p  { margin:4px 0 0; font-size:12px; color:#888; }
        .modal-close { background:none; border:none; font-size:22px; cursor:pointer; color:#aaa; margin:0; padding:0; line-height:1; }
        .modal-close:hover { color:#333; background:none; }
        .modal-body { padding:20px 24px; overflow-y:auto; flex:1; }
        .modal-footer {
            padding:14px 24px; border-top:1px solid #f0f0f0;
            display:flex; justify-content:flex-end; gap:10px; flex-shrink:0;
        }
        .btn-cancel { padding:9px 20px; background:#f5f5f5; color:#555; border:1px solid #ddd; border-radius:6px; cursor:pointer; margin:0; font-size:13px; }
        .btn-cancel:hover { background:#e8e8e8; }
        .btn-submit { padding:9px 22px; background:linear-gradient(135deg,#1a237e,#283593); color:white; border:none; border-radius:6px; cursor:pointer; margin:0; font-size:13px; font-weight:600; }
        .btn-submit:hover { opacity:.88; }

        /* ── Form fields ── */
        .mf-label { font-size:12px; font-weight:600; color:#555; margin-bottom:5px; display:block; }
        .mf-input {
            width:100%; padding:9px 12px;
            border:1.5px solid #e0e0e0; border-radius:7px;
            font-size:13px; background:#fafafa; color:#333;
            outline:none; transition:border-color .2s;
        }
        .mf-input:focus { border-color:#1a237e; background:#fff; }
        .mf-row  { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; }
        .mf-row3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:14px; }
        .mf-group { margin-bottom:14px; }
        .mf-hint  { font-size:11px; color:#999; margin-top:4px; }
        .mf-section { font-size:12px; font-weight:700; color:#1a237e; text-transform:uppercase; letter-spacing:.06em; margin:16px 0 10px; padding-bottom:6px; border-bottom:2px solid #e8eaf6; }
        .mf-note { background:#fff8e1; border-left:3px solid #ffc107; padding:8px 12px; border-radius:0 6px 6px 0; font-size:12px; color:#795548; margin-bottom:14px; }
        .mf-error { font-size:11px; color:#dc3545; margin-top:3px; display:none; }
        .mf-error.show { display:block; }

        /* Roles checkboxes */
        .roles-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
        .role-chip {
            display:flex; align-items:center; gap:7px;
            padding:7px 10px; border:1.5px solid #e0e0e0; border-radius:7px;
            cursor:pointer; transition:all .15s; font-size:12px; font-weight:600;
            background:#fafafa; user-select:none;
        }
        .role-chip:hover { border-color:#1a237e; background:#f0f4ff; }
        .role-chip input[type=checkbox] { display:none; }
        .role-chip.checked { background:#e8eaf6; border-color:#3949ab; color:#1a237e; }
        .role-chip .chip-dot { width:8px; height:8px; border-radius:50%; background:#ccc; flex-shrink:0; }
        .role-chip.checked .chip-dot { background:#3949ab; }

        /* No results */
        #noResults { display:none; text-align:center; padding:30px; color:#aaa; font-size:14px; }
    </style>
</head>
<body>
<?php include BASE_PATH . '/views/partials/navbar.php'; ?>
<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Gestión de Usuarios
</div>

<?php
// Recuperar datos de sesión para modales (errores)
$modalCreateData   = $_SESSION['modal_create_data']   ?? null;
$modalCreateErrors = $_SESSION['modal_create_errors'] ?? null;
$modalEditData     = $_SESSION['modal_edit_data']     ?? null;
$modalEditErrors   = $_SESSION['modal_edit_errors']   ?? null;
$openModal         = $_SESSION['open_modal']          ?? null;
unset($_SESSION['modal_create_data'], $_SESSION['modal_create_errors'],
      $_SESSION['modal_edit_data'],   $_SESSION['modal_edit_errors'],
      $_SESSION['open_modal']);
?>

<div class="container">
    <div class="page-header" style="background:linear-gradient(135deg,#2e7d32,#388e3c);">
        <div class="ph-icon">👥</div>
        <div>
            <h1>Gestión de Usuarios</h1>
            <p>Administración de docentes, estudiantes y representantes</p>
        </div>
    </div>

    <div class="card">
        <!-- Mensajes -->
        <?php if(isset($_GET['created'])):   ?><div class="success">✓ Usuario creado correctamente</div><?php endif; ?>
        <?php if(isset($_GET['updated'])):   ?><div class="success">✓ Usuario actualizado correctamente</div><?php endif; ?>
        <?php if(isset($_GET['deleted'])):   ?><div class="success">✓ Usuario eliminado correctamente</div><?php endif; ?>
        <?php if(isset($_GET['deactivated'])): ?><div class="info">ℹ️ Usuario desactivado (tiene registros de asistencia)</div><?php endif; ?>
        <?php if(isset($_GET['success'])):   ?><div class="success">✓ Rol asignado correctamente</div><?php endif; ?>
        <?php if(isset($_GET['removed'])):   ?><div class="success">✓ Rol eliminado correctamente</div><?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error']==='has_assignments'): ?><div class="error">✗ No se puede eliminar el rol docente porque tiene asignaciones activas.</div><?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error']==='not_found'):      ?><div class="error">✗ Usuario no encontrado</div><?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error']==='self_delete'):    ?><div class="error">✗ No puedes eliminar tu propia cuenta</div><?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error']==='delete_failed'):  ?><div class="error">✗ Error al eliminar el usuario</div><?php endif; ?>

        <!-- Header + Crear -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h2 style="font-size:16px;color:#333;">👥 Gestión de Usuarios</h2>
            <button onclick="openCreateModal()" style="padding:9px 18px;background:linear-gradient(135deg,#1a237e,#283593);color:white;border:none;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;">
                + Crear Usuario
            </button>
        </div>

        <!-- Filtro por rol -->
        <?php
        $roleIcons  = ['docente'=>'👨‍🏫','estudiante'=>'👨‍🎓','inspector'=>'👁','autoridad'=>'⚙️','representante'=>'👨‍👩‍👧'];
        $roleColors = [
            'docente'      => ['active'=>'background:#007bff;color:white;border-color:#007bff;',      'outline'=>'color:#007bff;border-color:#007bff;'],
            'estudiante'   => ['active'=>'background:#28a745;color:white;border-color:#28a745;',      'outline'=>'color:#28a745;border-color:#28a745;'],
            'inspector'    => ['active'=>'background:#fd7e14;color:white;border-color:#fd7e14;',      'outline'=>'color:#fd7e14;border-color:#fd7e14;'],
            'autoridad'    => ['active'=>'background:#6f42c1;color:white;border-color:#6f42c1;',      'outline'=>'color:#6f42c1;border-color:#6f42c1;'],
            'representante'=> ['active'=>'background:#20c997;color:white;border-color:#20c997;',      'outline'=>'color:#20c997;border-color:#20c997;'],
        ];
        $currentFilter = $_GET['filter_role'] ?? '';
        ?>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
            <a href="?action=users" style="padding:6px 14px;border-radius:20px;text-decoration:none;font-size:12px;font-weight:600;border:2px solid;<?= $currentFilter==='' ? 'background:#343a40;color:white;border-color:#343a40;' : 'color:#343a40;border-color:#343a40;background:white;' ?>">👥 Todos</a>
            <?php foreach($roles as $role):
                $rn=$role['name']; $ico=$roleIcons[$rn]??'👤';
                $col=$roleColors[$rn]??['active'=>'background:#007bff;color:white;border-color:#007bff;','outline'=>'color:#007bff;border-color:#007bff;'];
                $isActive=($currentFilter===$rn); ?>
            <a href="?action=users&filter_role=<?= $rn ?>" style="padding:6px 14px;border-radius:20px;text-decoration:none;font-size:12px;font-weight:600;border:2px solid;<?= $isActive?$col['active']:$col['outline'].'background:white;' ?>">
                <?= $ico ?> <?= ucfirst($rn) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Buscador -->
        <div class="search-bar">
            <input type="text" id="userSearch" placeholder="🔍  Buscar por nombre, email o cédula..." oninput="filterUsers()">
            <button id="searchClear" onclick="clearSearch()">✕ Limpiar</button>
        </div>

        <!-- Tabla -->
        <table id="usersTable">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th>Apellidos y Nombres</th>
                    <th>Email</th>
                    <th>Cédula</th>
                    <th>Roles</th>
                    <th>Asignar Rol</th>
                    <th style="width:140px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $filteredUsers = $users;
            if($currentFilter !== '') {
                $filteredUsers = array_filter($users, function($u) use($currentFilter) {
                    return $u['roles'] && in_array($currentFilter, explode(',', $u['roles']));
                });
            }
            ?>
            <?php if(empty($filteredUsers)): ?>
                <tr><td colspan="7" style="text-align:center;padding:24px;color:#aaa;">No hay usuarios con este rol</td></tr>
            <?php else: ?>
                <?php $counter=1; foreach($filteredUsers as $user): ?>
                <tr data-search="<?= strtolower(htmlspecialchars($user['last_name'].' '.$user['first_name'].' '.$user['email'].' '.($user['dni']??''))) ?>">
                    <td><?= $counter++ ?></td>
                    <td><strong><?= htmlspecialchars($user['last_name'].' '.$user['first_name']) ?></strong></td>
                    <td style="color:#555;"><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['dni'] ?? '—') ?></td>
                    <td>
                        <?php if($user['roles']):
                            $db2=new Database();
                            $stR=$db2->connect()->prepare("SELECT r.id,r.name FROM roles r INNER JOIN user_roles ur ON r.id=ur.role_id WHERE ur.user_id=:uid ORDER BY r.name");
                            $stR->execute([':uid'=>$user['id']]);
                            foreach($stR->fetchAll() as $rd): ?>
                            <span class="badge">
                                <?= ucfirst($rd['name']) ?>
                                <form method="POST" action="?action=remove_role<?= $currentFilter?'&filter_role='.$currentFilter:'' ?>" style="display:inline;" onsubmit="return confirmRemoveRole(event,'<?= ucfirst($rd['name']) ?>','<?= htmlspecialchars(addslashes($user['last_name'].' '.$user['first_name'])) ?>')">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="role_id" value="<?= $rd['id'] ?>">
                                    <button type="submit" class="btn-remove-role">×</button>
                                </form>
                            </span>
                        <?php endforeach; else: ?><em style="color:#bbb;font-size:12px;">Sin roles</em><?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" action="?action=assign_role<?= $currentFilter?'&filter_role='.$currentFilter:'' ?>" style="display:inline-flex;gap:5px;">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="role_id" required style="padding:4px 6px;font-size:12px;border-radius:5px;border:1px solid #ddd;">
                                <option value="">Rol...</option>
                                <?php foreach($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= ucfirst($r['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" style="padding:4px 8px;font-size:12px;background:#1a237e;color:white;border:none;border-radius:5px;cursor:pointer;">+</button>
                        </form>
                    </td>
                    <td class="action-buttons">
                        <button onclick="openEditModal(<?= $user['id'] ?>)" class="btn-warning btn-sm">✏️ Editar</button>
                        <form method="POST" action="?action=delete_user" style="display:inline;" onsubmit="return confirmDelete(event,'<?= htmlspecialchars(addslashes($user['last_name'].' '.$user['first_name'])) ?>')">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <button type="submit" class="btn-danger btn-sm">🗑️</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <div id="noResults">😕 Sin resultados para esa búsqueda</div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL: CREAR USUARIO
══════════════════════════════════════════ -->
<div id="modalCreate" class="modal-overlay <?= $openModal==='create'?'active':'' ?>">
    <div class="modal-box" style="max-width:640px;">
        <div class="modal-header">
            <div>
                <h3 style="color:#1a237e;">👤 Crear Nuevo Usuario</h3>
                <p>Completa los datos para registrar un usuario</p>
            </div>
            <button class="modal-close" onclick="closeCreateModal()">✕</button>
        </div>
        <div class="modal-body">

            <?php if($modalCreateErrors): ?>
            <div class="error" style="margin-bottom:14px;">
                <strong>⚠ Errores:</strong><ul style="margin:6px 0 0 18px;">
                <?php foreach($modalCreateErrors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="?action=create_user_modal" id="formCreate" novalidate>

                <div class="mf-section">🔐 Datos de Acceso</div>
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Usuario *</label>
                        <input type="text" name="username" class="mf-input" required autocomplete="off"
                               value="<?= htmlspecialchars($modalCreateData['username'] ?? '') ?>">
                        <div class="mf-hint">Sin espacios ni caracteres especiales</div>
                    </div>
                    <div>
                        <label class="mf-label">Correo Electrónico *</label>
                        <input type="email" name="email" class="mf-input" required autocomplete="off"
                               value="<?= htmlspecialchars($modalCreateData['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Contraseña *</label>
                        <input type="password" name="password" id="cp_password" class="mf-input" minlength="6" required>
                        <div class="mf-hint">Mínimo 6 caracteres</div>
                    </div>
                    <div>
                        <label class="mf-label">Confirmar Contraseña *</label>
                        <input type="password" name="confirm_password" id="cp_confirm" class="mf-input" minlength="6" required>
                        <div class="mf-error" id="cp_confirm_err">Las contraseñas no coinciden</div>
                    </div>
                </div>

                <div class="mf-section">👨‍💼 Datos Personales</div>
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Nombres *</label>
                        <input type="text" name="first_name" class="mf-input" required
                               value="<?= htmlspecialchars($modalCreateData['first_name'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="mf-label">Apellidos *</label>
                        <input type="text" name="last_name" class="mf-input" required
                               value="<?= htmlspecialchars($modalCreateData['last_name'] ?? '') ?>">
                    </div>
                </div>
                <!-- Toggle extranjero -->
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <span style="font-size:12px;font-weight:600;color:#555;">Documento de Identidad</span>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;color:#555;font-weight:normal;">
                        <input type="checkbox" name="es_extranjero" id="c_es_extranjero" value="1"
                               onchange="toggleDocCreate()"
                               <?= !empty($modalCreateData['es_extranjero']) ? 'checked' : '' ?>
                               style="width:15px;height:15px;accent-color:#1a237e;">
                        Extranjero (Pasaporte)
                    </label>
                </div>
                <div class="mf-row">
                    <!-- Cédula Ecuador -->
                    <div id="c_cedula_row" style="display:<?= !empty($modalCreateData['es_extranjero']) ? 'none' : 'block' ?>;">
                        <label class="mf-label">Cédula de Identidad</label>
                        <div style="position:relative;">
                            <input type="text" name="dni" id="c_cedula" class="mf-input" maxlength="10"
                                   inputmode="numeric" placeholder="0000000000"
                                   oninput="this.value=this.value.replace(/\D/g,''); validateCedulaCreate()"
                                   value="<?= htmlspecialchars(!empty($modalCreateData['es_extranjero']) ? '' : ($modalCreateData['dni'] ?? '')) ?>">
                            <span id="c_icon_cedula" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:13px;pointer-events:none;"></span>
                        </div>
                        <div id="c_err_cedula" class="mf-hint" style="color:#999;">10 dígitos — validación algoritmo Ecuador</div>
                    </div>
                    <!-- Pasaporte -->
                    <div id="c_passport_row" style="display:<?= !empty($modalCreateData['es_extranjero']) ? 'block' : 'none' ?>;">
                        <label class="mf-label">Número de Pasaporte</label>
                        <input type="text" name="passport" id="c_passport" class="mf-input" maxlength="20"
                               placeholder="Ej: AB123456"
                               value="<?= htmlspecialchars(!empty($modalCreateData['es_extranjero']) ? ($modalCreateData['passport'] ?? '') : '') ?>">
                        <div class="mf-hint">Alfanumérico, hasta 20 caracteres</div>
                    </div>
                    <div>
                        <label class="mf-label">Teléfono</label>
                        <input type="text" name="phone" class="mf-input" maxlength="15"
                               placeholder="09XXXXXXXX"
                               value="<?= htmlspecialchars($modalCreateData['phone'] ?? '') ?>">
                        <div class="mf-hint">Celular: 09XXXXXXXX · Fijo: 0XXXXXXXX</div>
                    </div>
                </div>

                <div class="mf-section">🎭 Roles (opcional)</div>
                <div class="roles-grid">
                    <?php foreach($roles as $r):
                        $checked = $modalCreateData && in_array($r['id'], (array)($modalCreateData['roles']??[]));
                    ?>
                    <label class="role-chip <?= $checked?'checked':'' ?>" onclick="toggleChip(this)">
                        <input type="checkbox" name="roles[]" value="<?= $r['id'] ?>" <?= $checked?'checked':'' ?>>
                        <span class="chip-dot"></span>
                        <?= ucfirst($r['name']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeCreateModal()">Cancelar</button>
            <button type="submit" form="formCreate" class="btn-submit">✓ Crear Usuario</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL: EDITAR USUARIO
══════════════════════════════════════════ -->
<div id="modalEdit" class="modal-overlay <?= $openModal==='edit'?'active':'' ?>">
    <div class="modal-box" style="max-width:640px;">
        <div class="modal-header">
            <div>
                <h3 style="color:#e65100;">✏️ Editar Usuario</h3>
                <p id="editModalSubtitle">Actualizando información</p>
            </div>
            <button class="modal-close" onclick="closeEditModal()">✕</button>
        </div>
        <div class="modal-body">

            <?php if($modalEditErrors): ?>
            <div class="error" style="margin-bottom:14px;">
                <strong>⚠ Errores:</strong><ul style="margin:6px 0 0 18px;">
                <?php foreach($modalEditErrors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="?action=edit_user_modal" id="formEdit" novalidate>
                <input type="hidden" name="user_id" id="editUserId"
                       value="<?= htmlspecialchars($modalEditData['user_id'] ?? '') ?>">

                <div class="mf-section">🔐 Datos de Acceso</div>
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Usuario *</label>
                        <input type="text" name="username" id="editUsername" class="mf-input" required
                               value="<?= htmlspecialchars($modalEditData['username'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="mf-label">Correo Electrónico *</label>
                        <input type="email" name="email" id="editEmail" class="mf-input" required
                               value="<?= htmlspecialchars($modalEditData['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="mf-note">ℹ️ Deja la contraseña en blanco si no deseas cambiarla</div>
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Nueva Contraseña</label>
                        <input type="password" name="password" id="ep_password" class="mf-input" minlength="6">
                    </div>
                    <div>
                        <label class="mf-label">Confirmar Nueva Contraseña</label>
                        <input type="password" name="confirm_password" id="ep_confirm" class="mf-input" minlength="6">
                        <div class="mf-error" id="ep_confirm_err">Las contraseñas no coinciden</div>
                    </div>
                </div>

                <div class="mf-section">👨‍💼 Datos Personales</div>
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Nombres *</label>
                        <input type="text" name="first_name" id="editFirstName" class="mf-input" required
                               value="<?= htmlspecialchars($modalEditData['first_name'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="mf-label">Apellidos *</label>
                        <input type="text" name="last_name" id="editLastName" class="mf-input" required
                               value="<?= htmlspecialchars($modalEditData['last_name'] ?? '') ?>">
                    </div>
                </div>
                <!-- Toggle extranjero editar -->
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <span style="font-size:12px;font-weight:600;color:#555;">Documento de Identidad</span>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;color:#555;font-weight:normal;">
                        <input type="checkbox" name="es_extranjero" id="e_es_extranjero" value="1"
                               onchange="toggleDocEdit()"
                               style="width:15px;height:15px;accent-color:#e65100;">
                        Extranjero (Pasaporte)
                    </label>
                </div>
                <div class="mf-row">
                    <div id="e_cedula_row">
                        <label class="mf-label">Cédula de Identidad</label>
                        <div style="position:relative;">
                            <input type="text" name="dni" id="editDni" class="mf-input" maxlength="10"
                                   inputmode="numeric" placeholder="0000000000"
                                   oninput="this.value=this.value.replace(/\D/g,''); validateCedulaEdit()">
                            <span id="e_icon_cedula" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:13px;pointer-events:none;"></span>
                        </div>
                        <div id="e_err_cedula" class="mf-hint" style="color:#999;">10 dígitos — validación algoritmo Ecuador</div>
                    </div>
                    <div id="e_passport_row" style="display:none;">
                        <label class="mf-label">Número de Pasaporte</label>
                        <input type="text" name="passport" id="editPassport" class="mf-input" maxlength="20"
                               placeholder="Ej: AB123456">
                        <div class="mf-hint">Alfanumérico, hasta 20 caracteres</div>
                    </div>
                    <div>
                        <label class="mf-label">Teléfono</label>
                        <input type="text" name="phone" id="editPhone" class="mf-input" maxlength="15"
                               placeholder="09XXXXXXXX"
                               value="<?= htmlspecialchars($modalEditData['phone'] ?? '') ?>">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancelar</button>
            <button type="submit" form="formEdit" class="btn-submit" id="editSubmitBtn">✓ Guardar Cambios</button>
        </div>
    </div>
</div>

<!-- Data JSON de usuarios para editar sin fetch -->
<script type="application/json" id="_usersData">
<?= json_encode(
    array_map(fn($u) => [
        'id'         => $u['id'],
        'username'   => $u['username'],
        'email'      => $u['email'],
        'first_name' => $u['first_name'],
        'last_name'  => $u['last_name'],
        'dni'        => $u['dni']  ?? '',
        'phone'      => $u['phone'] ?? '',
    ], array_values($users)),
    JSON_HEX_TAG|JSON_HEX_AMP
) ?>
</script>

<script>
var usersData = JSON.parse(document.getElementById('_usersData').textContent);

/* ── Buscador ────────────────────────────────────────────── */
function normalize(s){ return (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase(); }
function filterUsers(){
    var q = normalize(document.getElementById('userSearch').value.trim());
    document.getElementById('searchClear').style.display = q ? '' : 'none';
    var rows = document.querySelectorAll('#usersTable tbody tr[data-search]');
    var visible = 0;
    rows.forEach(function(r){
        var match = !q || normalize(r.dataset.search).includes(q);
        r.style.display = match ? '' : 'none';
        if(match) visible++;
    });
    document.getElementById('noResults').style.display = (visible===0 && q) ? '' : 'none';
}
function clearSearch(){
    document.getElementById('userSearch').value = '';
    filterUsers();
}

/* ── Modal Crear ─────────────────────────────────────────── */
function openCreateModal(){ document.getElementById('modalCreate').classList.add('active'); }
function closeCreateModal(){ document.getElementById('modalCreate').classList.remove('active'); }

/* ── Modal Editar ────────────────────────────────────────── */
function openEditModal(userId){
    var u = usersData.find(function(x){ return x.id == userId; });
    if(!u) return;
    document.getElementById('editUserId').value    = u.id;
    document.getElementById('editUsername').value  = u.username;
    document.getElementById('editEmail').value     = u.email;
    document.getElementById('editFirstName').value = u.first_name;
    document.getElementById('editLastName').value  = u.last_name;
    // Detectar si es extranjero (dni no numérico o mas de 10 chars)
    var dni = u.dni || '';
    var esExt = dni !== '' && !/^\d{10}$/.test(dni);
    var extChk = document.getElementById('e_es_extranjero');
    extChk.checked = esExt;
    document.getElementById('e_cedula_row').style.display   = esExt ? 'none' : '';
    document.getElementById('e_passport_row').style.display = esExt ? '' : 'none';
    if(esExt){
        document.getElementById('editDni').value     = '';
        document.getElementById('editPassport').value = dni;
        setCedulaState('e_icon_cedula','e_err_cedula','','','#999');
    } else {
        document.getElementById('editDni').value      = dni;
        document.getElementById('editPassport').value = '';
        if(dni) validateCedulaEdit();
        else setCedulaState('e_icon_cedula','e_err_cedula','','10 dígitos — validación algoritmo Ecuador','#999');
    }
    document.getElementById('editPhone').value = u.phone || '';
    document.getElementById('editModalSubtitle').textContent = u.last_name + ', ' + u.first_name;
    document.getElementById('ep_password').value  = '';
    document.getElementById('ep_confirm').value   = '';
    document.getElementById('ep_confirm_err').classList.remove('show');
    document.getElementById('modalEdit').classList.add('active');
}
function closeEditModal(){ document.getElementById('modalEdit').classList.remove('active'); }

/* ── Cerrar con ESC / clic fuera ─────────────────────────── */
document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ closeCreateModal(); closeEditModal(); }});
document.getElementById('modalCreate').addEventListener('click', function(e){ if(e.target===this) closeCreateModal(); });
document.getElementById('modalEdit').addEventListener('click',   function(e){ if(e.target===this) closeEditModal(); });

/* ── Validación contraseña confirmar ─────────────────────── */
document.getElementById('cp_confirm').addEventListener('input', function(){
    var match = this.value === document.getElementById('cp_password').value;
    document.getElementById('cp_confirm_err').classList.toggle('show', !match && this.value!=='');
});
document.getElementById('ep_confirm').addEventListener('input', function(){
    var match = this.value === document.getElementById('ep_password').value;
    document.getElementById('ep_confirm_err').classList.toggle('show', !match && this.value!=='');
});

/* ── Role chips ──────────────────────────────────────────── */
function toggleChip(label){
    var cb = label.querySelector('input[type=checkbox]');
    cb.checked = !cb.checked;
    label.classList.toggle('checked', cb.checked);
}

/* ── Confirm Delete ──────────────────────────────────────── */
function confirmDelete(event, userName){
    event.preventDefault();
    if(!confirm('¿Eliminar a ' + userName + '?\n\nSi tiene asistencias registradas se desactivará en lugar de eliminarse.')) return false;
    event.target.submit();
    return false;
}
function confirmRemoveRole(event, roleName, userName){
    event.preventDefault();
    if(!confirm('¿Quitar el rol ' + roleName + ' a ' + userName + '?')) return false;
    event.target.submit();
    return false;
}

/* ── Validación cédula Ecuador ───────────────────────────── */
function validarCedula(c){
    if(!/^\d{10}$/.test(c)) return false;
    var p=parseInt(c.substring(0,2)); if(p<1||p>24) return false;
    var coef=[2,1,2,1,2,1,2,1,2], suma=0;
    for(var i=0;i<9;i++){ var r=parseInt(c[i])*coef[i]; suma+=r>=10?r-9:r; }
    var res=suma%10, dv=res===0?0:10-res;
    return dv===parseInt(c[9]);
}
function setCedulaState(iconId, errId, icon, msg, color){
    document.getElementById(iconId).textContent = icon;
    var err = document.getElementById(errId);
    err.textContent = msg; err.style.color = color;
}
function validateCedulaCreate(){
    var v = document.getElementById('c_cedula').value.trim();
    if(v==='') return setCedulaState('c_icon_cedula','c_err_cedula','','10 dígitos — validación algoritmo Ecuador','#999');
    if(v.length<10) return setCedulaState('c_icon_cedula','c_err_cedula','',''+v.length+'/10 dígitos','#999');
    if(validarCedula(v)) setCedulaState('c_icon_cedula','c_err_cedula','✅','Cédula válida','#28a745');
    else                 setCedulaState('c_icon_cedula','c_err_cedula','❌','Cédula inválida — verifica los dígitos','#dc3545');
}
function validateCedulaEdit(){
    var v = document.getElementById('editDni').value.trim();
    if(v==='') return setCedulaState('e_icon_cedula','e_err_cedula','','10 dígitos — validación algoritmo Ecuador','#999');
    if(v.length<10) return setCedulaState('e_icon_cedula','e_err_cedula','',''+v.length+'/10 dígitos','#999');
    if(validarCedula(v)) setCedulaState('e_icon_cedula','e_err_cedula','✅','Cédula válida','#28a745');
    else                 setCedulaState('e_icon_cedula','e_err_cedula','❌','Cédula inválida — verifica los dígitos','#dc3545');
}

/* ── Toggle extranjero ────────────────────────────────────── */
function toggleDocCreate(){
    var ext = document.getElementById('c_es_extranjero').checked;
    document.getElementById('c_cedula_row').style.display   = ext ? 'none' : '';
    document.getElementById('c_passport_row').style.display = ext ? '' : 'none';
    if(ext){ document.getElementById('c_cedula').value=''; setCedulaState('c_icon_cedula','c_err_cedula','','','#999'); }
    else   { document.getElementById('c_passport').value=''; }
}
function toggleDocEdit(){
    var ext = document.getElementById('e_es_extranjero').checked;
    document.getElementById('e_cedula_row').style.display   = ext ? 'none' : '';
    document.getElementById('e_passport_row').style.display = ext ? '' : 'none';
    if(ext){ document.getElementById('editDni').value=''; setCedulaState('e_icon_cedula','e_err_cedula','','','#999'); }
    else   { document.getElementById('editPassport').value=''; }
}

/* ── Reabrir modal si hubo error ─────────────────────────── */
<?php if($openModal==='edit' && $modalEditData): ?>
openEditModal(<?= (int)$modalEditData['user_id'] ?>);
<?php endif; ?>
<?php if($openModal==='create'): ?>
openCreateModal();
<?php endif; ?>
</script>
</body>
</html>