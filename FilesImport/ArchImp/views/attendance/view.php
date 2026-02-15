<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Asistencias - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 12px 30px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .badge { padding: 5px 10px; border-radius: 3px; color: white; font-size: 12px; }
        .badge-presente { background: #28a745; }
        .badge-ausente { background: #dc3545; }
        .badge-tardanza { background: #ffc107; color: #333; }
        .badge-justificado { background: #17a2b8; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Consultar</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Curso</label>
                    <select name="course_id" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['id'] ?>"><?= $course['name'] ?> - <?= $course['shift_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <button type="submit">Buscar</button>
            </form>
        </div>

        <?php if(!empty($attendances)): ?>
        <div class="card">
            <h2>Resultados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($attendances as $att): ?>
                    <tr>
                        <td><?= $att['last_name'] . ' ' . $att['first_name'] ?></td>
                        <td><?= $att['subject_name'] ?></td>
                        <td><?= $att['teacher_name'] ?></td>
                        <td><?= $att['hour_period'] ?></td>
                        <td>
                            <span class="badge badge-<?= $att['status'] ?>">
                                <?= ucfirst($att['status']) ?>
                            </span>
                        </td>
                        <td><?= $att['observation'] ?: '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>