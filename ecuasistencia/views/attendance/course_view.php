<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Asistencia del Curso - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Asistencia del Curso
</div>

<div class="container">

    <?php if(empty($courses)): ?>
    <div class="alert alert-warning">
        ⚠️ No tiene cursos asignados.
        <?php if(Security::hasRole('docente')): ?>
            Contacte con la autoridad para que le asignen cursos.
        <?php endif; ?>
    </div>
    <?php else: ?>

    <!-- Header -->
    <div class="page-header blue">
        <div class="ph-icon">📊</div>
        <div>
            <h1>Asistencia del Curso</h1>
            <p>Consulta estadísticas de asistencia por curso y período</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="panel">
        <form method="GET" id="filterForm">
            <input type="hidden" name="action" value="view_course_attendance">
            <div class="form-row" style="align-items:flex-end;">
                <div class="form-group" style="flex:2;">
                    <label>Curso *</label>
                    <select name="course_id" class="form-control" required>
                        <option value="">Seleccionar curso...</option>
                        <?php foreach($courses as $c):
                            $label = htmlspecialchars($c['name']);
                            if (!empty($c['is_tutor'])) $label .= ' ⭐ (Mi curso)';
                        ?>
                            <option value="<?= $c['id'] ?>" <?= isset($courseId) && $courseId == $c['id'] ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="start_date" class="form-control"
                           value="<?= htmlspecialchars($startDate ?? date('Y-m-01')) ?>">
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="end_date" class="form-control"
                           value="<?= htmlspecialchars($endDate ?? date('Y-m-d')) ?>">
                </div>
                <div class="form-group" style="flex:0 0 auto;">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary" style="width:100%;">🔍 Consultar</button>
                </div>
            </div>
        </form>
    </div>

    <?php if(isset($courseId) && !empty($attendanceData)):
        $totalPresente    = array_sum(array_column($attendanceData, 'presente'));
        $totalAusente     = array_sum(array_column($attendanceData, 'ausente'));
        $totalTardanza    = array_sum(array_column($attendanceData, 'tardanza'));
        $totalJustificado = array_sum(array_column($attendanceData, 'justificado'));
        $totalRegistros   = array_sum(array_column($attendanceData, 'total_registros'));
        $pct = fn($v) => $totalRegistros > 0 ? round($v / $totalRegistros * 100, 1) : 0;
    ?>

    <!-- Stat cards -->
    <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
        <div class="stat-card gray">
            <div class="number"><?= $totalRegistros ?></div>
            <div class="label">Total Registros</div>
        </div>
        <div class="stat-card green">
            <div class="number"><?= $totalPresente ?></div>
            <div class="label">Presentes (<?= $pct($totalPresente) ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct($totalPresente) ?>%;background:#28a745;"></div></div>
        </div>
        <div class="stat-card red">
            <div class="number"><?= $totalAusente ?></div>
            <div class="label">Ausentes (<?= $pct($totalAusente) ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct($totalAusente) ?>%;background:#dc3545;"></div></div>
        </div>
        <div class="stat-card yellow">
            <div class="number"><?= $totalTardanza ?></div>
            <div class="label">Tardanzas (<?= $pct($totalTardanza) ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct($totalTardanza) ?>%;background:#ffc107;"></div></div>
        </div>
        <div class="stat-card teal">
            <div class="number"><?= $totalJustificado ?></div>
            <div class="label">Justificados (<?= $pct($totalJustificado) ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct($totalJustificado) ?>%;background:#17a2b8;"></div></div>
        </div>
    </div>

    <!-- Tabla estudiantes -->
    <div class="table-wrap">
        <div class="table-info">
            <span>📅 Período: <?= date('d/m/Y', strtotime($startDate)) ?> al <?= date('d/m/Y', strtotime($endDate)) ?></span>
            <span><strong><?= count($attendanceData) ?></strong> estudiantes</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estudiante</th>
                    <th style="text-align:center;">Total</th>
                    <th style="text-align:center;">Presentes</th>
                    <th style="text-align:center;">Ausentes</th>
                    <th style="text-align:center;">Tardanzas</th>
                    <th style="text-align:center;">Justificados</th>
                    <th style="text-align:center;">% Asistencia</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($attendanceData as $data):
                    $pctRow = $data['total_registros'] > 0
                        ? round(($data['presente'] / $data['total_registros']) * 100, 1) : 0;
                    $pctColor = $pctRow >= 90 ? '#28a745' : ($pctRow >= 75 ? '#e6ac00' : '#dc3545');
                ?>
                <tr>
                    <td style="color:#999;"><?= $i++ ?></td>
                    <td><strong><?= htmlspecialchars($data['student_name']) ?></strong></td>
                    <td style="text-align:center;"><?= $data['total_registros'] ?></td>
                    <td style="text-align:center;"><span class="badge badge-green"><?= $data['presente'] ?></span></td>
                    <td style="text-align:center;"><span class="badge badge-red"><?= $data['ausente'] ?></span></td>
                    <td style="text-align:center;"><span class="badge badge-yellow"><?= $data['tardanza'] ?></span></td>
                    <td style="text-align:center;"><span class="badge badge-teal"><?= $data['justificado'] ?></span></td>
                    <td style="text-align:center;font-weight:700;color:<?= $pctColor ?>;"><?= $pctRow ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php elseif(isset($courseId)): ?>
    <div class="empty-state">
        <div class="icon">📋</div>
        <p>No hay registros de asistencia para el período seleccionado.</p>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="icon">👆</div>
        <p>Seleccione un curso y período para ver la asistencia.</p>
    </div>
    <?php endif; ?>

    <?php endif; ?>

</div>
</body>
</html>
