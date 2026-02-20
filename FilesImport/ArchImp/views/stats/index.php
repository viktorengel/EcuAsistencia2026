<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Estadísticas
</div>

<div class="container-wide">

    <!-- Header -->
    <div class="page-header purple">
        <div class="ph-icon">📈</div>
        <div>
            <h1>Estadísticas de Asistencia</h1>
            <p>Análisis y métricas del período seleccionado</p>
        </div>
    </div>

    <!-- Filtro de fechas -->
    <div class="filters">
        <h3>🔍 Período de análisis</h3>
        <form method="GET">
            <input type="hidden" name="action" value="stats">
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="start_date" value="<?= $_GET['start_date'] ?? date('Y-m-01') ?>">
                </div>
                <div class="filter-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="end_date" value="<?= $_GET['end_date'] ?? date('Y-m-d') ?>">
                </div>
                <div class="filter-group" style="display:flex;align-items:flex-end;">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Stats globales -->
    <?php
    $total = $stats['overall']['total_records'] ?? 0;
    $pres  = $stats['overall']['presente'] ?? 0;
    $aus   = $stats['overall']['ausente'] ?? 0;
    $tard  = $stats['overall']['tardanza'] ?? 0;
    $just  = $stats['overall']['justificado'] ?? 0;
    $pct   = $total > 0 ? round(($pres / $total) * 100, 1) : 0;
    ?>
    <div class="stats-row">
        <div class="stat-card gray">
            <div class="number"><?= $total ?></div>
            <div class="label">Total Registros</div>
        </div>
        <div class="stat-card green">
            <div class="number"><?= $pres ?></div>
            <div class="label">Presentes (<?= $pct ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct ?>%;background:#28a745;"></div></div>
        </div>
        <div class="stat-card red">
            <div class="number"><?= $aus ?></div>
            <div class="label">Ausentes (<?= $total > 0 ? round(($aus/$total)*100,1) : 0 ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $total>0?round(($aus/$total)*100):0 ?>%;background:#dc3545;"></div></div>
        </div>
        <div class="stat-card yellow">
            <div class="number"><?= $tard ?></div>
            <div class="label">Tardanzas</div>
        </div>
        <div class="stat-card teal">
            <div class="number"><?= $just ?></div>
            <div class="label">Justificados</div>
        </div>
    </div>

    <!-- Asistencia por curso -->
    <div class="panel">
        <h3 class="blue">🏫 Asistencia por Curso</h3>
        <div class="table-wrap" style="margin-top:8px;">
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Total</th>
                        <th>Presentes</th>
                        <th>Ausentes</th>
                        <th>% Asistencia</th>
                        <th style="min-width:120px;">Indicador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stats['by_course'] as $course):
                        $color = $course['percentage'] >= 90 ? '#28a745' : ($course['percentage'] >= 75 ? '#ffc107' : '#dc3545');
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($course['course_name']) ?></strong></td>
                        <td><?= $course['total'] ?></td>
                        <td><?= $course['presente'] ?></td>
                        <td><?= $course['ausente'] ?></td>
                        <td><span style="font-weight:700;color:<?= $color ?>;"><?= $course['percentage'] ?>%</span></td>
                        <td>
                            <div class="progress-bar" style="height:10px;">
                                <div class="progress-fill" style="width:<?= $course['percentage'] ?>%;background:<?= $color ?>;"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top 10 ausencias -->
    <div class="panel">
        <h3 class="red">⚠️ Top 10 — Estudiantes con Más Ausencias</h3>
        <?php if(!empty($stats['by_student'])): ?>
        <?php $maxAbs = $stats['by_student'][0]['total_absences'] ?? 1; ?>
        <div style="margin-top:8px;">
            <?php foreach($stats['by_student'] as $i => $student): ?>
            <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f0f0f0;">
                <span style="font-size:0.75rem;color:#999;min-width:20px;text-align:right;"><?= $i+1 ?>.</span>
                <span style="flex:1;font-size:0.88rem;"><strong><?= htmlspecialchars($student['student_name']) ?></strong>
                    <span style="color:#888;font-size:0.8rem;margin-left:6px;"><?= htmlspecialchars($student['course_name']) ?></span>
                </span>
                <div style="flex:2;">
                    <div class="progress-bar" style="height:8px;">
                        <div class="progress-fill" style="width:<?= round(($student['total_absences']/$maxAbs)*100) ?>%;background:#dc3545;"></div>
                    </div>
                </div>
                <span style="font-weight:700;color:#dc3545;min-width:28px;text-align:right;"><?= $student['total_absences'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p style="color:#999;font-size:0.88rem;padding:12px 0;">No hay datos de ausencias en este período.</p>
        <?php endif; ?>
    </div>

    <!-- Tendencia diaria -->
    <div class="panel">
        <h3 class="blue">📅 Tendencia Diaria</h3>
        <div class="table-wrap" style="margin-top:8px;">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Registros</th>
                        <th>Presentes</th>
                        <th>% Asistencia</th>
                        <th style="min-width:120px;">Indicador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stats['by_day'] as $day):
                        $dc = $day['percentage'] >= 90 ? '#28a745' : ($day['percentage'] >= 75 ? '#ffc107' : '#dc3545');
                    ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($day['date'])) ?></td>
                        <td><?= $day['total'] ?></td>
                        <td><?= $day['presente'] ?></td>
                        <td><span style="font-weight:600;color:<?= $dc ?>;"><?= $day['percentage'] ?>%</span></td>
                        <td>
                            <div class="progress-bar" style="height:8px;">
                                <div class="progress-fill" style="width:<?= $day['percentage'] ?>%;background:<?= $dc ?>;"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
