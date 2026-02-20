<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - EcuAsist</title>
    <style>
        .avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #0277bd, #0288d1); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: 700; flex-shrink: 0; }
        .profile-header { display: flex; align-items: center; gap: 20px; margin-bottom: 24px; }
        .info-row { display: flex; padding: 14px 0; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #666; width: 150px; flex-shrink: 0; }
        .info-value { color: #333; flex: 1; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Mi Perfil
</div>

<div class="container" style="max-width:800px;">

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ Perfil actualizado correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['password_changed'])): ?>
        <div class="alert alert-success">✓ Contraseña cambiada correctamente</div>
    <?php endif; ?>

    <!-- Header -->
    <div class="page-header" style="background:linear-gradient(135deg,#0277bd,#0288d1);">
        <div class="ph-icon">👤</div>
        <div>
            <h1>Mi Perfil</h1>
            <p>Información de tu cuenta</p>
        </div>
        <div class="ph-actions">
            <a href="?action=edit_profile" class="btn" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.6);">✏️ Editar</a>
            <a href="?action=change_password" class="btn" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.6);margin-left:8px;">🔒 Contraseña</a>
        </div>
    </div>

    <div class="panel">
        <div class="profile-header">
            <div class="avatar">
                <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
            </div>
            <div>
                <h2 style="font-size:1.3rem;font-weight:700;"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                <p style="color:#888;margin-top:2px;">@<?= htmlspecialchars($user['username']) ?></p>
            </div>
        </div>

        <div class="info-row">
            <span class="info-label">Email</span>
            <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Cédula</span>
            <span class="info-value"><?= htmlspecialchars($user['dni'] ?? 'No registrada') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Teléfono</span>
            <span class="info-value"><?= htmlspecialchars($user['phone'] ?? 'No registrado') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Roles</span>
            <span class="info-value">
                <?php foreach($roles as $role): ?>
                    <span class="badge badge-green" style="margin-right:4px;"><?= ucfirst(htmlspecialchars($role)) ?></span>
                <?php endforeach; ?>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Miembro desde</span>
            <span class="info-value" style="color:#888;"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
        </div>
    </div>

</div>
</body>
</html>