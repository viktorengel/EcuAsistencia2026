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
        #otro-block { display: none; margin-top: 4px; }
        .days-badge { display: inline-block; padding: 4px 12px; border-radius: 12px;
            font-weight: 700; font-size: 13px; margin-left: 8px; }
        .approver-note { padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-top: 10px; display: none; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=my_attendance">Mi Asistencia</a> &rsaquo;
    Justificar Ausencia
</div>

<div class="container" style="max-width:720px;">

    <?php if(isset($_GET['error'])): ?>
        <?php $errMap = [
            'no_date'    => 'Debes seleccionar una fecha de inicio.',
            'date_range' => 'La fecha de fin no puede ser anterior a la de inicio.',
        ]; ?>
        <div class="alert alert-danger"><?= $errMap[$_GET['error']] ?? 'Error al enviar' ?></div>
    <?php endif; ?>

    <div class="page-header" style="background:linear-gradient(135deg,#e65100,#ef6c00);">
        <div class="ph-icon">📝</div>
        <div>
            <h1>Justificar Ausencia</h1>
            <p>Completa el formulario para solicitar la justificación</p>
        </div>
    </div>

    <div class="alert alert-info" style="margin-bottom:20px;">
        📌 Puedes justificar uno o varios días consecutivos. Si la ausencia es de <strong>hasta 3 días laborables</strong>,
        la revisará el <strong>Docente Tutor</strong>. Si es de más días, pasará a <strong>Inspector o Autoridad</strong>.
    </div>

    <div class="panel">
        <form method="POST" enctype="multipart/form-data" id="justForm">

            <?php if(Security::hasRole('representante')): ?>
            <div class="form-group">
                <label>Estudiante *</label>
                <select name="student_id" class="form-control" required>
                    <option value="">Seleccionar estudiante...</option>
                    <?php foreach($myChildren ?? [] as $child): ?>
                        <option value="<?= $child['id'] ?>"><?= htmlspecialchars($child['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Rango de fechas -->
            <div class="form-row">
                <div class="form-group">
                    <label>Fecha de inicio de ausencia *</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" required
                           max="<?= date('Y-m-d') ?>"
                           value="<?= $attendanceDate ?? date('Y-m-d') ?>"
                           onchange="calcDays()">
                </div>
                <div class="form-group">
                    <label>Fecha de fin <span style="color:#999;font-weight:400;">(si fue un solo día, dejar igual)</span></label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           max="<?= date('Y-m-d') ?>"
                           value="<?= $attendanceDate ?? date('Y-m-d') ?>"
                           onchange="calcDays()">
                </div>
            </div>

            <!-- Resumen días + quién aprueba -->
            <div id="days-summary" style="margin-bottom:16px;padding:12px 16px;background:#f8f9fa;border-radius:8px;font-size:14px;">
                <span>📅 Días laborables: <strong id="days-count">1</strong></span>
                <span class="days-badge" id="days-badge" style="background:#e3f2fd;color:#1565c0;">1 día</span>
            </div>
            <div class="approver-note" id="approver-note-tutor" style="background:#e8f5e9;color:#2e7d32;">
                ✅ Con <strong id="days-label-t">1</strong> día(s) laborable(s), la justificación será revisada por el <strong>Docente Tutor</strong>.
            </div>
            <div class="approver-note" id="approver-note-inspector" style="background:#fff8e1;color:#f57f17;">
                ⚠️ Con <strong id="days-label-i">4</strong> día(s) laborable(s), la justificación será revisada por <strong>Inspector o Autoridad</strong>.
            </div>

            <!-- Causa de la ausencia -->
            <div class="form-group" style="margin-top:20px;">
                <label>Motivo de la ausencia *</label>
                <div class="reason-grid" id="reasonGrid">
                    <?php
                    $causas = [
                        ['🤒', 'Enfermedad o malestar'],
                        ['🏥', 'Cita médica o examen'],
                        ['👨‍👩‍👧', 'Fallecimiento familiar'],
                        ['⚖️', 'Trámites legales / judiciales'],
                        ['🚗', 'Accidente o emergencia'],
                        ['🌧️', 'Causas climáticas o desastre natural'],
                        ['🏅', 'Evento deportivo o académico oficial'],
                        ['✈️', 'Viaje o traslado familiar'],
                        ['Otro', 'Otro motivo (especificar)'],
                    ];
                    foreach($causas as [$icon, $label]):
                        $val = ($icon === 'Otro') ? 'Otro' : $label;
                    ?>
                    <label class="reason-option" onclick="selectReason(this, '<?= $val ?>')">
                        <input type="radio" name="reason_type" value="<?= htmlspecialchars($val) ?>" required>
                        <?php if($icon !== 'Otro'): ?><span style="font-size:18px;"><?= $icon ?></span><?php endif; ?>
                        <?= htmlspecialchars($label) ?>
                    </label>
                    <?php endforeach; ?>
                </div>

                <!-- Campo "Otro" -->
                <div id="otro-block">
                    <textarea name="reason" id="reason-text" class="form-control"
                              rows="3" placeholder="Describe el motivo de la ausencia..."></textarea>
                </div>
                <!-- Detalle adicional para causas predefinidas -->
                <div id="detail-block" style="display:none;margin-top:8px;">
                    <input type="text" name="reason" id="reason-detail" class="form-control"
                           placeholder="Detalle adicional (opcional)...">
                </div>
            </div>

            <!-- Documento -->
            <div class="form-group">
                <label>Documento de respaldo <span style="color:#999;font-weight:400;">(opcional)</span></label>
                <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <small style="color:#888;display:block;margin-top:4px;">PDF, JPG o PNG — máximo 5MB</small>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-success" id="submitBtn" disabled>📤 Enviar Justificación</button>
                <a href="?action=my_attendance" class="btn btn-outline">Cancelar</a>
            </div>

        </form>
    </div>

</div>

<script>
// Días laborables
function countWorkingDays(from, to) {
    if (!from || !to) return 0;
    var count = 0;
    var cur   = new Date(from + 'T12:00:00');
    var end   = new Date(to   + 'T12:00:00');
    if (cur > end) return 0;
    while (cur <= end) {
        var dow = cur.getDay(); // 0=dom, 6=sab
        if (dow !== 0 && dow !== 6) count++;
        cur.setDate(cur.getDate() + 1);
    }
    return count;
}

function calcDays() {
    var from = document.getElementById('date_from').value;
    var to   = document.getElementById('date_to').value;

    // date_to no puede ser < date_from
    if (to && from && to < from) {
        document.getElementById('date_to').value = from;
        to = from;
    }
    // date_to no puede ser > hoy
    var today = new Date().toISOString().split('T')[0];
    if (to > today) { document.getElementById('date_to').value = today; to = today; }

    var days  = countWorkingDays(from, to);
    document.getElementById('days-count').textContent = days;
    document.getElementById('days-badge').textContent = days + ' día' + (days !== 1 ? 's' : '');

    var tutor = document.getElementById('approver-note-tutor');
    var insp  = document.getElementById('approver-note-inspector');

    if (days > 0) {
        if (days <= 3) {
            document.getElementById('days-label-t').textContent = days;
            tutor.style.display = 'block';
            insp.style.display  = 'none';
            document.getElementById('days-badge').style.background = '#e8f5e9';
            document.getElementById('days-badge').style.color = '#2e7d32';
        } else {
            document.getElementById('days-label-i').textContent = days;
            tutor.style.display = 'none';
            insp.style.display  = 'block';
            document.getElementById('days-badge').style.background = '#fff8e1';
            document.getElementById('days-badge').style.color = '#f57f17';
        }
    } else {
        tutor.style.display = 'none';
        insp.style.display  = 'none';
    }
    checkReady();
}

function selectReason(el, val) {
    // Quitar selected de todos
    document.querySelectorAll('.reason-option').forEach(function(r){ r.classList.remove('selected'); });
    el.classList.add('selected');
    el.querySelector('input[type=radio]').checked = true;

    var otroBlock   = document.getElementById('otro-block');
    var detailBlock = document.getElementById('detail-block');
    var reasonText  = document.getElementById('reason-text');
    var reasonDetail= document.getElementById('reason-detail');

    if (val === 'Otro') {
        otroBlock.style.display   = 'block';
        detailBlock.style.display = 'none';
        reasonText.required = true;
        reasonDetail.name   = '';
        reasonText.name     = 'reason';
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
    var from   = document.getElementById('date_from').value;
    var reason = document.querySelector('input[name=reason_type]:checked');
    document.getElementById('submitBtn').disabled = !(from && reason);
}

// Init
calcDays();
document.querySelectorAll('.reason-option input[type=radio]').forEach(function(r){
    r.addEventListener('change', function(){ checkReady(); });
});
</script>

</body>
</html>