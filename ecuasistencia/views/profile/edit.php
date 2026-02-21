<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=profile">Mi Perfil</a> &rsaquo;
    Editar
</div>

<div class="container" style="max-width:600px;">

    <!-- Header -->
    <div class="page-header blue">
        <div class="ph-icon">✏️</div>
        <div>
            <h1>Editar Perfil</h1>
            <p>Actualiza tu información personal</p>
        </div>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="panel">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombres *</label>
                    <input type="text" name="first_name" class="form-control"
                           value="<?= htmlspecialchars($user['first_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Apellidos *</label>
                    <input type="text" name="last_name" class="form-control"
                           value="<?= htmlspecialchars($user['last_name']) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="phone" class="form-control"
                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Ej: 0987654321">
            </div>

            <div style="display:flex;gap:10px;margin-top:24px;">
                <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
                <a href="?action=profile" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>

</div>
</body>
</html>
