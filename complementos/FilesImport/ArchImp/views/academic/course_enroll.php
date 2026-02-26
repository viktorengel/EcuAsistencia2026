<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes - <?= htmlspecialchars($course['name']) ?> - EcuAsist</title>
    <style>
        .two-col-layout {
            display: grid;
            grid-template-columns: 1fr 1.6fr;
            gap: 20px;
            align-items: start;
        }
        @media (max-width: 900px) {
            .two-col-layout { grid-template-columns: 1fr; }
        }
        .student-check-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border: 1px solid #e8e8e8; border-radius: 6px;
            margin-bottom: 6px; cursor: pointer; transition: background .15s;
        }
        .student-check-item:hover { background: #f0f7ff; border-color: #90caf9; }
        .student-check-item input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }
        .student-check-item label { margin: 0; font-weight: normal; cursor: pointer; font-size: 13px; flex: 1; }
        .enrolled-row td { font-size: 13px; padding: 8px 10px; }
        .badge-enrolled { background: #28a745; color: white; padding: 3px 9px; border-radius: 10px; font-size: 11px; font-weight: 600; }
        .btn-sm { padding: 5px 10px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; }
        .empty-state { text-align: center; padding: 30px; color: #999; }
        .empty-state .icon { font-size: 40px; margin-bottom: 10px; }
        .section-title {
            font-size: 15px; font-weight: 700; color: #333;
            margin-bottom: 14px; padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .counter-badge {
            background: #e3f2fd; color: #1565c0;
            padding: 2px 10px; border-radius: 10px; font-size: 12px; font-weight: 700;
        }
        /* Modal */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; }
        .modal-overlay.open { display:flex; }
        .modal-box { background:#fff; border-radius:10px; padding:28px; max-width:420px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,.2); }
        .modal-box h3 { margin:0 0 12px; color:#dc3545; }
        .modal-box p  { margin:0 0 20px; color:#555; font-size:14px; }
        .modal-actions { display:flex; gap:10px; justify-content:flex-end; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=academic">Configuración Académica</a> &rsaquo;
    <?= htmlspecialchars($course['name']) ?> — Estudiantes
</div>

<div class="container">

    <?php if(isset($_GET['enrolled']) && (int)$_GET['enrolled'] > 0): ?>
        <div class="alert alert-success">✓ <?= (int)$_GET['enrolled'] ?> estudiante(s) matriculado(s) correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['errors']) && (int)$_GET['errors'] > 0): ?>
        <div class="alert alert-warning">⚠ <?= (int)$_GET['errors'] ?> estudiante(s) ya estaban matriculados en otro curso</div>
    <?php endif; ?>
    <?php if(isset($_GET['unenrolled'])): ?>
        <div class="alert alert-success">✓ Estudiante retirado del curso correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['error']) && $_GET['error'] === 'unenroll_failed'): ?>
        <div class="alert alert-warning">✗ Error al retirar el estudiante</div>
    <?php endif; ?>

    <div class="page-header" style="background:linear-gradient(135deg,#1565c0,#1976d2);">
        <div class="ph-icon">👥</div>
        <div>
            <h1><?= htmlspecialchars($course['name']) ?></h1>
            <p>Jornada: <?= ucfirst($course['shift_name']) ?> &nbsp;|&nbsp; Gestión de matrículas</p>
        </div>
    </div>

    <div class="two-col-layout">

        <!-- ══ PANEL IZQUIERDO: Matricular ══ -->
        <div class="panel">
            <div class="section-title">
                ➕ Matricular Estudiantes
                <span class="counter-badge"><?= count($available) ?> disponibles</span>
            </div>

            <?php if(count($available) > 0): ?>
            <form method="POST" action="?action=enroll_students">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">

                <div style="margin-bottom:10px;">
                    <input type="text" id="searchAvailable" placeholder="🔍 Buscar estudiante..."
                           class="form-control" style="font-size:13px;"
                           oninput="filterStudents()">
                </div>

                <div style="max-height:320px; overflow-y:auto; padding-right:4px;" id="studentList">
                    <?php foreach($available as $s): ?>
                    <div class="student-check-item" data-name="<?= strtolower($s['last_name'].' '.$s['first_name']) ?>">
                        <input type="checkbox" name="student_ids[]"
                               value="<?= $s['id'] ?>" id="s<?= $s['id'] ?>">
                        <label for="s<?= $s['id'] ?>">
                            <strong><?= htmlspecialchars($s['last_name'].' '.$s['first_name']) ?></strong>
                            <?php if(!empty($s['dni'])): ?>
                                <span style="color:#999;font-size:11px;"> · <?= $s['dni'] ?></span>
                            <?php endif; ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="display:flex; gap:8px; margin-top:12px;">
                    <button type="button" onclick="selectAll()" class="btn btn-outline" style="font-size:12px;padding:6px 12px;">
                        Seleccionar todos
                    </button>
                    <button type="submit" class="btn btn-success" style="font-size:13px;padding:6px 16px;">
                        ✓ Matricular seleccionados
                    </button>
                </div>
            </form>
            <?php else: ?>
            <div class="empty-state">
                <div class="icon">🎉</div>
                <p>Todos los estudiantes ya están matriculados en algún curso.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- ══ PANEL DERECHO: Matriculados ══ -->
        <div class="panel">
            <div class="section-title">
                📋 Estudiantes Matriculados
                <span class="counter-badge"><?= count($enrolled) ?></span>
            </div>

            <?php if(count($enrolled) > 0): ?>
            <div style="margin-bottom:10px;">
                <input type="text" id="searchEnrolled" placeholder="🔍 Buscar..."
                       class="form-control" style="font-size:13px;"
                       oninput="filterEnrolled()">
            </div>
            <table style="width:100%;border-collapse:collapse;" id="enrolledTable">
                <thead>
                    <tr style="background:#f8f9fa;">
                        <th style="padding:8px 10px;font-size:12px;text-align:left;">#</th>
                        <th style="padding:8px 10px;font-size:12px;text-align:left;">Apellidos y Nombres</th>
                        <th style="padding:8px 10px;font-size:12px;text-align:left;">Cédula</th>
                        <th style="padding:8px 10px;font-size:12px;text-align:center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $n=1; foreach($enrolled as $s): ?>
                    <tr class="enrolled-row" data-name="<?= strtolower($s['last_name'].' '.$s['first_name']) ?>">
                        <td><?= $n++ ?></td>
                        <td><strong><?= htmlspecialchars($s['last_name'].' '.$s['first_name']) ?></strong></td>
                        <td><?= $s['dni'] ?? '-' ?></td>
                        <td style="text-align:center;">
                            <button class="btn-sm" style="background:#dc3545;"
                                    onclick="confirmUnenroll(<?= $s['id'] ?>, '<?= addslashes($s['last_name'].' '.$s['first_name']) ?>')">
                                ✗ Retirar
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <p>Ningún estudiante matriculado en este curso aún.</p>
            </div>
            <?php endif; ?>
        </div>

    </div><!-- /two-col-layout -->

    <div style="margin-top:16px;">
        <a href="?action=academic" class="btn btn-outline">← Volver a Configuración Académica</a>
    </div>
</div>

<!-- Modal Retirar -->
<div class="modal-overlay" id="unenrollModal">
    <div class="modal-box">
        <h3>⚠️ Retirar Estudiante</h3>
        <p id="unenrollMsg"></p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal()">Cancelar</button>
            <form method="POST" action="?action=unenroll_student" id="unenrollForm" style="display:inline;">
                <input type="hidden" name="student_id" id="unenrollStudentId">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <button type="submit" class="btn btn-danger">Sí, Retirar</button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmUnenroll(studentId, name) {
    document.getElementById('unenrollStudentId').value = studentId;
    document.getElementById('unenrollMsg').innerHTML =
        '¿Retirar a <strong>' + name + '</strong> del curso? Los registros de asistencia se conservarán.';
    document.getElementById('unenrollModal').classList.add('open');
}
function closeModal() {
    document.getElementById('unenrollModal').classList.remove('open');
}
document.getElementById('unenrollModal').addEventListener('click', function(e){
    if(e.target === this) closeModal();
});

function selectAll() {
    document.querySelectorAll('#studentList input[type="checkbox"]').forEach(function(cb){
        if(cb.closest('.student-check-item').style.display !== 'none') cb.checked = true;
    });
}

function filterStudents() {
    var q = document.getElementById('searchAvailable').value.toLowerCase();
    document.querySelectorAll('#studentList .student-check-item').forEach(function(el){
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
}

function filterEnrolled() {
    var q = document.getElementById('searchEnrolled').value.toLowerCase();
    document.querySelectorAll('#enrolledTable tbody tr.enrolled-row').forEach(function(el){
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
}
</script>

</body>
</html>