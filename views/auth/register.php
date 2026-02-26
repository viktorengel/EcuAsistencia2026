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
    <title>Registro de Representante - EcuAsistencia</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
               background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
               min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border-radius: 14px; box-shadow: 0 12px 40px rgba(0,0,0,0.2);
                max-width: 500px; width: 100%; padding: 40px; }
        .card-logo { text-align: center; margin-bottom: 28px; }
        .card-logo .icon { font-size: 3rem; margin-bottom: 10px; }
        .card-logo h2 { font-size: 1.4rem; font-weight: 700; color: #333; }
        .card-logo p { color: #888; font-size: 0.85rem; margin-top: 4px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 0.82rem; font-weight: 600; color: #555; }
        .form-group input { width: 100%; padding: 10px 14px; border: 1.5px solid #ddd; border-radius: 8px;
                            font-size: 0.9rem; transition: border-color 0.2s; }
        .form-group input:focus { border-color: #667eea; outline: none; box-shadow: 0 0 0 3px rgba(102,126,234,0.12); }
        .form-group .hint { font-size: 0.75rem; color: #aaa; margin-top: 4px; }
        .role-badge { display: flex; align-items: center; gap: 10px; background: #f0f4ff;
                      border: 1.5px solid #c7d5fb; border-radius: 8px; padding: 12px 16px;
                      margin-bottom: 20px; font-size: 0.88rem; color: #3b5bdb; font-weight: 600; }
        .btn-submit { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea, #764ba2);
                      color: #fff; border: none; border-radius: 8px; font-size: 0.95rem; font-weight: 600;
                      cursor: pointer; transition: opacity 0.2s; margin-top: 4px; }
        .btn-submit:hover { opacity: 0.9; }
        .alert { padding: 10px 14px; border-radius: 8px; margin-bottom: 18px; font-size: 0.85rem; }
        .alert-danger  { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .login-link { text-align: center; margin-top: 20px; font-size: 0.85rem; color: #888; }
        .login-link a { color: #667eea; text-decoration: none; font-weight: 600; }
        .login-link a:hover { text-decoration: underline; }
        .section-title { font-size: 0.78rem; font-weight: 700; color: #999; text-transform: uppercase;
                         letter-spacing: 0.05em; margin: 20px 0 12px; padding-bottom: 6px;
                         border-bottom: 1px solid #f0f0f0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-logo">
            <div class="icon">👨‍👩‍👧‍👦</div>
            <h2>Registro de Representante</h2>
            <p>Crea tu cuenta para seguir la asistencia de tu representado</p>
        </div>

        <?php if(isset($error)): ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="role-badge">
            🏷️ Tu cuenta se creará con el rol de <strong>Representante</strong>
        </div>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? Security::generateToken() ?>">

            <div class="section-title">Datos personales</div>

            <div class="form-row">
                <div class="form-group">
                    <label>Apellidos *</label>
                    <input type="text" name="last_name" required placeholder="Apellidos"
                           value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Nombres *</label>
                    <input type="text" name="first_name" required placeholder="Nombres"
                           value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Cédula *</label>
                    <input type="text" name="dni" required placeholder="0912345678" maxlength="10"
                           value="<?= htmlspecialchars($_POST['dni'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="phone" placeholder="0991234567"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="section-title">Acceso al sistema</div>

            <div class="form-group">
                <label>Correo electrónico *</label>
                <input type="email" name="email" required placeholder="correo@ejemplo.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Nombre de usuario *</label>
                <input type="text" name="username" required placeholder="Sin espacios ni caracteres especiales"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                <div class="hint">Solo letras, números y guiones bajos. Ejemplo: juan_perez</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Contraseña *</label>
                    <input type="password" name="password" required placeholder="Mínimo 6 caracteres">
                </div>
                <div class="form-group">
                    <label>Confirmar contraseña *</label>
                    <input type="password" name="password_confirm" required placeholder="Repite la contraseña">
                </div>
            </div>

            <button type="submit" class="btn-submit">✅ Crear mi cuenta</button>
        </form>

        <div class="login-link">
            ¿Ya tienes cuenta? <a href="?action=login">Iniciar sesión</a>
        </div>
    </div>
</body>
</html>