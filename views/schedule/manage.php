<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario de <?= htmlspecialchars($course['name']) ?> - EcuAsist</title>
    <style>
        .schedule-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 20px; }
        @media(max-width:900px) { .schedule-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=schedules">Horarios</a> &rsaquo;
    <?= htmlspecialchars($course['name']) ?>
</div>

<div class="container-wide">

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ Clase agregada al horario</div>
    <?php endif; ?>
    <?php if(isset($_GET['deleted'])): ?>
        <div class="alert alert-success">✓ Clase eliminada del horario</div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger">✗ <?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- Header -->
    <div class="page-header purple">
        <div class="ph-icon">📅</div>
        <div>
            <h1>Horario — <?= htmlspecialchars($course['name']) ?></h1>
            <p>Nivel: <?= htmlspecialchars($course['grade_level']) ?> &nbsp;·&nbsp;
               Paralelo: <?= htmlspecialchars($course['parallel']) ?> &nbsp;·&nbsp;
               Jornada: <?= ucfirst($course['shift_name']) ?>
            </p>
        </div>
        <div class="ph-actions">
            <a href="?action=schedules" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.5);">← Volver</a>
        </div>
    </div>

    <div class="schedule-grid">

        <!-- Formulario agregar clase -->
        <div class="panel">
            <h3 style="margin-bottom:16px;font-size:1rem;">➕ Agregar Clase al Horario</h3>
            <form method="POST" id="scheduleForm">
                <div class="form-group">
                    <label>Día de la Semana</label>
                    <select name="day_of_week" id="day_of_week" class="form-control" required>
                        <option value="">Seleccionar...</option>
                        <option value="lunes">Lunes</option>
                        <option value="martes">Martes</option>
                        <option value="miercoles">Miércoles</option>
                        <option value="jueves">Jueves</option>
                        <option value="viernes">Viernes</option>
                        <option value="sabado">Sábado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Número de Hora</label>
                    <select name="period_number" id="period_number" class="form-control" required>
                        <option value="">Seleccionar...</option>
                        <?php
                        $maxHours = strpos($course['grade_level'], 'Técnico') !== false ? 8 : 7;
                        for($i = 1; $i <= $maxHours; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?>ra hora</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Asignatura</label>
                    <select name="subject_id" id="subject_id" class="form-control" required>
                        <option value="">Cargando asignaturas...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Docente Asignado</label>
                    <input type="text" id="teacher_name" class="form-control" readonly
                           placeholder="Se asigna automáticamente" style="background:#f8f9fa;">
                    <input type="hidden" name="teacher_id" id="teacher_id">
                </div>

                <div id="schedule-warning" style="display:none;" class="alert alert-warning"></div>

                <button type="submit" id="submitBtn" class="btn btn-success" disabled
                        style="opacity:0.6;cursor:not-allowed;">
                    ➕ Agregar Clase
                </button>
            </form>
        </div>

        <!-- Tabla horario actual -->
        <div>
            <div class="table-wrap">
                <div class="table-info">
                    <span>📅 Horario Actual — <strong><?= count($schedule) ?></strong> clases</span>
                </div>
                <?php if(empty($schedule)): ?>
                <div class="empty-state" style="padding:30px 20px;">
                    <div class="icon">📅</div>
                    <p>No hay clases en el horario aún.</p>
                </div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Hora</th>
                            <th>Asignatura</th>
                            <th>Docente</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($schedule as $class): ?>
                        <tr>
                            <td><?= ucfirst($class['day_of_week']) ?></td>
                            <td><?= $class['period_number'] ?>ª hora</td>
                            <td><?= htmlspecialchars($class['subject_name']) ?></td>
                            <td style="color:#666;"><?= htmlspecialchars($class['teacher_name']) ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openModal('modalDel<?= $class['id'] ?>')">× Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Modales eliminar clase -->
<?php foreach($schedule as $class): ?>
<div class="modal-overlay" id="modalDel<?= $class['id'] ?>">
    <div class="modal-box" style="max-width:400px;">
        <h3>🗑️ Eliminar Clase</h3>
        <p style="margin:12px 0 20px;color:#555;">
            ¿Eliminar <strong><?= htmlspecialchars($class['subject_name']) ?></strong>
            (<?= ucfirst($class['day_of_week']) ?>, <?= $class['period_number'] ?>ª hora) del horario?
        </p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalDel<?= $class['id'] ?>')">Cancelar</button>
            <form method="POST" action="?action=delete_schedule_class" style="display:inline;">
                <input type="hidden" name="schedule_id" value="<?= $class['id'] ?>">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
const courseId = <?= (int)$course['id'] ?>;

// Cargar asignaturas
fetch('?action=get_course_subjects_schedule&course_id=' + courseId)
    .then(r => r.json())
    .then(data => {
        const sel = document.getElementById('subject_id');
        if (!data.length) {
            sel.innerHTML = '<option value="">Sin asignaturas asignadas</option>';
        } else {
            sel.innerHTML = '<option value="">Seleccionar...</option>';
            data.forEach(item => {
                const o = document.createElement('option');
                o.value = item.subject_id;
                o.textContent = item.subject_name;
                o.dataset.teacherId   = item.teacher_id;
                o.dataset.teacherName = item.teacher_name;
                sel.appendChild(o);
            });
        }
    });

document.getElementById('subject_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const tid  = opt.dataset.teacherId;
    const tname = opt.dataset.teacherName;
    document.getElementById('teacher_id').value   = tid || '';
    document.getElementById('teacher_name').value = tname || '';
    checkReady();
});
document.getElementById('day_of_week').addEventListener('change', () => { checkConflict(); checkReady(); });
document.getElementById('period_number').addEventListener('change', () => { checkConflict(); checkReady(); });

function checkReady() {
    const ok = document.getElementById('day_of_week').value &&
               document.getElementById('period_number').value &&
               document.getElementById('subject_id').value &&
               document.getElementById('teacher_id').value;
    const btn = document.getElementById('submitBtn');
    btn.disabled = !ok;
    btn.style.opacity = ok ? '1' : '0.6';
    btn.style.cursor  = ok ? 'pointer' : 'not-allowed';
}

function checkConflict() {
    const day    = document.getElementById('day_of_week').value;
    const period = document.getElementById('period_number').value;
    if (!day || !period) return;
    fetch('?action=check_schedule_conflict&course_id=' + courseId + '&day=' + day + '&period=' + period)
        .then(r => r.json())
        .then(data => {
            const w = document.getElementById('schedule-warning');
            if (data.exists) {
                w.textContent = '⚠️ Esta hora ya está ocupada: ' + data.subject_name + ' — ' + data.teacher_name + '. Elimínala primero.';
                w.style.display = 'block';
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').style.opacity = '0.6';
                setTimeout(() => { w.style.display = 'none'; }, 4000);
            } else {
                w.style.display = 'none';
            }
        });
}

function openModal(id)  { document.getElementById(id).classList.add('on'); }
function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if(e.target === m) closeModal(m.id); });
});
</script>
</body>
</html>
