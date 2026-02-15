<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario de <?= $course['name'] ?> - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select, input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .btn-danger { background: #dc3545; padding: 5px 10px; font-size: 12px; }
        .btn-danger:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #f8f9fa; font-weight: bold; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        h2 { margin-bottom: 20px; color: #333; }
        .course-info { background: #e7f3ff; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Clase agregada al horario</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="success">✓ Clase eliminada del horario</div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="error">✗ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="course-info">
            <h2><?= $course['name'] ?></h2>
            <p><strong>Nivel:</strong> <?= $course['grade_level'] ?> | <strong>Paralelo:</strong> <?= $course['parallel'] ?> | <strong>Jornada:</strong> <?= ucfirst($course['shift_name']) ?></p>
        </div>

        <div class="grid">
            <div class="card">
                <h2>Agregar Clase al Horario</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Día de la Semana</label>
                        <select name="day_of_week" required>
                            <option value="">Seleccionar...</option>
                            <option value="lunes">Lunes</option>
                            <option value="martes">Martes</option>
                            <option value="miercoles">Miércoles</option>
                            <option value="jueves">Jueves</option>
                            <option value="viernes">Viernes</option>
                            <option value="sabado">Sábado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número de Hora</label>
                        <select name="period_number" required>
                            <option value="">Seleccionar...</option>
                            <?php 
                            $maxHours = strpos($course['grade_level'], 'Técnico') !== false ? 8 : 7;
                            for($i = 1; $i <= $maxHours; $i++): 
                            ?>
                                <option value="<?= $i ?>"><?= $i ?>ra hora</option>
                            <?php endfor; ?>
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

                    <button type="submit">Agregar Clase</button>
                </form>
            </div>

            <div class="card">
                <h2>Horario Actual</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Hora</th>
                            <th>Asignatura</th>
                            <th>Docente</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($schedule)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">No hay clases en el horario</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($schedule as $class): ?>
                            <tr>
                                <td><?= ucfirst($class['day_of_week']) ?></td>
                                <td><?= $class['period_number'] ?>ra hora</td>
                                <td><?= $class['subject_name'] ?></td>
                                <td><?= $class['teacher_name'] ?></td>
                                <td>
                                    <form method="POST" action="?action=delete_schedule_class" style="display: inline;">
                                        <input type="hidden" name="schedule_id" value="<?= $class['id'] ?>">
                                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                        <button type="submit" class="btn-danger" onclick="return confirm('¿Eliminar esta clase del horario?')">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>