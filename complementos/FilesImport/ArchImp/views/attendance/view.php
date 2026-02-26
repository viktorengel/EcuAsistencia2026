<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Asistencias - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Asistencia &rsaquo;
    Ver Asistencias
</div>

<div class="container">

    <!-- Header -->
    <div class="page-header" style="background:linear-gradient(135deg,#1565c0,#1976d2);">
        <div class="ph-icon">📋</div>
        <div>
            <h1>Ver Asistencias</h1>
            <p>Consulta el registro de asistencia por curso y fecha</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="panel" style="margin-bottom:20px;">
        <h3 style="margin-bottom:16px;font-size:.95rem;color:#555;">🔍 Filtros de Búsqueda</h3>
        <form method="POST" id="filterForm">
            <div class="form-row" style="align-items:flex-end;">
                <div class="form-group" style="flex:2;">
                    <label>Curso</label>
                    <select name="course_id" class="form-control" required>
                        <option value="">Seleccionar curso...</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['id'] ?>"
                                <?= (isset($_POST['course_id']) && $_POST['course_id'] == $course['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course['name']) ?>
                                — <?= ucfirst($course['shift_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Fecha</label>
                    <input type="date" name="date" class="form-control"
                           value="<?= isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d') ?>"
                           max="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group" style="flex:0 0 auto;">
                    <button type="submit" class="btn btn-primary">🔍 Buscar</button>
                </div>
            </div>
        </form>
    </div>

    <?php if($_SERVER['REQUEST_METHOD'] === 'POST'): ?>

        <?php if(empty($attendances)): ?>
            <!-- Sin resultados -->
            <div class="empty-state">
                <div class="icon">📭</div>
                <p>No se encontraron registros de asistencia para los filtros seleccionados.</p>
                <small style="color:#aaa;">Verifica que el docente haya registrado asistencia para este curso y fecha.</small>
            </div>

        <?php else:
            /* ---- Calcular estadísticas ---- */
            $stats = ['presente'=>0,'ausente'=>0,'tardanza'=>0,'justificado'=>0];
            $students = [];
            foreach($attendances as $att) {
                $s = $att['status'];
                if(isset($stats[$s])) $stats[$s]++;
                $students[$att['last_name'].' '.$att['first_name']] = true;
            }
            $total      = count($attendances);
            $totalStu   = count($students);
            $pctPresente = $total > 0 ? round(($stats['presente'] + $stats['justificado']) / $total * 100) : 0;

            $selectedCourse = '';
            foreach($courses as $c) {
                if($c['id'] == $_POST['course_id']) {
                    $selectedCourse = $c['name'] . ' — ' . ucfirst($c['shift_name']);
                    break;
                }
            }
        ?>

        <!-- Stat cards -->
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:20px;">
            <div class="stat-card">
                <div class="stat-icon" style="background:#e3f2fd;">📊</div>
                <div class="stat-value"><?= $total ?></div>
                <div class="stat-label">Total registros</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#e8f5e9;">✅</div>
                <div class="stat-value" style="color:#2e7d32;"><?= $stats['presente'] ?></div>
                <div class="stat-label">Presentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#ffebee;">❌</div>
                <div class="stat-value" style="color:#c62828;"><?= $stats['ausente'] ?></div>
                <div class="stat-label">Ausentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#fffde7;">⏰</div>
                <div class="stat-value" style="color:#f57f17;"><?= $stats['tardanza'] ?></div>
                <div class="stat-label">Tardanzas</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#e0f7fa;">📝</div>
                <div class="stat-value" style="color:#00838f;"><?= $stats['justificado'] ?></div>
                <div class="stat-label">Justificados</div>
            </div>
        </div>

        <!-- Barra de asistencia global -->
        <div class="panel" style="margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <span style="font-size:.9rem;color:#555;">
                    📚 <strong><?= htmlspecialchars($selectedCourse) ?></strong>
                    &nbsp;|&nbsp; 📅 <?= date('d/m/Y', strtotime($_POST['date'])) ?>
                    &nbsp;|&nbsp; 👥 <?= $totalStu ?> estudiante<?= $totalStu!=1?'s':'' ?>
                </span>
                <span style="font-weight:700;font-size:1.1rem;color:<?= $pctPresente>=90?'#2e7d32':($pctPresente>=75?'#f57f17':'#c62828') ?>">
                    <?= $pctPresente ?>% asistencia efectiva
                </span>
            </div>
            <div style="background:#f0f0f0;border-radius:6px;height:10px;overflow:hidden;">
                <div style="height:100%;border-radius:6px;width:<?= $pctPresente ?>%;
                    background:<?= $pctPresente>=90?'#4caf50':($pctPresente>=75?'#ff9800':'#f44336') ?>;
                    transition:width .4s;">
                </div>
            </div>
        </div>

        <!-- Tabla de resultados -->
        <div class="table-wrap">
            <div class="table-info">
                <span>📋 <strong>Registros de Asistencia</strong> — <?= $total ?></span>
                <div style="display:flex;gap:8px;align-items:center;">
                    <input type="text" id="searchTable" class="form-control"
                           placeholder="🔎 Buscar estudiante..."
                           style="width:200px;font-size:13px;"
                           oninput="filterTable(this.value)">
                </div>
            </div>
            <table id="attendanceTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Estudiante</th>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th style="text-align:center;">Hora</th>
                        <th style="text-align:center;">Estado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach($attendances as $att):
                        $s = $att['status'];
                        $badgeMap = [
                            'presente'    => 'badge-green',
                            'ausente'     => 'badge-red',
                            'tardanza'    => 'badge-yellow',
                            'justificado' => 'badge-teal',
                        ];
                        $labelMap = [
                            'presente'    => '✓ Presente',
                            'ausente'     => '✗ Ausente',
                            'tardanza'    => '⏰ Tardanza',
                            'justificado' => '📝 Justificado',
                        ];
                        $badge = $badgeMap[$s] ?? 'badge-gray';
                        $label = $labelMap[$s] ?? ucfirst($s);
                    ?>
                    <tr>
                        <td style="color:#999;font-size:12px;"><?= $i++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($att['last_name'] . ' ' . $att['first_name']) ?></strong>
                        </td>
                        <td style="color:#555;"><?= htmlspecialchars($att['subject_name']) ?></td>
                        <td style="font-size:13px;color:#666;"><?= htmlspecialchars($att['teacher_name']) ?></td>
                        <td style="text-align:center;">
                            <span style="font-size:13px;background:#f0f0f0;padding:3px 8px;border-radius:4px;">
                                <?= htmlspecialchars($att['hour_period']) ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge <?= $badge ?>"><?= $label ?></span>
                        </td>
                        <td style="font-size:13px;color:#777;">
                            <?= $att['observation'] ? htmlspecialchars($att['observation']) : '<span style="color:#ccc;">—</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Leyenda -->
        <div style="margin-top:12px;display:flex;gap:12px;flex-wrap:wrap;">
            <span class="badge badge-green">✓ Presente</span>
            <span class="badge badge-red">✗ Ausente</span>
            <span class="badge badge-yellow">⏰ Tardanza</span>
            <span class="badge badge-teal">📝 Justificado</span>
        </div>

        <?php endif; ?>
    <?php endif; ?>

</div>

<script>
function filterTable(q) {
    q = q.toLowerCase();
    const rows = document.querySelectorAll('#attendanceTable tbody tr');
    rows.forEach(r => {
        const name = r.cells[1] ? r.cells[1].textContent.toLowerCase() : '';
        r.style.display = name.includes(q) ? '' : 'none';
    });
}
</script>

</body>
</html>