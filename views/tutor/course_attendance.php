<?php
// views/tutor/course_attendance.php

$statusLabels = [
    'presente'    => ['label' => 'Presente',    'color' => '#28a745', 'bg' => '#d4edda'],
    'ausente'     => ['label' => 'Ausente',     'color' => '#dc3545', 'bg' => '#f8d7da'],
    'tardanza'    => ['label' => 'Tardanza',    'color' => '#ffc107', 'bg' => '#fff3cd'],
    'justificado' => ['label' => 'Justificado', 'color' => '#17a2b8', 'bg' => '#d1ecf1'],
];
$pct = function($part, $total) {
    return $total > 0 ? round(($part / $total) * 100, 1) : 0;
};
$hasFilters = !empty($filters['subject_id']) || !empty($filters['student_id'])
           || !empty($filters['status'])     || !empty($filters['start_date'])
           || !empty($filters['end_date']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia del Curso - EcuAsist</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f6f9; color: #333; font-size: 14px; }
        .breadcrumb { padding: 10px 24px; background: #fff; border-bottom: 1px solid #e0e0e0; font-size: 0.83rem; color: #888; }
        .breadcrumb a { color: #007bff; text-decoration: none; }
        .container { max-width: 1200px; margin: 24px auto; padding: 0 16px; }
        .course-header { background: linear-gradient(135deg,#007bff,#0056b3); color:#fff; border-radius:10px; padding:20px 24px; margin-bottom:20px; display:flex; align-items:center; gap:16px; }
        .course-header .icon { font-size:2.2rem; }
        .course-header h1 { font-size:1.3rem; font-weight:700; }
        .course-header p  { font-size:0.85rem; opacity:0.85; margin-top:4px; }

        /* Stats */
        #stats-section { display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
        .stat-card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:14px 18px; flex:1; min-width:120px; border-top:4px solid #ddd; }
        .stat-card .number { font-size:1.8rem; font-weight:700; }
        .stat-card .label  { font-size:0.75rem; color:#888; margin-top:2px; text-transform:uppercase; letter-spacing:0.5px; }
        .stat-card.total    { border-top-color:#6c757d; } .stat-card.total .number    { color:#6c757d; }
        .stat-card.presente { border-top-color:#28a745; } .stat-card.presente .number { color:#28a745; }
        .stat-card.ausente  { border-top-color:#dc3545; } .stat-card.ausente .number  { color:#dc3545; }
        .stat-card.tardanza { border-top-color:#ffc107; } .stat-card.tardanza .number { color:#e6ac00; }
        .stat-card.justif   { border-top-color:#17a2b8; } .stat-card.justif .number   { color:#17a2b8; }
        .progress-bar { height:8px; background:#e9ecef; border-radius:4px; overflow:hidden; margin-top:6px; }
        .progress-fill { height:100%; border-radius:4px; transition:width 0.4s ease; }

        /* Top ausencias */
        .top-absences { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:16px; margin-bottom:20px; }
        .top-absences h3 { font-size:0.9rem; font-weight:600; margin-bottom:12px; color:#dc3545; }
        .absence-item { display:flex; align-items:center; gap:10px; padding:6px 0; border-bottom:1px solid #f0f0f0; }
        .absence-item:last-child { border-bottom:none; }
        .absence-name  { flex:1; font-size:0.85rem; }
        .absence-count { font-weight:700; color:#dc3545; font-size:0.95rem; min-width:28px; text-align:right; }
        .absence-bar   { flex:2; }

        /* Filtros */
        .filters { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:16px; margin-bottom:16px; }
        .filters h3 { font-size:0.85rem; font-weight:600; color:#555; margin-bottom:12px; }
        .filter-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:10px; }
        .filter-grid select,
        .filter-grid input { width:100%; padding:7px 10px; border:1px solid #ccc; border-radius:6px; font-size:0.85rem; transition:border-color 0.2s; }
        .filter-grid select:focus,
        .filter-grid input:focus { border-color:#007bff; outline:none; box-shadow:0 0 0 2px rgba(0,123,255,0.15); }
        .filter-grid select.on,
        .filter-grid input.on { border-color:#007bff; background:#f0f7ff; }
        .filter-banner { background:#fff3cd; border:1px solid #ffc107; border-radius:6px; padding:7px 12px; font-size:0.82rem; color:#856404; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center; }
        .filter-banner a { color:#856404; font-weight:700; text-decoration:none; cursor:pointer; }
        .btn { padding:7px 16px; border-radius:6px; border:none; cursor:pointer; font-size:0.85rem; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:5px; transition:opacity 0.15s; }
        .btn:hover { opacity:0.85; }
        .btn-outline { background:#fff; color:#555; border:1px solid #ccc; }

        /* Tabla */
        .table-section { position:relative; }
        .loading-overlay { display:none; position:absolute; inset:0; background:rgba(255,255,255,0.75); border-radius:8px; z-index:5; align-items:center; justify-content:center; }
        .loading-overlay.on { display:flex; }
        .spinner { width:28px; height:28px; border:3px solid #e0e0e0; border-top-color:#007bff; border-radius:50%; animation:spin 0.7s linear infinite; }
        @keyframes spin { to { transform:rotate(360deg); } }
        .table-wrap { background:#fff; border:1px solid #e0e0e0; border-radius:8px; overflow:hidden; }
        .table-info { padding:12px 16px; border-bottom:1px solid #e0e0e0; font-size:0.83rem; color:#666; display:flex; justify-content:space-between; align-items:center; }
        table { width:100%; border-collapse:collapse; }
        thead th { background:#f8f9fa; padding:10px 12px; text-align:left; font-size:0.8rem; font-weight:600; color:#555; border-bottom:2px solid #e0e0e0; white-space:nowrap; }
        tbody td { padding:9px 12px; border-bottom:1px solid #f0f0f0; font-size:0.85rem; }
        tbody tr:hover { background:#f8fbff; }
        tbody tr:last-child td { border-bottom:none; }
        .status-badge { display:inline-block; padding:2px 10px; border-radius:12px; font-size:0.75rem; font-weight:600; }
        .empty-state { text-align:center; padding:50px 20px; color:#999; }
        .empty-state .icon { font-size:2.5rem; margin-bottom:10px; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=dashboard">Mi Dashboard</a> &rsaquo;
    Asistencia de Mi Curso
</div>

<div class="container">

    <div class="course-header">
        <div class="icon">🎓</div>
        <div>
            <h1>📋 Asistencia — <?= htmlspecialchars($course['name']) ?></h1>
            <p>
                Jornada: <?= htmlspecialchars($course['shift_name']) ?> &nbsp;·&nbsp;
                Nivel: <?= htmlspecialchars($course['grade_level']) ?> &nbsp;·&nbsp;
                Paralelo: <?= htmlspecialchars($course['parallel']) ?> &nbsp;·&nbsp;
                Todas las asignaturas
            </p>
        </div>
    </div>

    <!-- Stats — actualizado por AJAX -->
    <div id="stats-section">
        <?php if ($stats && $stats['total'] > 0): ?>
        <div class="stat-card total"><div class="number"><?= $stats['total'] ?></div><div class="label">Total registros</div></div>
        <div class="stat-card presente">
            <div class="number"><?= $stats['presente'] ?></div>
            <div class="label">Presentes (<?= $pct($stats['presente'], $stats['total']) ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct($stats['presente'], $stats['total']) ?>%;background:#28a745;"></div></div>
        </div>
        <div class="stat-card ausente">
            <div class="number"><?= $stats['ausente'] ?></div>
            <div class="label">Ausentes (<?= $pct($stats['ausente'], $stats['total']) ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct($stats['ausente'], $stats['total']) ?>%;background:#dc3545;"></div></div>
        </div>
        <div class="stat-card tardanza">
            <div class="number"><?= $stats['tardanza'] ?></div>
            <div class="label">Tardanzas (<?= $pct($stats['tardanza'], $stats['total']) ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct($stats['tardanza'], $stats['total']) ?>%;background:#ffc107;"></div></div>
        </div>
        <div class="stat-card justif">
            <div class="number"><?= $stats['justificado'] ?></div>
            <div class="label">Justificados (<?= $pct($stats['justificado'], $stats['total']) ?>%)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pct($stats['justificado'], $stats['total']) ?>%;background:#17a2b8;"></div></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Top ausencias (estático) -->
    <?php if (!empty($topAbsences)): ?>
    <div class="top-absences">
        <h3>⚠️ Top estudiantes con más ausencias</h3>
        <?php $maxAbs = $topAbsences[0]['total_ausencias'] ?? 1;
        foreach ($topAbsences as $i => $row): ?>
        <div class="absence-item">
            <span style="font-size:0.75rem;color:#999;min-width:18px;"><?= $i+1 ?>.</span>
            <span class="absence-name"><?= htmlspecialchars($row['student_name']) ?></span>
            <div class="absence-bar"><div class="progress-bar"><div class="progress-fill" style="width:<?= round(($row['total_ausencias']/$maxAbs)*100) ?>%;background:#dc3545;"></div></div></div>
            <span class="absence-count"><?= $row['total_ausencias'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Filtros — con name para fallback, interceptados por JS -->
    <div class="filters">
        <h3>🔍 Filtrar asistencias</h3>
        <div id="filter-banner" style="display:<?= $hasFilters ? 'flex' : 'none' ?>;" class="filter-banner">
            <span>⚠️ Filtros activos</span>
            <a onclick="clearFilters()">✕ Limpiar filtros</a>
        </div>
        <div class="filter-grid">
            <div>
                <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Asignatura</label>
                <select name="subject_id" id="f_subject" class="<?= !empty($filters['subject_id']) ? 'on' : '' ?>">
                    <option value="">— Todas —</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($filters['subject_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Estudiante</label>
                <select name="student_id" id="f_student" class="<?= !empty($filters['student_id']) ? 'on' : '' ?>">
                    <option value="">— Todos —</option>
                    <?php foreach ($students as $st): ?>
                        <option value="<?= $st['id'] ?>" <?= ($filters['student_id'] ?? '') == $st['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($st['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Estado</label>
                <select name="status" id="f_status" class="<?= !empty($filters['status']) ? 'on' : '' ?>">
                    <option value="">— Todos —</option>
                    <option value="presente"    <?= ($filters['status'] ?? '') === 'presente'    ? 'selected' : '' ?>>Presente</option>
                    <option value="ausente"     <?= ($filters['status'] ?? '') === 'ausente'     ? 'selected' : '' ?>>Ausente</option>
                    <option value="tardanza"    <?= ($filters['status'] ?? '') === 'tardanza'    ? 'selected' : '' ?>>Tardanza</option>
                    <option value="justificado" <?= ($filters['status'] ?? '') === 'justificado' ? 'selected' : '' ?>>Justificado</option>
                </select>
            </div>
            <div>
                <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Desde</label>
                <input type="date" name="start_date" id="f_start"
                       value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>"
                       class="<?= !empty($filters['start_date']) ? 'on' : '' ?>">
            </div>
            <div>
                <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Hasta</label>
                <input type="date" name="end_date" id="f_end"
                       value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>"
                       class="<?= !empty($filters['end_date']) ? 'on' : '' ?>">
            </div>
        </div>
    </div>

    <!-- Tabla — actualizada por AJAX -->
    <div class="table-section">
        <div class="loading-overlay" id="loader"><div class="spinner"></div></div>
        <div class="table-wrap">
            <div class="table-info">
                <span>📋 <strong id="result-count"><?= count($attendances) ?></strong> registros encontrados</span>
                <span id="filter-tag" style="color:#856404;font-size:0.8rem;display:<?= $hasFilters ? 'inline' : 'none' ?>;">⚠️ Filtros activos</span>
            </div>
            <div id="table-body">
                <?php if (empty($attendances)): ?>
                    <div class="empty-state"><div class="icon">📋</div><p>No hay registros de asistencia.</p></div>
                <?php else: ?>
                <table>
                    <thead><tr>
                        <th>Fecha</th><th>Hora</th><th>Estudiante</th>
                        <th>Asignatura</th><th>Docente</th><th>Estado</th><th>Observación</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($attendances as $a):
                        $sl = $statusLabels[$a['status']] ?? ['label'=>$a['status'],'color'=>'#666','bg'=>'#eee'];
                    ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($a['date'])) ?></td>
                        <td style="white-space:nowrap;"><?= htmlspecialchars($a['hour_period']) ?></td>
                        <td><?= htmlspecialchars($a['student_name']) ?></td>
                        <td><?= htmlspecialchars($a['subject_name']) ?></td>
                        <td style="color:#666;"><?= htmlspecialchars($a['teacher_name']) ?></td>
                        <td><span class="status-badge" style="background:<?= $sl['bg'] ?>;color:<?= $sl['color'] ?>;"><?= $sl['label'] ?></span></td>
                        <td style="color:#888;font-size:0.82rem;"><?= htmlspecialchars($a['observation'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<script>
var SL = {
    'presente':    { label:'Presente',    color:'#28a745', bg:'#d4edda' },
    'ausente':     { label:'Ausente',     color:'#dc3545', bg:'#f8d7da' },
    'tardanza':    { label:'Tardanza',    color:'#ffc107', bg:'#fff3cd' },
    'justificado': { label:'Justificado', color:'#17a2b8', bg:'#d1ecf1' },
};

// Escuchar cambios en todos los filtros
['f_subject','f_student','f_status','f_start','f_end'].forEach(function(id){
    document.getElementById(id).addEventListener('change', runFilter);
});

function getVals() {
    return {
        subject_id:  document.getElementById('f_subject').value,
        student_id:  document.getElementById('f_student').value,
        status:      document.getElementById('f_status').value,
        start_date:  document.getElementById('f_start').value,
        end_date:    document.getElementById('f_end').value,
    };
}

function hasActive(v) {
    return v.subject_id || v.student_id || v.status || v.start_date || v.end_date;
}

function runFilter() {
    var v = getVals();

    // Marcar campos activos visualmente
    ['f_subject','f_student','f_status','f_start','f_end'].forEach(function(id){
        var el = document.getElementById(id);
        el.classList.toggle('on', !!el.value);
    });

    // Mostrar/ocultar banner y tag
    var active = hasActive(v);
    document.getElementById('filter-banner').style.display = active ? 'flex' : 'none';
    document.getElementById('filter-tag').style.display    = active ? 'inline' : 'none';

    // Spinner
    document.getElementById('loader').classList.add('on');

    // Construir URL para el endpoint AJAX
    var p = new URLSearchParams({ action: 'tutor_course_attendance_ajax' });
    if (v.subject_id)  p.set('subject_id',  v.subject_id);
    if (v.student_id)  p.set('student_id',  v.student_id);
    if (v.status)      p.set('status',       v.status);
    if (v.start_date)  p.set('start_date',   v.start_date);
    if (v.end_date)    p.set('end_date',     v.end_date);

    fetch('?' + p.toString(), { credentials: 'same-origin' })
        .then(function(r){ return r.json(); })
        .then(function(data){
            renderStats(data.stats, data.total);
            renderTable(data.attendances);
            document.getElementById('loader').classList.remove('on');
        })
        .catch(function(){
            document.getElementById('loader').classList.remove('on');
        });
}

function renderStats(s, total) {
    var sec = document.getElementById('stats-section');
    if (!s || !total) { sec.innerHTML = ''; return; }
    function pct(v){ return total > 0 ? Math.round(v/total*1000)/10 : 0; }
    function bar(v,c){ return '<div class="progress-bar"><div class="progress-fill" style="width:'+pct(v)+'%;background:'+c+';"></div></div>'; }
    sec.innerHTML =
        '<div class="stat-card total"><div class="number">'+total+'</div><div class="label">Total registros</div></div>' +
        '<div class="stat-card presente"><div class="number">'+s.presente+'</div><div class="label">Presentes ('+pct(s.presente)+'%)</div>'+bar(s.presente,'#28a745')+'</div>' +
        '<div class="stat-card ausente"><div class="number">'+s.ausente+'</div><div class="label">Ausentes ('+pct(s.ausente)+'%)</div>'+bar(s.ausente,'#dc3545')+'</div>' +
        '<div class="stat-card tardanza"><div class="number">'+s.tardanza+'</div><div class="label">Tardanzas ('+pct(s.tardanza)+'%)</div>'+bar(s.tardanza,'#ffc107')+'</div>' +
        '<div class="stat-card justif"><div class="number">'+s.justificado+'</div><div class="label">Justificados ('+pct(s.justificado)+'%)</div>'+bar(s.justificado,'#17a2b8')+'</div>';
}

function renderTable(rows) {
    document.getElementById('result-count').textContent = rows.length;
    if (!rows.length) {
        document.getElementById('table-body').innerHTML =
            '<div class="empty-state"><div class="icon">📋</div><p>No se encontraron registros con los filtros aplicados.</p></div>';
        return;
    }
    var html = '<table><thead><tr><th>Fecha</th><th>Hora</th><th>Estudiante</th><th>Asignatura</th><th>Docente</th><th>Estado</th><th>Observación</th></tr></thead><tbody>';
    rows.forEach(function(a){
        var sl = SL[a.status] || { label:a.status, color:'#666', bg:'#eee' };
        var d  = a.date ? a.date.substr(8,2)+'/'+a.date.substr(5,2)+'/'+a.date.substr(0,4) : '';
        html += '<tr>' +
            '<td>'+d+'</td>' +
            '<td style="white-space:nowrap;">'+esc(a.hour_period)+'</td>' +
            '<td>'+esc(a.student_name)+'</td>' +
            '<td>'+esc(a.subject_name)+'</td>' +
            '<td style="color:#666;">'+esc(a.teacher_name)+'</td>' +
            '<td><span class="status-badge" style="background:'+sl.bg+';color:'+sl.color+';">'+sl.label+'</span></td>' +
            '<td style="color:#888;font-size:0.82rem;">'+(a.observation ? esc(a.observation) : '—')+'</td>' +
            '</tr>';
    });
    html += '</tbody></table>';
    document.getElementById('table-body').innerHTML = html;
}

function esc(s){
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function clearFilters(){
    ['f_subject','f_student','f_status'].forEach(function(id){ document.getElementById(id).value = ''; });
    document.getElementById('f_start').value = '';
    document.getElementById('f_end').value   = '';
    runFilter();
}
</script>

</body>
</html>