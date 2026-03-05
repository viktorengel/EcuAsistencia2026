<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Asistencia - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=attendance_view">Asistencia</a> &rsaquo;
    Registrar Asistencia
</div>

<div class="container">

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ Asistencia registrada correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <?php if($_GET['error'] == 'future'): ?>
            <div class="alert alert-danger">✗ No se puede registrar asistencia de fechas futuras</div>
        <?php elseif($_GET['error'] == 'toolate'): ?>
            <div class="alert alert-danger">✗ Solo se puede registrar asistencia de hasta 48 horas hábiles atrás</div>
        <?php elseif($_GET['error'] == 'unauthorized'): ?>
            <div class="alert alert-danger">⛔ No tienes permiso para registrar asistencia en esa clase</div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Header -->
    <div class="page-header green">
        <div class="ph-icon">📝</div>
        <div>
            <h1>Registrar Asistencia</h1>
            <p>Selecciona tu clase del día y toma asistencia de los estudiantes</p>
        </div>
    </div>

    <!-- Clases del día -->
    <div class="panel">
        <h3>📅 Mis Clases de Hoy</h3>
        <div class="alert alert-warning" style="margin-bottom:12px;">
            ⚠ <strong>Importante:</strong> Puede registrar asistencia de hoy o corregir registros anteriores hasta el
            <strong><?= date('d/m/Y', strtotime($maxEditDate)) ?></strong>.
        </div>

        <?php if(empty($todayClasses)): ?>
            <div class="empty-state">
                <div class="icon">📅</div>
                <p>No tienes clases programadas para hoy según tu horario.</p>
            </div>
        <?php else: ?>
            <div id="classes-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;">
                <?php foreach($todayClasses as $class):
                    $jsonCourse  = json_encode($class['course_name'],  JSON_HEX_TAG | JSON_HEX_APOS);
                    $jsonSubject = json_encode($class['subject_name'],  JSON_HEX_TAG | JSON_HEX_APOS);
                ?>
                <div class="class-card" onclick='selectClass(event, <?= $class["id"] ?>, <?= $jsonCourse ?>, <?= $jsonSubject ?>, <?= $class["period_number"] ?>)'>
                    <div style="font-size:1.5rem;margin-bottom:6px;">📚</div>
                    <div style="font-weight:700;color:#007bff;font-size:0.95rem;margin-bottom:4px;">
                        <?= $class['period_number'] ?>ra hora — <?= htmlspecialchars($class['subject_name']) ?>
                    </div>
                    <div style="font-size:0.83rem;color:#666;">Curso: <?= htmlspecialchars($class['course_name']) ?></div>
                    <div style="margin-top:10px;">
                        <span class="btn btn-primary btn-sm">Tomar Asistencia →</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Selector de fecha -->
    <div class="panel" id="date-selector" style="display:none;">
        <h3>📅 Seleccionar Fecha</h3>
        <form id="config-form">
            <input type="hidden" id="schedule_id" name="schedule_id">
            <div class="form-row">
                <div class="form-group">
                    <label>Clase Seleccionada</label>
                    <input type="text" id="selected_class_info" class="form-control" readonly style="background:#f8f9fa;">
                </div>
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" id="date" name="date" class="form-control"
                        value="<?= date('Y-m-d') ?>"
                        max="<?= date('Y-m-d') ?>"
                        min="<?= $minDate ?>" required>
                    <div class="form-hint">📅 La asistencia de hoy podrá editarse hasta el <?= date('d/m/Y', strtotime($maxEditDate)) ?></div>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="loadStudents()">📋 Cargar Estudiantes</button>
        </form>
    </div>

    <!-- Lista de estudiantes -->
    <form method="POST" id="student-list" style="display:none;">
        <input type="hidden" name="schedule_id" id="schedule_id_hidden">
        <input type="hidden" name="date" id="date_hidden">

        <div class="panel">
            <h3>👥 Lista de Estudiantes</h3>
            <div class="table-wrap" style="margin-top:8px;">
                <table id="students-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Apellidos y Nombres</th>
                            <th>Cédula</th>
                            <th>Estado</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div style="margin-top:16px;">
                <button type="submit" class="btn btn-success btn-lg">💾 Guardar Asistencia</button>
            </div>
        </div>
    </form>

</div>

<style>
.class-card {
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 16px;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.class-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
}
.class-card.selected {
    border-color: #007bff;
    background: #f0f7ff;
}
</style>

<script>
const minDateAllowed = '<?= $minDate ?>';
let selectedScheduleId = null;

function selectClass(event, scheduleId, courseName, subjectName, periodNumber) {
    selectedScheduleId = scheduleId;
    document.querySelectorAll('.class-card').forEach(c => c.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    document.getElementById('date-selector').style.display = 'block';
    document.getElementById('schedule_id').value = scheduleId;
    document.getElementById('selected_class_info').value = periodNumber + 'ra hora - ' + subjectName + ' (' + courseName + ')';
    document.getElementById('student-list').style.display = 'none';
    document.getElementById('date-selector').scrollIntoView({ behavior: 'smooth' });
}

document.getElementById('date') && document.getElementById('date').addEventListener('change', function() {
    const today = new Date().toISOString().split('T')[0];
    if (this.value > today) { alert('No se puede seleccionar una fecha futura'); this.value = today; return; }
    if (this.value < minDateAllowed) { alert('Solo puede registrar asistencia de hasta 48 horas hábiles atrás'); this.value = today; }
});

function loadStudents() {
    const scheduleId = document.getElementById('schedule_id').value;
    const date = document.getElementById('date').value;
    if (!scheduleId || !date) { alert('Complete todos los campos'); return; }

    document.getElementById('schedule_id_hidden').value = scheduleId;
    document.getElementById('date_hidden').value = date;

    fetch('?action=get_schedule_info&schedule_id=' + scheduleId)
        .then(r => r.json())
        .then(scheduleData => {
            const formData = new FormData();
            formData.append('course_id', scheduleData.course_id);
            return fetch('?action=get_students', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(students => {
                    if (!students.length) { alert('No hay estudiantes matriculados en este curso'); return; }
                    return fetch('?action=get_existing_attendance&schedule_id=' + scheduleId + '&date=' + date)
                        .then(r => r.json())
                        .then(existing => {
                            const existingMap = {};
                            existing.forEach(a => { existingMap[a.student_id] = { status: a.status, observation: a.observation || '' }; });
                            const tbody = document.querySelector('#students-table tbody');
                            tbody.innerHTML = '';
                            const statuses = ['presente','ausente','tardanza','justificado'];
                            students.forEach((student, idx) => {
                                const cur = existingMap[student.id] || { status: 'presente', observation: '' };
                                const opts = statuses.map(s => `<option value="${s}" ${cur.status===s?'selected':''}>${s.charAt(0).toUpperCase()+s.slice(1)}</option>`).join('');
                                tbody.innerHTML += `<tr>
                                    <td style="color:#999;">${idx+1}</td>
                                    <td><strong>${student.last_name} ${student.first_name}</strong></td>
                                    <td style="color:#666;">${student.dni||'-'}</td>
                                    <td><select name="status_${student.id}" style="padding:5px 8px;border:1px solid #ccc;border-radius:6px;font-size:0.85rem;">${opts}</select></td>
                                    <td><input type="text" name="obs_${student.id}" value="${cur.observation}" placeholder="Opcional" style="width:100%;padding:5px 8px;border:1px solid #ccc;border-radius:6px;font-size:0.85rem;"></td>
                                </tr>`;
                            });
                            document.getElementById('student-list').style.display = 'block';
                            document.getElementById('student-list').scrollIntoView({ behavior: 'smooth' });
                        });
                });
        })
        .catch(err => console.error('Error:', err));
}
</script>

</body>
</html>