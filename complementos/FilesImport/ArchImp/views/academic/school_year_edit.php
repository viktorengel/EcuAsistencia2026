<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Año Lectivo - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=academic">Configuración Académica</a> &rsaquo;
    Editar Año Lectivo
</div>

<div class="container" style="max-width:600px;">

    <div class="page-header" style="background:linear-gradient(135deg,#e65100,#ef6c00);">
        <div class="ph-icon">✏️</div>
        <div>
            <h1>Editar Año Lectivo</h1>
            <p><?= htmlspecialchars($year['name']) ?></p>
        </div>
    </div>

    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $e): ?>
                <div>⚠️ <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <form method="POST" action="?action=edit_school_year&id=<?= $year['id'] ?>">

            <div class="form-group">
                <label>Nombre del Año Lectivo *</label>
                <input type="text" name="name" class="form-control" required
                       value="<?= htmlspecialchars($_POST['name'] ?? $year['name']) ?>"
                       placeholder="Ej: 2025-2026">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Fecha de Inicio *</label>
                    <input type="date" name="start_date" class="form-control" required
                           value="<?= htmlspecialchars($_POST['start_date'] ?? $year['start_date']) ?>">
                </div>
                <div class="form-group">
                    <label>Fecha de Fin *</label>
                    <input type="date" name="end_date" class="form-control" required
                           value="<?= htmlspecialchars($_POST['end_date'] ?? $year['end_date']) ?>">
                </div>
            </div>

            <?php if($year['is_active']): ?>
                <div class="alert alert-info" style="margin-top:8px;font-size:13px;">
                    ✅ Este es el <strong>año lectivo activo</strong> actualmente. Para cambiarlo, activa otro año lectivo.
                </div>
            <?php endif; ?>

            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
                <a href="?action=academic" class="btn btn-outline">Cancelar</a>
            </div>

        </form>
    </div>

</div>
</body>
</html>