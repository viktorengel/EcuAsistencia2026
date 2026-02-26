<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { margin-bottom: 20px; color: #333; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Nueva Contraseña</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
            <input type="password" name="password" placeholder="Nueva contraseña" required>
            <input type="password" name="password_confirm" placeholder="Confirmar contraseña" required>
            <button type="submit">Cambiar contraseña</button>
        </form>
    </div>
</body>
</html>