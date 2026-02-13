<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignaciones del Curso - EcuAsist</title>
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
        .course-info { background: #e7f3ff; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .tutor-box { background: #fff3cd; padding: 15px; border-radius: 4px; border-left: 4px solid #ffc107; margin-bottom: 20px; }
        h2, h3 { color: #333; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Asignaciones del Curso</h1>
        <div>
            <a href="?action=assignments">← Asignaciones</a>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="course-info">
                <h2><?= $course['name'] ?></h2>
                <p><strong>Nivel:</strong> <?= $course['grade_level'] ?> | <strong>Paralelo:</strong> <?= $course['parallel'] ?></p>
                <p><strong>Jornada:</strong> <?= ucfirst($course['shift_name']) ?> | <strong>Año:</strong> <?= $course['year_name'] ?></p>
            </div>

            <?php if($tutor): ?>
            <div class="tutor-box">
                <h3>👨‍🏫 Docente Tutor</h3>
                <p><?= $tutor['last_name'] . ' ' . $tutor['first_name'] ?></p>
                <p style="font-size: 14px; color: #666;">Email: <?= $tutor['email'] ?></p>
            </div>
            <?php else: ?>
            <div class="tutor-box">
                <p><strong>⚠ Este curso no tiene docente tutor asignado</strong></p>
            </div>
            <?php endif; ?>

            <h3>Asignaturas y Docentes</h3>
            <?php if(count($assignments) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Asignatura</th>
                        <th>Docente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($assignments as $assignment): ?>
                    <tr>
                        <td><?= $assignment['subject_name'] ?></td>
                        <td>
                            <?= $assignment['teacher_name'] ?>
                            <?php if($assignment['is_tutor']): ?>
                                <span style="color: #ffc107; font-weight: bold;">(Tutor)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: #666; margin-top: 15px;">No hay docentes asignados a este curso.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>