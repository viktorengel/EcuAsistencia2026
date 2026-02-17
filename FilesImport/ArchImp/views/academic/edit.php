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
    <title>Editar Año Lectivo - EcuAsist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 70px; }
        .form-section { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
        .form-section h5 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .required::after {
            content: " *";
            color: red;
        }
        .info-box {
            background: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 15px;
            margin-bottom: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .status-active {
            background: #28a745;
            color: white;
        }
        .status-inactive {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">✏️ Editar Año Lectivo: <?= htmlspecialchars($year['name']) ?></h4>
                            <span class="status-badge <?= $year['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                <?= $year['is_active'] ? '✓ ACTIVO' : 'Inactivo' ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <strong>Errores:</strong>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="info-box">
                            <strong>ℹ️ Información importante:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Las fechas no pueden solaparse con otros años lectivos</li>
                                <li>El estado (activo/inactivo) se gestiona desde la tabla principal</li>
                                <li>No se puede eliminar un año lectivo activo</li>
                            </ul>
                        </div>

                        <form method="POST" action="?action=edit_school_year&id=<?= $year['id'] ?>">
                            <div class="form-section">
                                <h5>📋 Información General</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label required">Nombre del Año Lectivo</label>
                                    <input type="text" 
                                           name="name" 
                                           class="form-control" 
                                           placeholder="Ej: 2025-2026"
                                           value="<?= htmlspecialchars($year['name']) ?>"
                                           required>
                                    <small class="text-muted">Formato recomendado: YYYY-YYYY</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Fecha de Inicio</label>
                                        <input type="date" 
                                               name="start_date" 
                                               class="form-control" 
                                               value="<?= $year['start_date'] ?>"
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Fecha de Fin</label>
                                        <input type="date" 
                                               name="end_date" 
                                               class="form-control" 
                                               value="<?= $year['end_date'] ?>"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <strong>ℹ️ Estado del año lectivo:</strong> 
                                Para activar o desactivar este año lectivo, usa los botones 
                                "Activar" o "Desactivar" en la tabla principal de años lectivos.
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="?action=academic" class="btn btn-secondary">
                                    ← Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    ✓ Guardar Cambios
                                </button>
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