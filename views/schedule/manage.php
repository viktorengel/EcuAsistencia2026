<?php Security::requireLogin(); ?>
<?php
// ── Working days from institution ──────────────────────────────────
$workingDays = ['lunes','martes','miercoles','jueves','viernes']; // default
if(!empty($_SESSION['institution_id'])) {
    try {
        $db2 = new Database();
        $instRow = $db2->connect()->prepare("SELECT working_days_list FROM institutions WHERE id = :id");
        $instRow->execute([':id' => $_SESSION['institution_id']]);
        $inst = $instRow->fetch();
        if(!empty($inst['working_days_list'])) {
            $parsed = json_decode($inst['working_days_list'], true);
            if(is_array($parsed) && count($parsed)) $workingDays = $parsed;
        }
    } catch (PDOException $e) {
        // Columna working_days_list no existe aun, usar dias por defecto
    }
}

$dayLabels = ['lunes'=>'Lunes','martes'=>'Martes','miercoles'=>'Miércoles',
              'jueves'=>'Jueves','viernes'=>'Viernes','sabado'=>'Sábado'];

$isTecnico = strpos($course['grade_level'],'BT') !== false
          || strpos($course['grade_level'],'Técnico') !== false
          || ($course['education_type'] ?? '') === 'bt';
$maxHours  = $isTecnico ? 8 : 7;
$recreoAfter = 4;

// Index schedule grid
$grid = [];
foreach($schedule as $cls) {
    $grid[$cls['day_of_week']][$cls['period_number']] = $cls;
}

// Count assigned hours per subject
$assignedCount = []; // subject_id -> count
foreach($schedule as $cls) {
    $assignedCount[$cls['subject_id']] = ($assignedCount[$cls['subject_id']] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario — <?= htmlspecialchars($course['name']) ?></title>
    <style>
    :root {
        --ink:      #1a2035;
        --muted:    #6b7a99;
        --bg:       #eef0f7;
        --surface:  #ffffff;
        --border:   #dde1ee;
        --accent:   #4361ee;
        --success:  #06d6a0;
        --danger:   #ef476f;
        --warn:     #ffd166;
        --recreo-bg:#f8f9fc;
        --cell-h:   70px;
        --label-w:  110px;
        --radius:   12px;
        --shadow:   0 2px 16px rgba(67,97,238,.09);
    }
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--ink);}

    .wrap{max-width:1440px;margin:0 auto;padding:20px 16px 60px;}

    .crumb{font-size:12px;color:var(--muted);margin-bottom:14px;}
    .crumb a{color:var(--accent);text-decoration:none;}

    /* Page header */
    .ph{background:linear-gradient(135deg,#1a237e 0%,#4361ee 100%);
        border-radius:var(--radius);padding:18px 24px;margin-bottom:18px;
        display:flex;align-items:center;gap:14px;flex-wrap:wrap;
        box-shadow:0 4px 24px rgba(67,97,238,.25);}
    .ph-ico{font-size:30px;flex-shrink:0;}
    .ph h1{font-size:1.2rem;font-weight:800;color:#fff;line-height:1.2;}
    .ph p{font-size:12px;color:rgba(255,255,255,.75);margin-top:3px;}
    .ph .back{margin-left:auto;padding:7px 16px;background:rgba(255,255,255,.18);
               color:#fff;text-decoration:none;border-radius:7px;font-size:12px;
               border:1px solid rgba(255,255,255,.3);transition:background .15s;white-space:nowrap;}
    .ph .back:hover{background:rgba(255,255,255,.3);}

    /* Toast */
    #toast{position:fixed;top:16px;right:16px;z-index:9999;padding:11px 20px;
           border-radius:9px;font-size:13px;font-weight:600;
           box-shadow:0 4px 20px rgba(0,0,0,.2);
           opacity:0;transform:translateY(-6px);transition:all .22s;pointer-events:none;max-width:300px;}
    #toast.show{opacity:1;transform:translateY(0);}
    #toast.ok {background:#0d3b2e;color:#06d6a0;}
    #toast.err{background:#3b0d18;color:#ef476f;}
    #toast.inf{background:#1a2035;color:#fff;}

    /* Layout - full width, no sidebar */
    .layout { display:flex; flex-direction:column; gap:14px; }

    /* ── Top subjects bar ───────────────────────────────── */
    .sp {
        background:var(--surface); border-radius:var(--radius);
        box-shadow:var(--shadow); padding:14px 16px;
    }
    .sp-title {
        font-size:11px; font-weight:800; color:var(--muted);
        text-transform:uppercase; letter-spacing:.08em; margin-bottom:10px;
    }
    .chips-wrap {
        display:flex; flex-wrap:wrap; gap:8px;
    }
    .chip {
        display:flex; align-items:center; gap:8px; border-radius:8px;
        padding:8px 12px; cursor:grab; border:1.5px solid transparent;
        transition:transform .12s, box-shadow .12s; user-select:none;
        font-size:12px; flex-shrink:0;
    }
    .chip:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,.14); }
    .chip.dragging { opacity:.35; cursor:grabbing; }
    .chip.full { display:none; }
    .chip.selected-mobile { outline:2px solid var(--accent); outline-offset:2px; }
    .chip .dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .chip-name { font-weight:700; line-height:1.2; }
    .chip-tch { font-size:10px; color:var(--muted); font-weight:400; }
    .chip-hrs { display:flex; align-items:center; gap:5px; margin-top:3px; }
    .hrs-bar { height:4px; border-radius:2px; background:rgba(0,0,0,.1); width:60px; overflow:hidden; }
    .hrs-fill { height:100%; border-radius:2px; transition:width .3s; }
    .hrs-txt { font-size:9px; white-space:nowrap; opacity:.7; }
    .hrs-done .hrs-fill { background:#06d6a0; }
    .hrs-partial .hrs-fill { background:#ffd166; }
    .no-sub { color:var(--muted); font-size:12px; padding:4px 0; }
    .sp-hint {
        font-size:11px; color:var(--muted); margin-top:10px;
        padding-top:10px; border-top:1px solid var(--border);
    }

    /* ── Grid card ─────────────────────────────────────── */
    .gc {
        background:var(--surface); border-radius:var(--radius);
        box-shadow:var(--shadow); overflow:hidden;
    }

    /* Scroll wrapper — only day columns scroll, hour column sticky */
    .gs {
        overflow-x:auto;
        -webkit-overflow-scrolling:touch;
        position:relative;
    }

    /* Schedule table */
    .st {
        border-collapse:collapse;
        width:100%;
        table-layout:fixed;
    }

    /* Sticky hour column */
    .st thead th:first-child,
    .st tbody td:first-child {
        position:sticky;
        left:0;
        z-index:2;
    }

    /* Day column fixed width */
    .st colgroup col.day-col { width:160px; }
    .st colgroup col.hr-col  { width:80px; }

    /* Header */
    .st thead th {
        background:#1a237e; color:#fff;
        font-size:12px; font-weight:800;
        text-transform:uppercase; letter-spacing:.06em;
        height:46px; text-align:center; padding:0 10px;
        border-right:1px solid rgba(255,255,255,.15);
        white-space:nowrap;
    }
    .st thead th:first-child {
        background:#0d1545; width:80px; min-width:80px;
        border-right:3px solid rgba(255,255,255,.2);
    }

    /* Period label — sticky */
    .pl {
        background:#f0f2fa;
        border-right:3px solid #c5cae9;
        border-bottom:1px solid #dde1ee;
        height:82px; vertical-align:middle;
        text-align:center; padding:0;
        width:80px; min-width:80px;
    }
    .pl .n {
        font-size:26px; font-weight:900; color:#9fa8da;
        display:block; line-height:1;
    }
    .pl .l {
        font-size:10px; color:var(--muted); margin-top:2px;
        font-weight:600; letter-spacing:.04em;
    }

    /* Recreo */
    .rl {
        background:#fafbfe; border-right:3px solid #c5cae9;
        border-bottom:1px solid #dde1ee;
        height:34px; vertical-align:middle; text-align:center;
        position:sticky; left:0; z-index:2;
    }
    .rl span {
        font-size:10px; font-weight:800; color:#b0bce8;
        text-transform:uppercase; letter-spacing:.1em;
    }
    .rc {
        background:#fafbfe;
        border-right:1px solid #e8eaf6;
        border-bottom:1px solid #dde1ee;
        height:34px;
    }

    /* Schedule cells */
    .sc {
        border-right:1px solid #dde1ee;
        border-bottom:1px solid #dde1ee;
        height:82px; vertical-align:top;
        padding:6px; position:relative;
        transition:background .12s;
        width:160px; min-width:160px;
        cursor:pointer;
    }
    .sc:hover { background:#f0f3fd; }
    .sc.over  { background:#e8effe; outline:2px dashed var(--accent); }
    .sc.occ   { cursor:default; }
    .sc.occ:hover { background:transparent; }

    /* Class card */
    .cc {
        height:100%; border-radius:8px; padding:6px 10px;
        display:flex; flex-direction:column; justify-content:center;
        border-left:4px solid transparent; position:relative; overflow:hidden;
    }
    .cc .s {
        font-size:13px; font-weight:800; line-height:1.25;
        white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }
    .cc .t {
        font-size:11px; margin-top:3px; opacity:.7;
        white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }
    .cc .x {
        position:absolute; top:4px; right:4px;
        width:18px; height:18px; border-radius:50%;
        border:none; cursor:pointer; font-size:11px;
        display:flex; align-items:center; justify-content:center;
        background:rgba(0,0,0,.15); color:#fff;
        opacity:0; transition:opacity .12s;
    }
    .cc:hover .x { opacity:1; }
    .cc .x:hover { background:var(--danger); }

    /* Empty hint */
    .eh {
        height:100%; display:flex; align-items:center; justify-content:center;
        color:#d0d5ea; font-size:20px; border-radius:8px;
        border:2px dashed transparent; transition:all .12s;
    }
    .sc:hover .eh { border-color:#c5cae9; color:#9fa8da; }

    /* Legend */
    .leg {
        display:flex; gap:12px; flex-wrap:wrap;
        padding:10px 16px; border-top:2px solid var(--border);
        font-size:11px; color:var(--muted);
    }
    .leg-i { display:flex; align-items:center; gap:5px; }
    .leg-d  { width:10px; height:10px; border-radius:50%; }

    /* Modals */
    .mo { display:none; position:fixed; inset:0; background:rgba(10,15,40,.55);
          z-index:8000; align-items:center; justify-content:center; }
    .mo.on { display:flex; }
    .mb { background:#fff; border-radius:14px; padding:26px;
          max-width:380px; width:92%; box-shadow:0 10px 50px rgba(0,0,0,.2); }
    .mb h3 { font-size:1rem; font-weight:800; margin-bottom:8px; }
    .mb p  { color:var(--muted); font-size:13px; margin-bottom:18px; line-height:1.55; }
    .ma    { display:flex; gap:8px; justify-content:flex-end; }
    .btn   { padding:8px 18px; border:none; border-radius:7px; font-size:13px;
             font-weight:700; cursor:pointer; transition:opacity .12s; }
    .btn:hover { opacity:.85; }
    .btn-g { background:#f0f2f9; color:var(--ink); }
    .btn-d { background:var(--danger); color:#fff; }
    .btn-p { background:var(--accent); color:#fff; }
    .mb select { width:100%; padding:10px 12px; border:1.5px solid var(--border);
                 border-radius:8px; font-size:13px; margin-bottom:10px; outline:none; }
    .mb select:focus { border-color:var(--accent); }
    </style>
</head>
<body>
<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="wrap">
    <div class="crumb">
        <a href="?action=dashboard">🏠 Inicio</a> ›
        <a href="?action=schedules">Horarios</a> ›
        <?= htmlspecialchars($course['name']) ?>
    </div>

    <div class="ph">
        <span class="ph-ico">📅</span>
        <div>
            <h1>Horario — <?= htmlspecialchars($course['name']) ?></h1>
            <p><?= htmlspecialchars($course['grade_level']) ?> · Paralelo <?= htmlspecialchars($course['parallel']) ?> · <?= ucfirst($course['shift_name']) ?> · <?= $maxHours ?> horas/día</p>
        </div>
        <a href="?action=academic" class="back">← Volver</a>
    </div>

    <div class="layout">

        <!-- ── Top subjects bar ──────────────────────── -->
        <div class="sp">
            <div class="sp-title">📚 Materias del curso — arrastra o toca para asignar</div>
            <div id="chipsList" class="chips-wrap">
                <span class="no-sub">Cargando...</span>
            </div>
            <div class="sp-hint">
                🖥 <strong>Desktop:</strong> arrastra la materia a la celda &nbsp;·&nbsp;
                📱 <strong>Móvil:</strong> toca la materia → toca la hora
            </div>
        </div>

        <!-- ── Grid ──────────────────────────────────── -->
        <div class="gc">
            <div class="gs">
                <table class="st">
                    <colgroup>
                        <col class="hr-col">
                        <?php foreach($workingDays as $day): ?>
                        <col class="day-col">
                        <?php endforeach; ?>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <?php foreach($workingDays as $day): ?>
                            <th><?= $dayLabels[$day] ?? ucfirst($day) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php for($p = 1; $p <= $maxHours; $p++): ?>

                        <?php if($p === $recreoAfter + 1): ?>
                        <tr>
                            <td class="rl"><span>☕ Recreo</span></td>
                            <?php foreach($workingDays as $day): ?>
                            <td class="rc"></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endif; ?>

                        <tr>
                            <td class="pl">
                                <span class="n"><?= $p ?></span>
                                <span class="l"><?= $p ?>ª hora</span>
                            </td>
                            <?php foreach($workingDays as $day):
                                $cls = $grid[$day][$p] ?? null; ?>
                            <td class="sc <?= $cls ? 'occ' : '' ?>"
                                data-day="<?= $day ?>"
                                data-period="<?= $p ?>"
                                <?= $cls ? 'data-sid="'.$cls['id'].'"' : '' ?>
                                ondragover="dov(event)"
                                ondragleave="dlv(event)"
                                ondrop="drp(event)"
                                onclick="cellClick(this)">
                                <?php if($cls): ?>
                                <div class="cc" data-subject-id="<?= $cls['subject_id'] ?>">
                                    <button class="x" onclick="openDel(event,<?= $cls['id'] ?>,'<?= htmlspecialchars(addslashes($cls['subject_name'])) ?>','<?= $dayLabels[$day] ?? ucfirst($day) ?>',<?= $p ?>)">×</button>
                                    <span class="s"><?= htmlspecialchars($cls['subject_name']) ?></span>
                                    <span class="t">👤 <?= htmlspecialchars($cls['teacher_name'] ?? 'Sin docente') ?></span>
                                </div>
                                <?php else: ?>
                                <div class="eh">＋</div>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>

                    <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <div class="leg" id="leg"></div>
        </div>

    </div>
</div>

<!-- Toast -->
<div id="toast"></div>

<!-- Delete modal -->
<div class="mo" id="moDel">
    <div class="mb">
        <h3>🗑️ Eliminar Clase</h3>
        <p id="delTxt"></p>
        <div class="ma">
            <button class="btn btn-g" onclick="closeMo('moDel')">Cancelar</button>
            <form method="POST" action="?action=delete_schedule_class" style="display:inline;" id="delFrm">
                <input type="hidden" name="schedule_id" id="delSid">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <button type="submit" class="btn btn-d">Eliminar</button>
            </form>
        </div>
    </div>
</div>

<!-- Assign modal -->
<div class="mo" id="moAsgn">
    <div class="mb">
        <h3>📚 Asignar Materia</h3>
        <p id="asgnLbl" style="margin-bottom:10px;"></p>
        <select id="asgnSel">
            <option value="">Seleccionar materia...</option>
        </select>
        <div id="asgnTch" style="font-size:12px;color:var(--muted);margin-bottom:14px;min-height:16px;"></div>
        <div class="ma">
            <button class="btn btn-g" onclick="closeAsgn()">Cancelar</button>
            <button class="btn btn-p" id="asgnOk" onclick="confirmAsgn()" disabled>Asignar</button>
        </div>
    </div>
</div>

<!-- Hidden save form -->
<form method="POST" action="?action=manage_schedule&course_id=<?= $course['id'] ?>" id="saveFrm" style="display:none;">
    <input type="hidden" name="subject_id"    id="sf_sub">
    <input type="hidden" name="teacher_id"    id="sf_tch">
    <input type="hidden" name="day_of_week"   id="sf_day">
    <input type="hidden" name="period_number" id="sf_per">
</form>

<script>
const CID      = <?= (int)$course['id'] ?>;
const DLABELS  = <?= json_encode(array_combine($workingDays, array_map(fn($d)=>$dayLabels[$d]??ucfirst($d), $workingDays))) ?>;
const ASSIGNED = <?= json_encode($assignedCount) ?>;   // {subjectId: count}

// Rich subject color palette
const PAL = [
    {bg:'#e8f4fd',bd:'#5b9bd5',tx:'#0d4f8b'},
    {bg:'#fef9e7',bd:'#f4c430',tx:'#7d5a00'},
    {bg:'#eafaf1',bd:'#52be80',tx:'#1a5c36'},
    {bg:'#fdedec',bd:'#e74c3c',tx:'#7b241c'},
    {bg:'#f5eef8',bd:'#a569bd',tx:'#5b2c6f'},
    {bg:'#eaf4fb',bd:'#48c9b0',tx:'#0e6655'},
    {bg:'#fef5e7',bd:'#e59866',tx:'#7e5109'},
    {bg:'#f4ecf7',bd:'#c39bd3',tx:'#6c3483'},
    {bg:'#e8f6f3',bd:'#76d7c4',tx:'#148f77'},
    {bg:'#fdfefe',bd:'#85929e',tx:'#2e4057'},
    {bg:'#f9f3e3',bd:'#d4ac0d',tx:'#7d6608'},
    {bg:'#ebf5fb',bd:'#3498db',tx:'#1a5276'},
];
const cmap = {};
let subjects    = [];
let dragSub     = null;
let selSub      = null;   // mobile selected
let asgnTarget  = null;

fetch('?action=get_course_subjects_schedule&course_id=' + CID)
    .then(r => r.json())
    .then(data => {
        subjects = data;
        
        // 🔥 NUEVO: Calcular total de horas y verificar sobreasignación
        let totalHorasAsignadas = 0;
        subjects.forEach(s => {
            totalHorasAsignadas += parseInt(s.hours_per_week) || 1;
        });
        
        // Obtener horas disponibles del curso (debes definir estas variables)
        const horasDisponibles = <?= $this->courseModel->getTotalWeeklyHoursAvailable($course['id']) ?>;
        const horasPorDia = <?= $this->courseModel->getMaxHoursPerDay($course['id']) ?>;
        const diasLaborables = <?= $this->courseModel->getWorkingDaysCount() ?>;
        
        // 🔥 NUEVO: Mostrar advertencia si hay sobreasignación
        const sp = document.querySelector('.sp');
        
        // Eliminar advertencia anterior si existe
        const oldWarning = document.getElementById('hours-warning');
        if (oldWarning) oldWarning.remove();
        
        if (totalHorasAsignadas > horasDisponibles) {
            const warning = document.createElement('div');
            warning.id = 'hours-warning';
            warning.style.margin = '10px 0 15px 0';
            warning.style.padding = '12px 16px';
            warning.style.background = '#f8d7da';
            warning.style.color = '#721c24';
            warning.style.border = '1px solid #f5c6cb';
            warning.style.borderRadius = '8px';
            warning.style.fontSize = '14px';
            warning.style.fontWeight = '500';
            warning.innerHTML = `
                ⚠️ <strong>Advertencia de sobreasignación:</strong><br>
                Las materias tienen asignadas <strong>${totalHorasAsignadas} horas</strong> semanales,<br>
                pero el horario solo tiene <strong>${horasDisponibles} horas disponibles</strong><br>
                <small style="display:block;margin-top:8px;color:#856404;background:#fff3cd;padding:8px;border-radius:4px;">
                📊 ${horasPorDia} horas/día × ${diasLaborables} días = ${horasDisponibles} horas totales
                </small>
            `;
            
            // Insertar al inicio del panel de materias
            sp.insertBefore(warning, sp.firstChild);
        } else if (totalHorasAsignadas === horasDisponibles) {
            const info = document.createElement('div');
            info.id = 'hours-warning';
            info.style.margin = '10px 0 15px 0';
            info.style.padding = '12px 16px';
            info.style.background = '#d4edda';
            info.style.color = '#155724';
            info.style.border = '1px solid #c3e6cb';
            info.style.borderRadius = '8px';
            info.style.fontSize = '14px';
            info.innerHTML = `
                ✅ <strong>Horario completo:</strong><br>
                Has asignado exactamente <strong>${totalHorasAsignadas} de ${horasDisponibles} horas</strong> disponibles.<br>
                <small style="display:block;margin-top:4px;">${horasPorDia} horas/día × ${diasLaborables} días</small>
            `;
            sp.insertBefore(info, sp.firstChild);
        } else {
            const info = document.createElement('div');
            info.id = 'hours-warning';
            info.style.margin = '10px 0 15px 0';
            info.style.padding = '12px 16px';
            info.style.background = '#fff3cd';
            info.style.color = '#856404';
            info.style.border = '1px solid #ffeeba';
            info.style.borderRadius = '8px';
            info.style.fontSize = '14px';
            info.innerHTML = `
                ℹ️ <strong>Horas disponibles:</strong><br>
                Has asignado <strong>${totalHorasAsignadas} de ${horasDisponibles} horas</strong>.<br>
                Te quedan <strong>${horasDisponibles - totalHorasAsignadas} horas</strong> por distribuir.<br>
                <small style="display:block;margin-top:4px;">${horasPorDia} horas/día × ${diasLaborables} días</small>
            `;
            sp.insertBefore(info, sp.firstChild);
        }

        subjects.forEach((s,i) => cmap[s.subject_id] = i % PAL.length);

        const list = document.getElementById('chipsList');
        list.innerHTML = '';
        if (!subjects.length) {
            list.innerHTML = '<div class="no-sub">Sin materias.<br>Ve a 📚 Asignaturas.</div>';
            return;
        }

        subjects.forEach(s => {
            const sid  = String(s.subject_id);
            const p    = PAL[cmap[sid]];
            const hrs  = parseInt(s.hours_per_week) || 1;
            const done = parseInt(ASSIGNED[sid]) || 0;
            const pct  = Math.min(100, Math.round(done / hrs * 100));
            const full = done >= hrs;
            const barCls = full ? 'hrs-done' : (done > 0 ? 'hrs-partial' : '');

            const chip = document.createElement('div');
            chip.className = 'chip' + (full ? ' full' : '');
            chip.draggable = !full;
            if (full) chip.style.display = 'none';
            chip.dataset.subjectId   = sid;
            chip.dataset.teacherId   = s.teacher_id   || '';
            chip.dataset.teacherName = s.teacher_name || 'Sin docente';
            chip.dataset.hrsPerWeek  = hrs;
            chip.style.background  = p.bg;
            chip.style.borderColor = p.bd;
            chip.style.color       = p.tx;
            chip.innerHTML = `
                <span class="dot" style="background:${p.bd}"></span>
                <div style="flex:1;min-width:0;">
                    <div class="chip-name">${s.subject_name}</div>
                    <div class="chip-tch">${s.teacher_name || 'Sin docente'}</div>
                    <div class="chip-hrs ${barCls}">
                        <div class="hrs-bar"><div class="hrs-fill" style="width:${pct}%;background:${p.bd};"></div></div>
                        <span class="hrs-txt">${done}/${hrs}h</span>
                    </div>
                </div>`;

            chip.addEventListener('dragstart', e => {
                if (chip.classList.contains('full')) { e.preventDefault(); return; }
                dragSub = s;
                chip.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'copy';
            });
            chip.addEventListener('dragend', () => { chip.classList.remove('dragging'); dragSub = null; });

            chip.addEventListener('click', () => {
                if (chip.classList.contains('full')) return;
                // Toggle selection
                const already = selSub && selSub.subject_id == s.subject_id;
                document.querySelectorAll('.chip').forEach(c => c.classList.remove('selected-mobile'));
                if (already) { selSub = null; return; }
                selSub = s;
                chip.classList.add('selected-mobile');
                toast('Toca la hora para asignar: ' + s.subject_name, 'inf');
            });

            list.appendChild(chip);
        });

        colorCells();
        buildLegend();
    });

function colorCells() {
    document.querySelectorAll('.cc').forEach(card => {
        const sid = card.dataset.subjectId;
        if (cmap[sid] !== undefined) {
            const p = PAL[cmap[sid]];
            card.style.background  = p.bg;
            card.style.borderLeftColor = p.bd;
            card.style.color       = p.tx;
        }
    });
}

function buildLegend() {
    const leg = document.getElementById('leg');
    subjects.forEach(s => {
        const p = PAL[cmap[s.subject_id]];
        const el = document.createElement('div');
        el.className = 'leg-i';
        el.innerHTML = `<span class="leg-d" style="background:${p.bd}"></span>${s.subject_name}`;
        leg.appendChild(el);
    });
}

// ── Drag & drop ───────────────────────────────────────────────────
function dov(e) { e.preventDefault(); e.currentTarget.classList.add('over'); }
function dlv(e) { e.currentTarget.classList.remove('over'); }
function drp(e) {
    e.preventDefault();
    const cell = e.currentTarget;
    cell.classList.remove('over');
    if (!dragSub) return;
    if (cell.dataset.sid) { toast('Esta hora ya está ocupada.','err'); return; }
    saveClass(cell, dragSub);
}

// ── Cell click ────────────────────────────────────────────────────
function cellClick(cell) {
    if (cell.dataset.sid) return; // occupied
    // If mobile subject pre-selected, assign directly
    if (selSub) {
        saveClass(cell, selSub);
        document.querySelectorAll('.chip').forEach(c => c.classList.remove('selected-mobile'));
        selSub = null;
        return;
    }
    openAsgn(cell);
}

// ── Assign modal ──────────────────────────────────────────────────
function openAsgn(cell) {
    asgnTarget = cell;
    const day = cell.dataset.day, per = cell.dataset.period;
    document.getElementById('asgnLbl').textContent = (DLABELS[day]||day) + ', ' + per + 'ª hora';
    const sel = document.getElementById('asgnSel');
    sel.innerHTML = '<option value="">Seleccionar materia...</option>';
    // Only show subjects that still have hours available
    subjects.forEach(s => {
        const sid  = String(s.subject_id);
        const hrs  = parseInt(s.hours_per_week) || 1;
        const done = parseInt(ASSIGNED[sid]) || 0;
        if (done >= hrs) return; // skip full subjects
        const o = document.createElement('option');
        o.value = s.subject_id;
        o.textContent = s.subject_name + (s.teacher_name && s.teacher_name !== 'Sin docente' ? ' — ' + s.teacher_name : '');
        o.dataset.teacherId   = s.teacher_id   || '';
        o.dataset.teacherName = s.teacher_name || '';
        sel.appendChild(o);
    });
    if (sel.options.length === 1) {
        toast('Todas las materias ya tienen sus horas completas.', 'inf');
        return;
    }
    document.getElementById('asgnTch').textContent = '';
    document.getElementById('asgnOk').disabled = true;
    document.getElementById('moAsgn').classList.add('on');
}
function closeAsgn() {
    document.getElementById('moAsgn').classList.remove('on');
    asgnTarget = null; selSub = null;
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('selected-mobile'));
}
document.getElementById('asgnSel').addEventListener('change', function(){
    const o = this.options[this.selectedIndex];
    document.getElementById('asgnTch').textContent = o.dataset.teacherName ? '👤 ' + o.dataset.teacherName : '';
    document.getElementById('asgnOk').disabled = !this.value;
});
function confirmAsgn() {
    const sel  = document.getElementById('asgnSel');
    const o    = sel.options[sel.selectedIndex];
    const cell = asgnTarget; // save before closeAsgn nullifies it
    const s    = {
        subject_id:   parseInt(sel.value),
        teacher_id:   o.dataset.teacherId || '',
        subject_name: o.textContent.split(' — ')[0],
        teacher_name: o.dataset.teacherName || ''
    };
    closeAsgn();
    saveClass(cell, s);
}

// ── Save ──────────────────────────────────────────────────────────
function saveClass(cell, s) {
    const sid = String(s.subject_id);
    const subj = subjects.find(x => String(x.subject_id) === sid) || {};
    const hrs = parseInt(subj.hours_per_week) || 1;
    const done = parseInt(ASSIGNED[sid]) || 0;
    
    // Si no tiene docente, igual se puede asignar (pero se mostrará como "Sin docente")
    if (done >= hrs) {
        toast('⚠️ ' + s.subject_name + ' ya tiene todas sus horas asignadas (' + hrs + '/' + hrs + ').', 'err');
        return;
    }
    
    // Actualizar local counter inmediatamente
    ASSIGNED[sid] = done + 1;
    
    // Marcar chip full si alcanzó el límite
    if (ASSIGNED[sid] >= hrs) {
        document.querySelectorAll('.chip').forEach(c => {
            if (String(c.dataset.subjectId) === sid) {
                c.classList.add('full');
                c.draggable = false;
            }
        });
    }
    
    document.getElementById('sf_sub').value = s.subject_id;
    document.getElementById('sf_tch').value = s.teacher_id || '';
    document.getElementById('sf_day').value = cell.dataset.day;
    document.getElementById('sf_per').value = cell.dataset.period;
    document.getElementById('saveFrm').submit();
}

// ── Delete modal ──────────────────────────────────────────────────
function openDel(e, id, subj, day, per) {
    e.stopPropagation();
    document.getElementById('delSid').value  = id;
    document.getElementById('delTxt').innerHTML = `¿Eliminar <strong>${subj}</strong> del ${day}, ${per}ª hora?`;
    document.getElementById('moDel').classList.add('on');
}

// ── Generic modal close ───────────────────────────────────────────
function closeMo(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.mo').forEach(m =>
    m.addEventListener('click', e => { if(e.target===m){ closeMo(m.id); closeAsgn(); } })
);

// ── Toast ─────────────────────────────────────────────────────────
function toast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.className = 'show ' + (type||'');
    clearTimeout(t._t); t._t = setTimeout(() => t.className='', 3400);
}
(function(){
    const p = new URLSearchParams(location.search);
    if(p.get('success')) toast('✓ Clase agregada al horario','ok');
    if(p.get('deleted')) toast('✓ Clase eliminada','ok');
    if(p.get('error'))   toast('✗ ' + p.get('error'),'err');
    if(p.get('success')||p.get('deleted')||p.get('error')) {
        const u = new URL(location.href);
        ['success','deleted','error'].forEach(k=>u.searchParams.delete(k));
        history.replaceState(null,'',u);
    }
})();
</script>
</body>
</html>