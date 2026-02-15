<?php 
if(isset($_SESSION['user_id'])) {
    header('Location: ?action=dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - EcuAsist 2026</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 420px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="login-card p-5">
        <div class="text-center mb-4">
            <i class="fas fa-school fa-4x text-primary mb-3"></i>
            <h2>EcuAsist 2026</h2>
            <p class="text-muted">Sistema de Asistencia Escolar</p>
        </div>
        
        <?php if(isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['registered'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> Registro exitoso. Ahora puede iniciar sesión.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['reset'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> Contraseña restablecida correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <?php Security::generateToken(); ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-user"></i> Usuario o Correo Electrónico
                </label>
                <input type="text" class="form-control" id="email" name="email" required autofocus placeholder="Ingrese usuario o email">
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Contraseña
                </label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Ingrese contraseña">
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>
        
        <div class="text-center">
            <a href="?action=forgot" class="text-decoration-none small">
                <i class="fas fa-key"></i> ¿Olvidó su contraseña?
            </a>
        </div>
        
        <hr class="my-4">
        
        <div class="text-center">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Usuario de prueba: <strong>admin</strong> / <strong>password</strong>
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>