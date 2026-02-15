<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 36px; font-weight: bold; margin-bottom: 5px; }
        .stat-label { font-size: 14px; opacity: 0.9; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .progress-bar { background: #e9ecef; border-radius: 4px; height: 20px; overflow: hidden; }
        .progress-fill { background: #28a745; height: 100%; transition: width 0.3s; }
        .form-group { margin-bottom: 15px; display: inline-block; margin-right: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, button { padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        h2 { margin-bottom: 20px; color: #333; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <form method="GET">
                <input type="hidden" name="action" value="stats">
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="start_date" value="<?= $_GET['start_date'] ?? date('Y-m-01') ?>">
                </div>
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="end_date" value="<?= $_GET['end_date'] ?? date('Y-m-d') ?>">
                </div>
                <button type="submit">Filtrar</button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stat-number"><?= $stats['overall']['total_records'] ?></div>
                <div class="stat-label">Registros Totales</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="stat-number"><?= $stats['overall']['presente'] ?></div>
                <div class="stat-label">Presentes</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                <div class="stat-number"><?= $stats['overall']['ausente'] ?></div>
                <div class="stat-label">Ausentes</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="stat-number"><?= $stats['overall']['tardanza'] ?></div>
                <div class="stat-label">Tardanzas</div>
            </div>
        </div>

        <div class="card">
            <h2>Asistencia por Curso</h2>
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Total</th>
                        <th>Presentes</th>
                        <th>Ausentes</th>
                        <th>% Asistencia</th>
                        <th>Indicador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stats['by_course'] as $course): ?>
                    <tr>
                        <td><?= $course['course_name'] ?></td>
                        <td><?= $course['total'] ?></td>
                        <td><?= $course['presente'] ?></td>
                        <td><?= $course['ausente'] ?></td>
                        <td><?= $course['percentage'] ?>%</td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $course['percentage'] ?>%;"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Top 10 - Estudiantes con Más Ausencias</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Estudiante</th>
                        <th>Curso</th>
                        <th>Total Ausencias</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stats['by_student'] as $index => $student): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $student['student_name'] ?></td>
                        <td><?= $student['course_name'] ?></td>
                        <td><strong style="color: #dc3545;"><?= $student['total_absences'] ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Tendencia Diaria</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Registros</th>
                        <th>Presentes</th>
                        <th>% Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stats['by_day'] as $day): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($day['date'])) ?></td>
                        <td><?= $day['total'] ?></td>
                        <td><?= $day['presente'] ?></td>
                        <td><?= $day['percentage'] ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>