<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { margin-bottom: 20px; color: #333; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .error { color: red; margin-bottom: 10px; }
        .success { color: green; margin-bottom: 10px; }
        a { color: #007bff; text-decoration: none; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <?php if(isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <?php if(isset($_GET['registered'])): ?>
            <p class="success">Registro exitoso. Inicie sesión.</p>
        <?php endif; ?>
        <?php if(isset($_GET['reset'])): ?>
            <p class="success">Contraseña restablecida. Inicie sesión.</p>
        <?php endif; ?>
        
        <div class="info">
            💡 Puede ingresar con su <strong>usuario</strong> o <strong>correo electrónico</strong>
        </div>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
            <input type="text" name="email" placeholder="Usuario o Correo Electrónico" required autofocus>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>
        
        <p style="margin-top: 15px;">
            <a href="?action=register">Registrarse</a> | 
            <a href="?action=forgot">¿Olvidó su contraseña?</a>
        </p>
    </div>
</body>
</html>