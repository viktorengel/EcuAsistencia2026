<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Asistencia - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Mi Asistencia
</div>

<div class="container">

<?php
$total     = count($attendances);
$presente  = count(array_filter($attendances, fn($a) => $a['status'] === 'presente'));
$ausente   = count(array_filter($attendances, fn($a) => $a['status'] === 'ausente'));
$tardanza  = count(array_filter($attendances, fn($a) => $a['status'] === 'tardanza'));
$pctAsist  = $total > 0 ? round(($presente / $total) * 100, 1) : 0;
$pct       = fn($v) => $total > 0 ? round($v / $total * 100, 1) : 0;
?>

    <!-- Header -->
    <div class="page-header green">
        <div class="ph-icon">🎓</div>
        <div>
            <h1>Mi Asistencia</h1>
            <p>Historial completo de tu asistencia escolar</p>
        </div>
        <div class="ph-actions">
            <a href="?action=my_justifications" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.5);">
                📝 Mis Justificaciones
            </a>
        </div>
    </div>

    <!-- Stat cards -->
    <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
        <div class="stat-card gray">
            <div class="number"><?= $total ?></div>
            <div class="label">Total Registros</div>
        </div>
        <div class="stat-card green">
            <div class="number"><?= $pctAsist ?>%</div>
            <div class="label">Asistencia (<?= $presente ?> presentes)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pctAsist ?>%;background:#28a745;"></div></div>
        </div>
        <div class="stat-card red">
            <div class="number"><?= $ausente ?></div>
            <div class="label">Ausencias (<?= $pct($ausente) ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct($ausente) ?>%;background:#dc3545;"></div></div>
        </div>
        <div class="stat-card yellow">
            <div class="number"><?= $tardanza ?></div>
            <div class="label">Tardanzas (<?= $pct($tardanza) ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct($tardanza) ?>%;background:#ffc107;"></div></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-wrap">
        <div class="table-info">
            <span>📋 <strong><?= $total ?></strong> registros</span>
        </div>
        <?php if(empty($attendances)): ?>
        <div class="empty-state">
            <div class="icon">📋</div>
            <p>No hay registros de asistencia.</p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Curso</th>
                    <th>Asignatura</th>
                    <th>Jornada</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Observación</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($attendances as $att):
                    $badgeClass = match($att['status']) {
                        'presente'    => 'badge-green',
                        'ausente'     => 'badge-red',
                        'tardanza'    => 'badge-yellow',
                        'justificado' => 'badge-teal',
                        default       => 'badge-gray'
                    };
                    $label = match($att['status']) {
                        'presente'    => 'Presente',
                        'ausente'     => 'Ausente',
                        'tardanza'    => 'Tardanza',
                        'justificado' => 'Justificado',
                        default       => ucfirst($att['status'])
                    };
                ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($att['date'])) ?></td>
                    <td><?= htmlspecialchars($att['course_name']) ?></td>
                    <td><?= htmlspecialchars($att['subject_name']) ?></td>
                    <td style="color:#666;"><?= ucfirst($att['shift_name']) ?></td>
                    <td style="white-space:nowrap;"><?= htmlspecialchars($att['hour_period']) ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $label ?></span></td>
                    <td style="color:#888;font-size:0.82rem;"><?= htmlspecialchars($att['observation'] ?? '—') ?></td>
                    <td>
                        <?php if($att['status'] === 'ausente'): ?>
                            <a href="?action=submit_justification&attendance_id=<?= $att['id'] ?>"
                               class="btn btn-warning btn-sm">✏️ Justificar</a>
                        <?php else: ?>
                            <span style="color:#ccc;">—</span>
                        <?php endif; ?>
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
