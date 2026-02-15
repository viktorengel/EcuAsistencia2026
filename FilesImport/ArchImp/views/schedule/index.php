<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Horarios - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .btn { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; text-decoration: none; display: inline-block; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        h2 { margin-bottom: 20px; color: #333; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Gestión de Horarios por Curso</h2>
            <p style="color: #666; margin-bottom: 20px;">
                Seleccione un curso para configurar su horario de clases
            </p>
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Nivel</th>
                        <th>Paralelo</th>
                        <th>Jornada</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($courses as $course): ?>
                    <tr>
                        <td><?= $course['name'] ?></td>
                        <td><?= $course['grade_level'] ?></td>
                        <td><?= $course['parallel'] ?></td>
                        <td><?= ucfirst($course['shift_name']) ?></td>
                        <td>
                            <a href="?action=manage_schedule&course_id=<?= $course['id'] ?>" class="btn">
                                Configurar Horario
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>