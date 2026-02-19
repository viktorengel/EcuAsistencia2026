<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Académica - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Configuración Académica
</div>

<div class="container-wide">

    <?php
    $msgs = [
        'course_success'    => '✓ Curso creado correctamente',
        'course_updated'    => '✓ Curso actualizado correctamente',
        'course_deleted'    => '✓ Curso eliminado correctamente',
        'subject_success'   => '✓ Asignatura creada correctamente',
        'subject_updated'   => '✓ Asignatura actualizada correctamente',
        'subject_deleted'   => '✓ Asignatura eliminada correctamente',
        'sy_created'        => '✓ Año lectivo creado',
        'sy_updated'        => '✓ Año lectivo actualizado',
        'sy_deleted'        => '✓ Año lectivo eliminado',
        'sy_activated'      => '✓ Año lectivo activado',
        'sy_deactivated'    => '✓ Año lectivo desactivado',
    ];
    foreach($msgs as $key => $msg):
        if(isset($_GET[$key])): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; endforeach;
    $errors = [
        'no_active_year'          => 'No hay un año lectivo activo',
        'course_not_found'        => 'Curso no encontrado',
        'course_has_students'     => 'No se puede eliminar el curso: tiene estudiantes matriculados',
        'course_has_assignments'  => 'No se puede eliminar el curso: tiene asignaciones docentes',
        'subject_not_found'       => 'Asignatura no encontrada',
        'subject_has_assignments' => 'No se puede eliminar la asignatura: tiene asignaciones docentes',
        'active_year_delete'      => 'No se puede eliminar el año lectivo activo',
        'year_has_courses'        => 'No se puede eliminar el año lectivo: tiene cursos asociados',
        'year_not_found'          => 'Año lectivo no encontrado',
    ];
    if(isset($_GET['error']) && isset($errors[$_GET['error']])): ?>
    <div class="alert alert-danger">✗ <?= $errors[$_GET['error']] ?></div>
    <?php endif; ?>

    <!-- Header -->
    <div class="page-header dark">
        <div class="ph-icon">🎓</div>
        <div>
            <h1>Configuración Académica</h1>
            <p>Gestión de años lectivos, cursos y asignaturas</p>
        </div>
    </div>

    <!-- Años Lectivos -->
    <div class="table-wrap" style="margin-bottom:20px;">
        <div class="table-info">
            <span>📅 <strong>Años Lectivos</strong></span>
            <a href="?action=create_school_year" class="btn btn-primary btn-sm">+ Nuevo Año</a>
        </div>
        <table>
            <thead>
                <tr><th>#</th><th>Nombre</th><th>Inicio</th><th>Fin</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php if(empty($schoolYears)): ?>
                <tr><td colspan="6" style="text-align:center;color:#999;">No hay años lectivos</td></tr>
                <?php else: $i = 1; foreach($schoolYears as $year): ?>
                <tr style="<?= $year['is_active'] ? 'background:#f0faf0;' : '' ?>">
                    <td style="color:#999;"><?= $i++ ?></td>
                    <td><strong><?= htmlspecialchars($year['name']) ?></strong></td>
                    <td><?= date('d/m/Y', strtotime($year['start_date'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($year['end_date'])) ?></td>
                    <td>
                        <?php if($year['is_active']): ?>
                            <span class="badge badge-green">✓ ACTIVO</span>
                        <?php else: ?>
                            <span class="badge badge-gray">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="?action=edit_school_year&id=<?= $year['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                        <?php if($year['is_active']): ?>
                            <form method="POST" action="?action=deactivate_school_year" style="display:inline;">
                                <input type="hidden" name="year_id" value="<?= $year['id'] ?>">
                                <button type="submit" class="btn btn-outline btn-sm"
                                        onclick="return confirm('¿Desactivar este año lectivo?')">⊘ Desactivar</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="?action=activate_school_year" style="display:inline;">
                                <input type="hidden" name="year_id" value="<?= $year['id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm"
                                        onclick="return confirm('¿Activar? Se desactivarán los demás.')">✓ Activar</button>
                            </form>
                            <button class="btn btn-danger btn-sm"
                                onclick="openDelYear(<?= $year['id'] ?>, '<?= htmlspecialchars(addslashes($year['name'])) ?>')">🗑️</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Grid formularios -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

        <!-- Crear Curso -->
        <div class="panel">
            <h3 style="margin-bottom:16px;font-size:1rem;">🏫 Crear Curso</h3>
            <form method="POST" action="?action=create_course" id="courseForm">
                <div class="form-group">
                    <label>Nivel Educativo</label>
                    <select name="education_type" id="education_type" class="form-control" required onchange="updateGradeLevels()">
                        <option value="">Seleccionar tipo...</option>
                        <option value="inicial">🧒 Educación Inicial</option>
                        <option value="egb">📘 EGB</option>
                        <option value="bgu">🎓 BGU</option>
                        <option value="bt">🛠 Bachillerato Técnico</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Grado / Año</label>
                    <select name="grade_level" id="grade_level" class="form-control" required onchange="onGradeChange()">
                        <option value="">Seleccione nivel primero...</option>
                    </select>
                </div>
                <div class="form-group" id="group_specialty" style="display:none;">
                    <label>Figura Profesional</label>
                    <select name="specialty" id="specialty" class="form-control" onchange="updateCarreras()">
                        <option value="">Seleccionar...</option>
                        <option>Informática</option><option>Administración</option><option>Contabilidad</option>
                        <option>Electromecánica Automotriz</option><option>Instalaciones Eléctricas</option>
                        <option>Atención Integral en Salud</option><option>Diseño Gráfico</option>
                        <option>Servicios de Belleza</option><option>Producción Agropecuaria</option>
                        <option>Redes y Telecomunicaciones</option><option>Turismo</option>
                        <option>Servicios Hoteleros</option><option>Música</option><option>Artes Plásticas</option>
                    </select>
                </div>
                <div class="form-group" id="group_carrera" style="display:none;">
                    <label>Especialidad <span style="color:#999;font-weight:400;">(opcional)</span></label>
                    <select name="carrera" id="carrera" class="form-control" onchange="generateCourseName()">
                        <option value="">Sin especificar</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Paralelo</label>
                        <select name="parallel" id="parallel" class="form-control" required onchange="generateCourseName()">
                            <option value="">...</option>
                            <?php foreach(range('A', 'J') as $l): ?>
                                <option><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jornada</label>
                        <select name="shift_id" id="shift_id" class="form-control" required onchange="generateCourseName()">
                            <option value="">...</option>
                            <?php foreach($shifts as $s): ?>
                                <option value="<?= $s['id'] ?>" data-shift="<?= $s['name'] ?>"><?= ucfirst($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nombre del Curso <span style="color:#999;font-weight:400;">(generado automáticamente)</span></label>
                    <input type="text" name="name" id="course_name" class="form-control" readonly
                           style="background:#f8f9fa;font-weight:700;">
                </div>
                <button type="submit" class="btn btn-success">🏫 Crear Curso</button>
            </form>
        </div>

        <!-- Crear Asignatura -->
        <div class="panel">
            <h3 style="margin-bottom:16px;font-size:1rem;">📖 Crear Asignatura</h3>
            <form method="POST" action="?action=create_subject">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ej: Matemáticas" required>
                </div>
                <div class="form-group">
                    <label>Código *</label>
                    <input type="text" name="code" class="form-control" placeholder="Ej: MAT" required>
                </div>
                <button type="submit" class="btn btn-success">📖 Crear Asignatura</button>
            </form>
        </div>
    </div>

    <!-- Tabla Cursos -->
    <div class="table-wrap" style="margin-bottom:20px;">
        <div class="table-info">
            <span>🏫 <strong>Cursos Registrados</strong> — <?= count($courses) ?></span>
            <a href="?action=enroll_students" class="btn btn-primary btn-sm">👥 Matricular Estudiantes</a>
        </div>
        <?php if(empty($courses)): ?>
        <div class="empty-state" style="padding:30px;"><div class="icon">🏫</div><p>No hay cursos registrados.</p></div>
        <?php else: ?>
        <table>
            <thead><tr><th>Nombre</th><th>Nivel</th><th>Paralelo</th><th>Jornada</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach($courses as $c): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                    <td><?= htmlspecialchars($c['grade_level']) ?></td>
                    <td><?= htmlspecialchars($c['parallel']) ?></td>
                    <td><?= ucfirst($c['shift_name']) ?></td>
                    <td style="white-space:nowrap;">
                        <a href="?action=view_course_students&course_id=<?= $c['id'] ?>" class="btn btn-info btn-sm">👥</a>
                        <a href="?action=edit_course&id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                        <button class="btn btn-danger btn-sm"
                            onclick="openDelCourse(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['name'])) ?>')">🗑️</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Tabla Asignaturas -->
    <div class="table-wrap">
        <div class="table-info">
            <span>📖 <strong>Asignaturas Registradas</strong> — <?= count($subjects) ?></span>
        </div>
        <?php if(empty($subjects)): ?>
        <div class="empty-state" style="padding:30px;"><div class="icon">📖</div><p>No hay asignaturas registradas.</p></div>
        <?php else: ?>
        <table>
            <thead><tr><th>Código</th><th>Nombre</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach($subjects as $s): ?>
                <tr>
                    <td><span class="badge badge-blue"><?= htmlspecialchars($s['code']) ?></span></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td>
                        <a href="?action=edit_subject&id=<?= $s['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                        <button class="btn btn-danger btn-sm"
                            onclick="openDelSubject(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['name'])) ?>')">🗑️</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div>

<!-- Modal eliminar año -->
<div class="modal-overlay" id="modalDelYear">
    <div class="modal-box" style="max-width:380px;">
        <h3>🗑️ Eliminar Año Lectivo</h3>
        <p style="margin:12px 0 20px;color:#555;">¿Eliminar el año <strong id="delYearName"></strong>?</p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalDelYear')">Cancelar</button>
            <form method="POST" action="?action=delete_school_year" style="display:inline;">
                <input type="hidden" name="year_id" id="delYearId">
                <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal eliminar curso -->
<div class="modal-overlay" id="modalDelCourse">
    <div class="modal-box" style="max-width:380px;">
        <h3>🗑️ Eliminar Curso</h3>
        <p style="margin:12px 0 20px;color:#555;">¿Eliminar el curso <strong id="delCourseName"></strong>?</p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalDelCourse')">Cancelar</button>
            <form method="POST" action="?action=delete_course" style="display:inline;">
                <input type="hidden" name="course_id" id="delCourseId">
                <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal eliminar asignatura -->
<div class="modal-overlay" id="modalDelSubject">
    <div class="modal-box" style="max-width:380px;">
        <h3>🗑️ Eliminar Asignatura</h3>
        <p style="margin:12px 0 20px;color:#555;">¿Eliminar <strong id="delSubjectName"></strong>?</p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalDelSubject')">Cancelar</button>
            <form method="POST" action="?action=delete_subject" style="display:inline;">
                <input type="hidden" name="subject_id" id="delSubjectId">
                <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
            </form>
        </div>
    </div>
</div>

<script>
function openDelYear(id, name) {
    document.getElementById('delYearId').value = id;
    document.getElementById('delYearName').textContent = name;
    document.getElementById('modalDelYear').classList.add('on');
}
function openDelCourse(id, name) {
    document.getElementById('delCourseId').value = id;
    document.getElementById('delCourseName').textContent = name;
    document.getElementById('modalDelCourse').classList.add('on');
}
function openDelSubject(id, name) {
    document.getElementById('delSubjectId').value = id;
    document.getElementById('delSubjectName').textContent = name;
    document.getElementById('modalDelSubject').classList.add('on');
}
function openModal(id)  { document.getElementById(id).classList.add('on'); }
function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if(e.target === m) closeModal(m.id); });
});

/* ---- Lógica formulario curso ---- */
const gradeLevels = {
    inicial: ['Inicial 1 (0-3 años)', 'Inicial 2 (3-5 años)'],
    egb: ['1.º EGB','2.º EGB','3.º EGB','4.º EGB','5.º EGB','6.º EGB','7.º EGB','8.º EGB','9.º EGB','10.º EGB'],
    bgu: ['1.º BGU','2.º BGU','3.º BGU'],
    bt:  ['1.º BT','2.º BT','3.º BT']
};
const egbNocturna = ['8.º EGB','9.º EGB','10.º EGB'];
const carreras = {
    'Informática': ['Aplicaciones Informáticas','Programación de Software','Soporte Técnico'],
    'Administración': ['Asistencia Administrativa','Gestión Empresarial'],
    'Contabilidad': ['Contabilidad','Ventas e Información Comercial'],
    'Atención Integral en Salud': ['Atención en Enfermería','Auxiliar de Salud'],
    'Diseño Gráfico': ['Diseño Gráfico','Multimedia'],
    'Servicios de Belleza': ['Peluquería','Cosmetología'],
    'Redes y Telecomunicaciones': ['Redes','Telecomunicaciones'],
};

function getNocturna() {
    const opts = document.getElementById('shift_id').options;
    for(let i = 0; i < opts.length; i++) {
        if((opts[i].dataset.shift || '').toLowerCase() === 'nocturna') return opts[i];
    }
    return null;
}
function updateNocturna(type, grade) {
    const n = getNocturna(); if(!n) return;
    const ok = type === 'bgu' || type === 'bt' || (type === 'egb' && egbNocturna.includes(grade));
    n.style.display = ok ? '' : 'none';
    if(!ok && n.selected) { document.getElementById('shift_id').value = ''; generateCourseName(); }
}
function updateGradeLevels() {
    const type = document.getElementById('education_type').value;
    const gs = document.getElementById('grade_level');
    gs.innerHTML = '<option value="">Seleccionar grado...</option>';
    (gradeLevels[type] || []).forEach(g => { const o = document.createElement('option'); o.value = o.textContent = g; gs.appendChild(o); });
    const isBT = type === 'bt';
    document.getElementById('group_specialty').style.display = isBT ? 'block' : 'none';
    document.getElementById('group_carrera').style.display = isBT ? 'block' : 'none';
    if(!isBT) { document.getElementById('specialty').value = ''; document.getElementById('carrera').innerHTML = '<option value="">Sin especificar</option>'; }
    updateNocturna(type, ''); generateCourseName();
}
function onGradeChange() {
    updateNocturna(document.getElementById('education_type').value, document.getElementById('grade_level').value);
    generateCourseName();
}
function updateCarreras() {
    const fig = document.getElementById('specialty').value;
    const cs = document.getElementById('carrera');
    cs.innerHTML = '<option value="">Sin especificar</option>';
    (carreras[fig] || []).forEach(c => { const o = document.createElement('option'); o.value = o.textContent = c; cs.appendChild(o); });
    generateCourseName();
}
function generateCourseName() {
    const grade    = document.getElementById('grade_level').value;
    const parallel = document.getElementById('parallel').value;
    const shiftSel = document.getElementById('shift_id');
    const shiftOpt = shiftSel.options[shiftSel.selectedIndex];
    const shiftName = shiftOpt ? (shiftOpt.dataset.shift || '') : '';
    const type     = document.getElementById('education_type').value;
    const specialty = document.getElementById('specialty').value;
    const carrera  = document.getElementById('carrera').value;
    if(!grade || !parallel || !shiftName) { document.getElementById('course_name').value = ''; return; }
    let name = grade + ' "' + parallel + '"';
    if(type === 'bt' && specialty) { name += ' - ' + specialty; if(carrera) name += ' (' + carrera + ')'; }
    name += ' - ' + shiftName.charAt(0).toUpperCase() + shiftName.slice(1);
    document.getElementById('course_name').value = name;
}
</script>
</body>
</html>
