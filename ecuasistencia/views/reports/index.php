<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Reportes
</div>

<div class="container">

    <!-- Header -->
    <div class="page-header purple">
        <div class="ph-icon">📄</div>
        <div>
            <h1>Generación de Reportes</h1>
            <p>Exporta informes de asistencia en PDF o Excel</p>
        </div>
    </div>

    <!-- Configuración -->
    <div class="panel">
        <h3 class="blue">⚙️ Configuración del Reporte</h3>
        <div class="alert alert-info" style="margin-bottom:16px;">
            <strong>Tipos disponibles:</strong> Vista Previa (pantalla) · PDF (formato institucional) · Excel (análisis de datos)
        </div>
        <form method="POST" id="report-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Curso</label>
                    <select name="course_id" class="form-control" required>
                        <option value="">— Seleccionar curso —</option>
                        <?php foreach($courses as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= isset($course) && $course['id'] == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?> - <?= htmlspecialchars($c['shift_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $startDate ?? date('Y-m-01') ?>" required>
                </div>
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $endDate ?? date('Y-m-d') ?>" required>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;flex-wrap:wrap;">
                <button type="submit" name="preview" class="btn btn-primary">👁️ Vista Previa</button>
                <button type="button" class="btn btn-danger" onclick="generateReport('pdf')" <?= empty($data) ? 'disabled title="Genera una Vista Previa primero"' : '' ?>>
                    📄 Generar PDF
                </button>
                <button type="button" class="btn btn-success" onclick="generateReport('excel')" <?= empty($data) ? 'disabled title="Genera una Vista Previa primero"' : '' ?>>
                    📊 Generar Excel
                </button>
            </div>
        </form>
    </div>

    <!-- Vista previa -->
    <?php if(!empty($data)): ?>
    <?php
    $totalPresente    = count(array_filter($data, fn($d) => $d['status'] === 'presente'));
    $totalAusente     = count(array_filter($data, fn($d) => $d['status'] === 'ausente'));
    $totalTardanza    = count(array_filter($data, fn($d) => $d['status'] === 'tardanza'));
    $totalJustificado = count(array_filter($data, fn($d) => $d['status'] === 'justificado'));
    $total            = count($data);
    $porcentaje       = $total > 0 ? round(($totalPresente / $total) * 100, 1) : 0;
    ?>

    <div class="panel">
        <h3 class="blue">📋 Vista Previa del Reporte</h3>
        <div class="alert alert-info" style="margin-bottom:14px;">
            <strong>Curso:</strong> <?= htmlspecialchars($course['name']) ?> — <?= htmlspecialchars($course['shift_name']) ?> &nbsp;|&nbsp;
            <strong>Período:</strong> <?= date('d/m/Y', strtotime($startDate)) ?> al <?= date('d/m/Y', strtotime($endDate)) ?> &nbsp;|&nbsp;
            <strong>Total:</strong> <?= $total ?> registros
        </div>

        <!-- Mini stats -->
        <div class="stats-row" style="margin-bottom:16px;">
            <div class="stat-card green">
                <div class="number"><?= $totalPresente ?></div>
                <div class="label">Presentes (<?= $porcentaje ?>%)</div>
                <div class="progress-bar"><div class="progress-fill" style="width:<?= $porcentaje ?>%;background:#28a745;"></div></div>
            </div>
            <div class="stat-card red">
                <div class="number"><?= $totalAusente ?></div>
                <div class="label">Ausentes</div>
            </div>
            <div class="stat-card yellow">
                <div class="number"><?= $totalTardanza ?></div>
                <div class="label">Tardanzas</div>
            </div>
            <div class="stat-card teal">
                <div class="number"><?= $totalJustificado ?></div>
                <div class="label">Justificados</div>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estudiante</th>
                        <th>Asignatura</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $statusBadge = [
                        'presente'    => 'badge-green',
                        'ausente'     => 'badge-red',
                        'tardanza'    => 'badge-yellow',
                        'justificado' => 'badge-teal',
                    ];
                    foreach($data as $row):
                        $bc = $statusBadge[$row['status']] ?? 'badge-gray';
                    ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['date'])) ?></td>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['subject_name']) ?></td>
                        <td><?= htmlspecialchars($row['hour_period']) ?></td>
                        <td><span class="badge <?= $bc ?>"><?= ucfirst($row['status']) ?></span></td>
                        <td style="color:#888;"><?= htmlspecialchars($row['observation'] ?: '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php elseif($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="panel">
        <div class="empty-state">
            <div class="icon">📋</div>
            <p>No hay registros de asistencia para el período seleccionado</p>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
function generateReport(type) {
    var form = document.getElementById('report-form');
    if (!form.checkValidity()) { form.reportValidity(); return; }
    var tf = document.createElement('form');
    tf.method = 'POST';
    tf.style.display = 'none';
    tf.action = type === 'pdf' ? '?action=generate_pdf' : '?action=generate_excel';
    ['course_id','start_date','end_date'].forEach(function(n) {
        var i = document.createElement('input');
        i.type = 'hidden'; i.name = n;
        i.value = form.querySelector('[name="'+n+'"]').value;
        tf.appendChild(i);
    });
    document.body.appendChild(tf);
    tf.submit();
    document.body.removeChild(tf);
}
</script>

</body>
</html>
