<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justificar Ausencia - EcuAsist</title>
    <style>
        .reason-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; }
        .reason-option { display: flex; align-items: center; gap: 8px; padding: 10px 14px;
            border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer;
            transition: all .2s; font-size: 14px; background: #fff; }
        .reason-option:hover { border-color: #007bff; background: #f0f7ff; }
        .reason-option input[type=radio] { accent-color: #007bff; width: 16px; height: 16px; flex-shrink: 0; }
        .reason-option.selected { border-color: #007bff; background: #e8f2ff; font-weight: 600; }

        .absence-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer;
            transition: all .2s; margin-bottom: 8px; background: #fff; }
        .absence-item:hover { border-color: #007bff; background: #f0f7ff; }
        .absence-item.checked { border-color: #007bff; background: #e8f2ff; }
        .absence-item input[type=checkbox] { accent-color: #007bff; width: 18px; height: 18px; flex-shrink:0; }
        .absence-date { font-weight: 700; font-size: 15px; color: #333; min-width: 95px; }
        .absence-meta { font-size: 13px; color: #666; flex: 1; }
        .absence-hour { display:inline-block; font-size: 12px; background: #f0f0f0;
            padding: 2px 8px; border-radius: 4px; color: #555; margin: 2px 2px 0 0; }
        .select-all-bar { display: flex; align-items: center; gap: 10px;
            padding: 8px 0; margin-bottom: 10px; border-bottom: 1px solid #eee; }
        #otro-block, #detail-block { display: none; margin-top: 6px; }
        .counter-badge { display: inline-block; background: #007bff; color: #fff;
            border-radius: 12px; padding: 3px 12px; font-size: 13px; font-weight: 700; }
        .approver-note { padding: 10px 14px; border-radius: 6px; font-size: 13px;
            margin-top: 10px; display: none; }
        .step-title { font-size: .95rem; color: #555; margin-bottom: 14px; font-weight: 600; }

        /* Selector de hijo */
        .child-selector { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 4px; }
        .child-card {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 18px; border: 2px solid #e0e0e0; border-radius: 8px;
            cursor: pointer; transition: all .2s; background: #fff; text-decoration: none; color: #333;
            font-size: 14px; font-weight: 500;
        }
        .child-card:hover  { border-color: #007bff; background: #f0f7ff; }
        .child-card.active { border-color: #007bff; background: #e8f2ff; font-weight: 700; }
        .child-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: #1e40af; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; flex-shrink: 0;
        }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<?php
/* ── Definir URL de "volver" según rol ── */
$backUrl = Security::hasRole('representante')
    ? '?action=my_children'
    : '?action=my_attendance';
?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="<?= $backUrl ?>">
        <?= Security::hasRole('representante') ? 'Mis Representados' : 'Mi Asistencia' ?>
    </a> &rsaquo;
    Justificar Ausencia
</div>

<div class="container" style="max-width:720px;">

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?= ['no_absences' => 'Debes seleccionar al menos una ausencia.'][$_GET['error']] ?? 'Error al enviar.' ?>
        </div>
    <?php endif; ?>

    <div class="page-header" style="background:linear-gradient(135deg,#e65100,#ef6c00);">
        <div class="ph-icon">📝</div>
        <div>
            <h1>Justificar Ausencia</h1>
            <p>Selecciona los días que deseas justificar</p>
        </div>
    </div>

    <?php if(Security::hasRole('representante') && empty($myChildren)): ?>
    <!-- Representante sin hijos asignados -->
    <div class="empty-state">
        <div class="icon">👨‍👩‍👧‍👦</div>
        <p>No tienes estudiantes asignados como representado.</p>
        <a href="?action=my_children" class="btn btn-primary" style="margin-top:12px;">← Volver</a>
    </div>

    <?php elseif(Security::hasRole('representante') && !isset($_GET['student_id'])): ?>
    <!-- Representante debe seleccionar hijo primero -->
    <div class="panel" style="margin-bottom:16px;">
        <p class="step-title">👤 Selecciona el estudiante a justificar</p>
        <div class="child-selector">
            <?php foreach($myChildren as $child): ?>
            <a href="?action=submit_justification&student_id=<?= $child['id'] ?>" class="child-card">
                <div class="child-avatar"><?= strtoupper(substr($child['full_name'], 0, 2)) ?></div>
                <?= htmlspecialchars($child['full_name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <a href="?action=my_children" class="btn btn-outline">← Volver</a>

    <?php elseif(empty($absences)): ?>
    <div class="empty-state">
        <div class="icon">🎉</div>
        <?php if(Security::hasRole('representante')): ?>
            <p>Este estudiante no tiene ausencias pendientes de justificar.</p>
            <a href="?action=submit_justification" class="btn btn-outline" style="margin-top:12px;">← Elegir otro estudiante</a>
        <?php else: ?>
            <p>No tienes ausencias pendientes de justificar.</p>
        <?php endif; ?>
        <a href="<?= $backUrl ?>" class="btn btn-primary" style="margin-top:12px;">← Volver</a>
    </div>

    <?php else: ?>

    <?php if(Security::hasRole('representante')): ?>
    <!-- Indicador del hijo seleccionado -->
    <div class="alert alert-info" style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        👤 Justificando ausencias de: <strong>
        <?php
            $selChild = array_filter($myChildren, fn($c) => $c['id'] == $_GET['student_id']);
            echo htmlspecialchars(reset($selChild)['full_name'] ?? 'Estudiante');
        ?>
        </strong>
        &nbsp;·&nbsp; <a href="?action=submit_justification" style="font-size:13px;">Cambiar</a>
    </div>
    <?php endif; ?>

    <div class="alert alert-info">
        📌 Selecciona uno o varios días. <strong>Hasta 3 días</strong> → revisa el <strong>Docente Tutor</strong>. <strong>Más de 3</strong> → revisa <strong>Inspector o Autoridad</strong>.
    </div>

    <form method="POST" enctype="multipart/form-data" id="justForm">

        <?php if(Security::hasRole('representante')): ?>
        <input type="hidden" name="student_id" value="<?= (int)$_GET['student_id'] ?>">
        <?php endif; ?>

        <!-- ── PASO 1: Seleccionar días ── -->
        <div class="panel" style="margin-bottom:16px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <p class="step-title" style="margin:0;">📅 Paso 1 — Días de ausencia a justificar</p>
                <span id="selected-count" class="counter-badge">0 seleccionados</span>
            </div>

            <div class="select-all-bar">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#555;">
                    <input type="checkbox" id="select-all" onchange="toggleAll(this)"
                           style="width:16px;height:16px;accent-color:#007bff;">
                    Seleccionar todos (<?= count($absences) ?> días)
                </label>
            </div>

            <?php
            $byDate = [];
            foreach ($absences as $a) {
                $byDate[$a['date']][] = $a;
            }
            $days = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
            foreach ($byDate as $date => $rows):
                $ids      = implode(',', array_column($rows, 'id'));
                $dayLabel = $days[date('w', strtotime($date))];
                $dateLabel= date('d/m/Y', strtotime($date));
            ?>
            <div class="absence-item" onclick="toggleCheck(this)">
                <input type="checkbox" name="attendance_ids[]" value="<?= $ids ?>"
                       onchange="updateCount(); event.stopPropagation();">
                <div class="absence-date"><?= $dateLabel ?></div>
                <div class="absence-meta">
                    <div style="font-weight:600;color:#444;margin-bottom:4px;"><?= $dayLabel ?></div>
                    <?php foreach($rows as $r): ?>
                        <span class="absence-hour">Hora <?= $r['hour_period'] ?> · <?= htmlspecialchars($r['subject_name']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="approver-note" id="note-tutor" style="background:#e8f5e9;color:#2e7d32;">
                ✅ Con <strong id="lbl-t">0</strong> día(s), revisará el <strong>Docente Tutor</strong>.
            </div>
            <div class="approver-note" id="note-insp" style="background:#fff8e1;color:#f57f17;">
                ⚠️ Con <strong id="lbl-i">0</strong> día(s), revisará el <strong>Inspector o Autoridad</strong>.
            </div>
        </div>

        <!-- ── PASO 2: Motivo ── -->
        <div class="panel" style="margin-bottom:16px;">
            <p class="step-title">📋 Paso 2 — Motivo de la ausencia</p>
            <div class="reason-grid">
                <?php foreach([
                    ['🤒','Enfermedad o malestar'],
                    ['🏥','Cita médica o examen'],
                    ['👨‍👩‍👧','Fallecimiento familiar'],
                    ['⚖️','Trámites legales / judiciales'],
                    ['🚗','Accidente o emergencia'],
                    ['🌧️','Causas climáticas'],
                    ['🏅','Evento deportivo o académico oficial'],
                    ['✈️','Viaje o traslado familiar'],
                    ['📝','Otro motivo'],
                ] as [$icon, $label]):
                    $val = ($label === 'Otro motivo') ? 'Otro' : $label;
                ?>
                <label class="reason-option" onclick="selectReason(this,'<?= addslashes($val) ?>')">
                    <input type="radio" name="reason_type" value="<?= htmlspecialchars($val) ?>" required>
                    <span style="font-size:18px;"><?= $icon ?></span>
                    <?= htmlspecialchars($label) ?>
                </label>
                <?php endforeach; ?>
            </div>
            <div id="otro-block">
                <textarea name="reason" id="reason-text" class="form-control" rows="3"
                          placeholder="Describe el motivo de la ausencia..."></textarea>
            </div>
            <div id="detail-block">
                <input type="text" name="reason" id="reason-detail" class="form-control"
                       placeholder="Detalle adicional (opcional)...">
            </div>
        </div>

        <!-- ── PASO 3: Documento ── -->
        <div class="panel" style="margin-bottom:20px;">
            <p class="step-title">📎 Paso 3 — Documento de respaldo <span style="color:#999;font-weight:400;">(opcional)</span></p>
            <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            <small style="color:#888;display:block;margin-top:4px;">PDF, JPG o PNG — máximo 5MB</small>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-success" id="submitBtn" disabled>📤 Enviar Justificación</button>
            <a href="<?= $backUrl ?>" class="btn btn-outline">Cancelar</a>
        </div>

    </form>
    <?php endif; ?>

</div>

<script>
function toggleCheck(item) {
    var cb = item.querySelector('input[type=checkbox]');
    cb.checked = !cb.checked;
    item.classList.toggle('checked', cb.checked);
    updateCount();
}

function toggleAll(master) {
    document.querySelectorAll('.absence-item input[type=checkbox]').forEach(function(cb) {
        cb.checked = master.checked;
        cb.closest('.absence-item').classList.toggle('checked', master.checked);
    });
    updateCount();
}

function updateCount() {
    var checked = document.querySelectorAll('.absence-item input[type=checkbox]:checked');
    var n = checked.length;
    document.getElementById('selected-count').textContent = n + (n === 1 ? ' seleccionado' : ' seleccionados');

    var noteTutor = document.getElementById('note-tutor');
    var noteInsp  = document.getElementById('note-insp');

    if (n === 0) {
        noteTutor.style.display = 'none';
        noteInsp.style.display  = 'none';
    } else if (n <= 3) {
        document.getElementById('lbl-t').textContent = n;
        noteTutor.style.display = 'block';
        noteInsp.style.display  = 'none';
    } else {
        document.getElementById('lbl-i').textContent = n;
        noteTutor.style.display = 'none';
        noteInsp.style.display  = 'block';
    }

    var all = document.querySelectorAll('.absence-item input[type=checkbox]');
    var masterCb = document.getElementById('select-all');
    masterCb.indeterminate = (n > 0 && n < all.length);
    masterCb.checked       = (n > 0 && n === all.length);

    checkReady();
}

function selectReason(el, val) {
    document.querySelectorAll('.reason-option').forEach(function(r){ r.classList.remove('selected'); });
    el.classList.add('selected');
    el.querySelector('input[type=radio]').checked = true;

    var otroBlock    = document.getElementById('otro-block');
    var detailBlock  = document.getElementById('detail-block');
    var reasonText   = document.getElementById('reason-text');
    var reasonDetail = document.getElementById('reason-detail');

    if (val === 'Otro') {
        otroBlock.style.display   = 'block';
        detailBlock.style.display = 'none';
        reasonText.required = true;
        reasonText.name     = 'reason';
        reasonDetail.name   = '';
    } else {
        otroBlock.style.display   = 'none';
        detailBlock.style.display = 'block';
        reasonText.required = false;
        reasonText.name     = '';
        reasonDetail.name   = 'reason';
    }
    checkReady();
}

function checkReady() {
    var hasAbsence = document.querySelectorAll('.absence-item input:checked').length > 0;
    var hasReason  = document.querySelector('input[name=reason_type]:checked');
    document.getElementById('submitBtn').disabled = !(hasAbsence && hasReason);
}
</script>

</body>
</html>