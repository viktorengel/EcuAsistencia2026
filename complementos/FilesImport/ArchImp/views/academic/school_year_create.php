<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Año Lectivo - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=academic">Configuración Académica</a> &rsaquo;
    Crear Año Lectivo
</div>

<div class="container" style="max-width:600px;">

    <div class="page-header" style="background:linear-gradient(135deg,#1a237e,#283593);">
        <div class="ph-icon">📅</div>
        <div>
            <h1>Crear Año Lectivo</h1>
            <p>Define el período académico</p>
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
        <form method="POST" action="?action=create_school_year">

            <div class="form-group">
                <label>Nombre del Año Lectivo *</label>
                <input type="text" name="name" class="form-control" required
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       placeholder="Ej: 2025-2026">
                <small style="color:#888;">Formato recomendado: AAAA-AAAA</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Fecha de Inicio *</label>
                    <input type="date" name="start_date" class="form-control" required
                           value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Fecha de Fin *</label>
                    <input type="date" name="end_date" class="form-control" required
                           value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group" style="margin-top:8px;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:normal;">
                    <input type="checkbox" name="is_active" value="1"
                           <?= isset($_POST['is_active']) ? 'checked' : '' ?>
                           style="width:18px;height:18px;accent-color:#28a745;">
                    <span>
                        <strong>Marcar como año lectivo activo</strong><br>
                        <small style="color:#888;">Solo puede haber un año lectivo activo a la vez</small>
                    </span>
                </label>
            </div>

            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit" class="btn btn-success">💾 Crear Año Lectivo</button>
                <a href="?action=academic" class="btn btn-outline">Cancelar</a>
            </div>

        </form>
    </div>

</div>
</body>
</html>