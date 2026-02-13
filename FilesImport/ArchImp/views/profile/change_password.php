<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 600px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 12px 30px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .btn-cancel { background: #6c757d; margin-left: 10px; }
        .btn-cancel:hover { background: #5a6268; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Cambiar Contraseña</h1>
        <div>
            <a href="?action=profile">← Volver</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <?php if(isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <h2 style="margin-bottom: 20px;">Nueva Contraseña</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Contraseña Actual</label>
                    <input type="password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label>Nueva Contraseña</label>
                    <input type="password" name="new_password" minlength="6" required>
                </div>

                <div class="form-group">
                    <label>Confirmar Nueva Contraseña</label>
                    <input type="password" name="confirm_password" minlength="6" required>
                </div>

                <button type="submit">Cambiar Contraseña</button>
                <button type="button" class="btn-cancel" onclick="location.href='?action=profile'">Cancelar</button>
            </form>
        </div>
    </div>
</body>
</html>