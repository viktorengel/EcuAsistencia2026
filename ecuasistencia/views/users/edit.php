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
    <title>Editar Usuario - EcuAsist</title>
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
        .password-info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">✏️ Editar Usuario: <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h4>
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

                        <form method="POST" action="?action=edit_user&id=<?= $user['id'] ?>">
                            <!-- Sección Datos de Acceso -->
                            <div class="form-section">
                                <h5>🔐 Datos de Acceso</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Nombre de Usuario</label>
                                        <input type="text" 
                                               name="username" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars($user['username']) ?>"
                                               required>
                                        <small class="text-muted">Sin espacios ni caracteres especiales</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Email</label>
                                        <input type="email" 
                                               name="email" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars($user['email']) ?>"
                                               required>
                                    </div>
                                </div>
                                
                                <div class="password-info">
                                    <strong>ℹ️ Contraseña:</strong> Deja en blanco si no deseas cambiarla
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <input type="password" 
                                               name="password" 
                                               class="form-control" 
                                               minlength="6">
                                        <small class="text-muted">Mínimo 6 caracteres</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirmar Nueva Contraseña</label>
                                        <input type="password" 
                                               name="confirm_password" 
                                               class="form-control" 
                                               minlength="6">
                                    </div>
                                </div>
                            </div>

                            <!-- Sección Datos Personales -->
                            <div class="form-section">
                                <h5>👨‍💼 Datos Personales</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Nombres</label>
                                        <input type="text" 
                                               name="first_name" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars($user['first_name']) ?>"
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Apellidos</label>
                                        <input type="text" 
                                               name="last_name" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars($user['last_name']) ?>"
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Cédula/DNI</label>
                                        <input type="text" 
                                               name="dni" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars($user['dni'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" 
                                               name="phone" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Roles -->
                            <div class="alert alert-info">
                                <strong>ℹ️ Roles:</strong> Los roles se gestionan desde la tabla de usuarios principal usando los botones de asignar/quitar roles.
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="?action=users" class="btn btn-secondary">
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