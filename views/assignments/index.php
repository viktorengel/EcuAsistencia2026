<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignaciones Docentes - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .btn-danger { background: #dc3545; padding: 5px 10px; font-size: 12px; }
        .btn-danger:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #f8f9fa; font-weight: bold; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .badge { padding: 4px 8px; border-radius: 3px; font-size: 11px; background: #ffc107; color: #333; }
        h2 { margin-bottom: 20px; color: #333; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Asignación creada correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="error">✗ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['removed'])): ?>
            <div class="success">✓ Asignación eliminada</div>
        <?php endif; ?>

        <div class="card">
            <h2>Asignar Docente a Materia</h2>
            <form method="POST" action="?action=create_assignment">
                <div class="form-group">
                    <label>Docente</label>
                    <select name="teacher_id" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>">
                                <?= $teacher['last_name'] . ' ' . $teacher['first_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Curso</label>
                    <select name="course_id" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['id'] ?>">
                                <?= $course['name'] ?> - <?= $course['shift_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Asignatura</label>
                    <select name="subject_id" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($subjects as $subject): ?>
                            <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit">Asignar</button>
            </form>
        </div>

        <div class="card">
            <h2>Asignaciones Docente-Materia</h2>
            <table>
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Curso</th>
                        <th>Asignatura</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($assignments as $assignment): ?>
                    <tr>
                        <td><?= $assignment['teacher_name'] ?></td>
                        <td><?= $assignment['course_name'] ?></td>
                        <td><?= $assignment['subject_name'] ?></td>
                        <td>
                            <form method="POST" action="?action=remove_assignment" style="display: inline;">
                                <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                <button type="submit" class="btn-danger" onclick="return confirm('¿Eliminar asignación?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Ver Asignaciones por Curso</h2>
            <form method="GET" action="">
                <input type="hidden" name="action" value="view_course_assignments">
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label>Seleccionar Curso</label>
                        <select name="course_id" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($courses as $course): ?>
                                <option value="<?= $course['id'] ?>">
                                    <?= $course['name'] ?> - <?= $course['shift_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" style="background: #007bff;">Ver Detalle</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>