<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .profile-header { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; }
        .avatar { width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px; font-weight: bold; }
        .info-row { display: flex; padding: 15px 0; border-bottom: 1px solid #f0f0f0; }
        .info-row:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #666; width: 150px; }
        .value { color: #333; flex: 1; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px; margin-top: 10px; }
        .btn:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 3px; background: #28a745; color: white; font-size: 12px; margin: 2px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Perfil actualizado correctamente</div>
        <?php endif; ?>
        
        <?php if(isset($_GET['password_changed'])): ?>
            <div class="success">✓ Contraseña cambiada correctamente</div>
        <?php endif; ?>

        <div class="card">
            <div class="profile-header">
                <div class="avatar">
                    <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                </div>
                <div>
                    <h2><?= $user['first_name'] . ' ' . $user['last_name'] ?></h2>
                    <p style="color: #666;">@<?= $user['username'] ?></p>
                </div>
            </div>

            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value"><?= $user['email'] ?></span>
            </div>

            <div class="info-row">
                <span class="label">Cédula:</span>
                <span class="value"><?= $user['dni'] ?? 'No registrada' ?></span>
            </div>

            <div class="info-row">
                <span class="label">Teléfono:</span>
                <span class="value"><?= $user['phone'] ?? 'No registrado' ?></span>
            </div>

            <div class="info-row">
                <span class="label">Roles:</span>
                <span class="value">
                    <?php foreach($roles as $role): ?>
                        <span class="badge"><?= ucfirst($role) ?></span>
                    <?php endforeach; ?>
                </span>
            </div>

            <div class="info-row">
                <span class="label">Miembro desde:</span>
                <span class="value"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
            </div>

            <div style="margin-top: 20px;">
                <a href="?action=edit_profile" class="btn">Editar Perfil</a>
                <a href="?action=change_password" class="btn btn-secondary">Cambiar Contraseña</a>
            </div>
        </div>
    </div>
</body>
</html>