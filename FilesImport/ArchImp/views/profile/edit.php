<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - EcuAsist</title>
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
        button { padding: 12px 30px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .btn-cancel { background: #6c757d; margin-left: 10px; }
        .btn-cancel:hover { background: #5a6268; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Editar Perfil</h1>
        <div>
            <a href="?action=profile">← Volver</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h2 style="margin-bottom: 20px;">Actualizar Información</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nombres</label>
                    <input type="text" name="first_name" value="<?= $user['first_name'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Apellidos</label>
                    <input type="text" name="last_name" value="<?= $user['last_name'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $user['email'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="phone" value="<?= $user['phone'] ?>">
                </div>

                <button type="submit">Guardar Cambios</button>
                <button type="button" class="btn-cancel" onclick="location.href='?action=profile'">Cancelar</button>
            </form>
        </div>
    </div>
</body>
</html>