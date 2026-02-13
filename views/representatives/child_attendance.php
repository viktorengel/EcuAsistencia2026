<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asistencia - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge { padding: 5px 10px; border-radius: 3px; color: white; font-size: 12px; }
        .badge-presente { background: #28a745; }
        .badge-ausente { background: #dc3545; }
        .badge-tardanza { background: #ffc107; color: #333; }
        .badge-justificado { background: #17a2b8; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 32px; font-weight: bold; margin-bottom: 5px; }
        .stat-label { font-size: 13px; opacity: 0.9; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php
        $total = count($attendances);
        $presente = count(array_filter($attendances, fn($a) => $a['status'] === 'presente'));
        $ausente = count(array_filter($attendances, fn($a) => $a['status'] === 'ausente'));
        $tardanza = count(array_filter($attendances, fn($a) => $a['status'] === 'tardanza'));
        $asistencia = $total > 0 ? round(($presente / $total) * 100, 1) : 0;
        ?>

        <div class="stats">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stat-number"><?= $total ?></div>
                <div class="stat-label">Registros</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="stat-number"><?= $asistencia ?>%</div>
                <div class="stat-label">Asistencia</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                <div class="stat-number"><?= $ausente ?></div>
                <div class="stat-label">Ausencias</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="stat-number"><?= $tardanza ?></div>
                <div class="stat-label">Tardanzas</div>
            </div>
        </div>

        <div class="card">
            <h2>Historial Completo</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Asignatura</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($attendances as $att): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($att['date'])) ?></td>
                        <td><?= $att['subject_name'] ?></td>
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
    </div>
</body>
</html>