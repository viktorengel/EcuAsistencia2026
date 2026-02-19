<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=profile">Mi Perfil</a> &rsaquo;
    Cambiar Contraseña
</div>

<div class="container" style="max-width:500px;">

    <!-- Header -->
    <div class="page-header dark">
        <div class="ph-icon">🔒</div>
        <div>
            <h1>Cambiar Contraseña</h1>
            <p>Actualiza tu contraseña de acceso</p>
        </div>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="panel">
        <form method="POST">
            <div class="form-group">
                <label>Contraseña Actual *</label>
                <input type="password" name="current_password" class="form-control" required
                       placeholder="Ingresa tu contraseña actual">
            </div>

            <div class="form-group">
                <label>Nueva Contraseña *</label>
                <input type="password" name="new_password" class="form-control" minlength="6" required
                       placeholder="Mínimo 6 caracteres">
            </div>

            <div class="form-group">
                <label>Confirmar Nueva Contraseña *</label>
                <input type="password" name="confirm_password" class="form-control" minlength="6" required
                       placeholder="Repite la nueva contraseña">
            </div>

            <div style="display:flex;gap:10px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">🔒 Cambiar Contraseña</button>
                <a href="?action=profile" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>

</div>
</body>
</html>
