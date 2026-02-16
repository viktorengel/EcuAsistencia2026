<?php
require_once BASE_PATH . '/helpers/Security.php';
Security::requireLogin();
if (!Security::hasRole('autoridad')) {
    die('Acceso denegado');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Asignatura - EcuAsist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 70px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">✏️ Editar Asignatura: <?= htmlspecialchars($subject['name']) ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control" 
                                       placeholder="Ej: Matemáticas" 
                                       value="<?= htmlspecialchars($subject['name']) ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Código *</label>
                                <input type="text" 
                                       name="code" 
                                       class="form-control" 
                                       placeholder="Ej: MAT" 
                                       value="<?= htmlspecialchars($subject['code']) ?>"
                                       required>
                                <small class="text-muted">Código corto para identificar la asignatura</small>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="?action=academic" class="btn btn-secondary">← Cancelar</a>
                                <button type="submit" class="btn btn-success">✓ Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>