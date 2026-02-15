<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respaldos - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .btn { padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 10px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        h2 { margin-bottom: 20px; color: #333; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Respaldo creado exitosamente: <?= $_GET['file'] ?></div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="error">✗ Error al crear el respaldo. Verifique la configuración de MySQL.</div>
        <?php endif; ?>

        <?php if(isset($_GET['cleanup'])): ?>
            <div class="success">✓ Se eliminaron <?= $_GET['cleanup'] ?> respaldos antiguos</div>
        <?php endif; ?>

        <div class="card">
            <div class="info">
                ⚠️ <strong>Importante:</strong> Los respaldos se crean automáticamente. Se recomienda descargar y guardar copias en un lugar seguro.
            </div>

            <a href="?action=create_backup" class="btn btn-success" onclick="return confirm('¿Crear nuevo respaldo?')">
                Crear Respaldo Ahora
            </a>
            <a href="?action=cleanup_backups" class="btn btn-danger" onclick="return confirm('¿Eliminar respaldos de más de 30 días?')">
                Limpiar Antiguos
            </a>
        </div>

        <div class="card">
            <h2>Respaldos Disponibles (<?= count($backups) ?>)</h2>
            
            <?php if(count($backups) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Fecha</th>
                        <th>Tamaño</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($backups as $backup): ?>
                    <tr>
                        <td><?= $backup['name'] ?></td>
                        <td><?= date('d/m/Y H:i:s', $backup['date']) ?></td>
                        <td><?= number_format($backup['size'] / 1024, 2) ?> KB</td>
                        <td>
                            <a href="?action=download_backup&file=<?= $backup['name'] ?>" class="btn">
                                Descargar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #666; padding: 40px;">No hay respaldos disponibles</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>