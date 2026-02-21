<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes del Curso - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        h2 { margin-bottom: 10px; color: #333; }
        .course-info { background: #e7f3ff; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .btn-back { display: inline-block; margin-bottom: 20px; padding: 8px 16px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    🏠 Inicio &rsaquo; <a href="?action=academic">Configuración Académica</a> &rsaquo; Estudiantes del Curso
</div>

<div class="container">
    <a href="?action=academic" class="btn-back">← Volver a Configuración Académica</a>

    <div class="card">
        <div class="course-info">
            <h2><?= $course['name'] ?></h2>
            <p><strong>Nivel:</strong> <?= $course['grade_level'] ?> | <strong>Paralelo:</strong> <?= $course['parallel'] ?></p>
            <p><strong>Jornada:</strong> <?= ucfirst($course['shift_name']) ?> | <strong>Año Lectivo:</strong> <?= $course['year_name'] ?></p>
        </div>

        <h3>Estudiantes Matriculados (<?= count($students) ?>)</h3>

        <?php if(count($students) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Apellidos y Nombres</th>
                    <th>Cédula</th>
                    <th>Fecha Matrícula</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $index => $student): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $student['last_name'] . ' ' . $student['first_name'] ?></td>
                    <td><?= $student['dni'] ?? '-' ?></td>
                    <td><?= date('d/m/Y', strtotime($student['enrollment_date'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="color: #666; margin-top: 20px;">No hay estudiantes matriculados en este curso.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>