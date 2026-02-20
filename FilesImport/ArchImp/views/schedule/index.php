<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Horarios - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Gestión de Horarios
</div>

<div class="container">

    <!-- Header -->
    <div class="page-header purple">
        <div class="ph-icon">🗓️</div>
        <div>
            <h1>Horarios de Clases</h1>
            <p>Configure el horario semanal por curso</p>
        </div>
    </div>

    <!-- Tabla de cursos -->
    <div class="table-wrap">
        <div class="table-info">
            <span>📚 <strong><?= count($courses) ?></strong> cursos disponibles</span>
        </div>
        <?php if(empty($courses)): ?>
        <div class="empty-state">
            <div class="icon">📚</div>
            <p>No hay cursos registrados.</p>
        </div>
        <?php else: ?>
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
                    <td><strong><?= htmlspecialchars($course['name']) ?></strong></td>
                    <td><?= htmlspecialchars($course['grade_level']) ?></td>
                    <td><?= htmlspecialchars($course['parallel']) ?></td>
                    <td><?= ucfirst($course['shift_name']) ?></td>
                    <td>
                        <a href="?action=manage_schedule&course_id=<?= $course['id'] ?>"
                           class="btn btn-primary btn-sm">
                            ⚙️ Configurar Horario
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
