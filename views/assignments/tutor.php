<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Docente Tutor - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=assignments">Asignaciones</a> &rsaquo;
    Tutores
</div>

<div class="container-wide">

    <?php if(isset($_GET['tutor_success'])): ?><div class="alert alert-success">✓ Tutor asignado correctamente</div><?php endif; ?>
    <?php if(isset($_GET['tutor_removed'])): ?><div class="alert alert-success">✓ Tutor eliminado correctamente</div><?php endif; ?>
    <?php if(isset($_GET['tutor_error'])): ?><div class="alert alert-danger">✗ <?= htmlspecialchars($_GET['tutor_error']) ?></div><?php endif; ?>

    <!-- Header -->
    <div class="page-header orange">
        <div class="ph-icon">⭐</div>
        <div>
            <h1>Asignar Docente Tutor</h1>
            <p>Cada curso puede tener un único tutor. El docente debe tener asignatura en el curso.</p>
        </div>
        <div class="ph-actions">
            <a href="?action=assignments" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.5);">← Asignaciones</a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;">

        <!-- Formulario -->
        <div class="panel">
            <h3 style="margin-bottom:16px;font-size:1rem;">⭐ Asignar Tutor</h3>
            <form method="POST" action="?action=set_tutor" id="tutorForm">
                <div class="form-group">
                    <label>Curso</label>
                    <select name="course_id" id="course_tutor" class="form-control" required
                            onchange="loadTeachers(this.value)">
                        <option value="">Seleccionar...</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['id'] ?>">
                                <?= htmlspecialchars($course['name']) ?> — <?= ucfirst($course['shift_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Docente Tutor</label>
                    <select name="teacher_id" id="teacher_tutor" class="form-control" required>
                        <option value="">Primero seleccione un curso...</option>
                    </select>
                </div>

                <div id="tutor-warning" style="display:none;" class="alert alert-warning"></div>

                <button type="submit" class="btn btn-success">⭐ Asignar Tutor</button>
            </form>
        </div>

        <!-- Tabla cursos - tutores -->
        <div class="table-wrap">
            <div class="table-info">
                <span>📋 Tutores por Curso</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Jornada</th>
                        <th>Tutor Asignado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($courses as $course):
                        $courseTutor = null;
                        foreach($assignments as $a) {
                            if ($a['course_name'] == $course['name'] && $a['is_tutor']) {
                                $courseTutor = $a; break;
                            }
                        }
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($course['name']) ?></strong></td>
                        <td><?= ucfirst($course['shift_name']) ?></td>
                        <td>
                            <?php if($courseTutor): ?>
                                <span class="badge badge-green">⭐ <?= htmlspecialchars($courseTutor['teacher_name']) ?></span>
                            <?php else: ?>
                                <span style="color:#ccc;font-size:0.82rem;font-style:italic;">Sin tutor</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($courseTutor): ?>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openModal('modalRem<?= $course['id'] ?>')">× Quitar</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modales quitar tutor -->
<?php foreach($courses as $course):
    $courseTutor = null;
    foreach($assignments as $a) {
        if ($a['course_name'] == $course['name'] && $a['is_tutor']) {
            $courseTutor = $a; break;
        }
    }
    if(!$courseTutor) continue;
?>
<div class="modal-overlay" id="modalRem<?= $course['id'] ?>">
    <div class="modal-box" style="max-width:400px;">
        <h3>⭐ Quitar Tutor</h3>
        <p style="margin:12px 0 20px;color:#555;">
            ¿Quitar a <strong><?= htmlspecialchars($courseTutor['teacher_name']) ?></strong>
            como tutor de <strong><?= htmlspecialchars($course['name']) ?></strong>?
        </p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalRem<?= $course['id'] ?>')">Cancelar</button>
            <form method="POST" action="?action=remove_tutor" style="display:inline;">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <button type="submit" class="btn btn-danger">× Quitar Tutor</button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
function loadTeachers(courseId) {
    const sel = document.getElementById('teacher_tutor');
    const warn = document.getElementById('tutor-warning');
    if (!courseId) {
        sel.innerHTML = '<option value="">Primero seleccione un curso...</option>';
        warn.style.display = 'none';
        return;
    }
    sel.innerHTML = '<option value="">Cargando...</option>';
    fetch('?action=get_course_teachers&course_id=' + courseId)
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                sel.innerHTML = '<option value="">No hay docentes asignados a este curso</option>';
                return;
            }
            sel.innerHTML = '<option value="">Seleccionar...</option>';
            data.forEach(t => {
                const o = document.createElement('option');
                o.value = t.teacher_id;
                o.textContent = t.teacher_name;
                sel.appendChild(o);
            });
        });

    // Verificar si el curso ya tiene tutor
    fetch('?action=check_course_tutor&course_id=' + courseId)
        .then(r => r.json())
        .then(data => {
            if (data.has_tutor) {
                warn.textContent = '⚠️ Este curso ya tiene tutor: ' + data.teacher_name + '. Si continúas, será reemplazado.';
                warn.style.display = 'block';
            } else {
                warn.style.display = 'none';
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
