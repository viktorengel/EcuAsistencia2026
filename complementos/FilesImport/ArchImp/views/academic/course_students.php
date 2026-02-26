<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['name']) ?> — Estudiantes - EcuAsist</title>
    <style>
        .enrolled-table { width:100%; border-collapse:collapse; }
        .enrolled-table th { background:#f8f9fa; padding:10px 12px; font-size:12px; text-align:left; border-bottom:2px solid #e0e0e0; }
        .enrolled-table td { padding:9px 12px; font-size:13px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
        .enrolled-table tbody tr:hover { background:#fafafa; }
        .badge-count { background:#e3f2fd; color:#1565c0; padding:3px 10px; border-radius:10px; font-size:12px; font-weight:700; }
        .btn-retirar { padding:4px 10px; font-size:12px; background:#dc3545; color:#fff; border:none; border-radius:4px; cursor:pointer; }
        .btn-retirar:hover { background:#c82333; }
        .search-box { width:100%; padding:9px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px; margin-bottom:12px; }

        /* ── Modal base ── */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; }
        .modal-overlay.open { display:flex; }
        .modal-box { background:#fff; border-radius:10px; padding:28px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,.2); }
        .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .modal-header h3 { margin:0; font-size:16px; }
        .modal-close { background:none; border:none; font-size:20px; cursor:pointer; color:#999; line-height:1; }
        .modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }

        /* ── Modal matricular ── */
        #modalEnroll .modal-box { max-width:480px; }
        .student-check-item { display:flex; align-items:center; gap:10px; padding:8px 10px; border:1px solid #e8e8e8; border-radius:6px; margin-bottom:5px; cursor:pointer; transition:background .12s; }
        .student-check-item:hover { background:#f0f7ff; border-color:#90caf9; }
        .student-check-item input[type="checkbox"] { width:15px; height:15px; cursor:pointer; flex-shrink:0; }
        .student-check-item label { margin:0; font-weight:normal; font-size:13px; cursor:pointer; flex:1; }
        .student-scroll { max-height:280px; overflow-y:auto; padding-right:2px; }
        .enroll-toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
        .counter-sel { font-size:12px; color:#555; background:#f0f0f0; padding:3px 10px; border-radius:10px; }

        /* ── Modal confirmar retirar ── */
        #modalConfirmUnenroll .modal-box { max-width:420px; }
        #modalConfirmUnenroll h3 { color:#dc3545; }

        /* ── Alertas ── */
        .flash-success { background:#d4edda; color:#155724; padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
        .flash-warning { background:#fff3cd; color:#856404; padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }

        /* ── Empty state ── */
        .empty-state { text-align:center; padding:36px 20px; color:#aaa; }
        .empty-state .es-icon { font-size:42px; margin-bottom:10px; }
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
        <div class="flash-success">✓ <?= (int)$_GET['enrolled'] ?> estudiante(s) matriculado(s) correctamente<?= isset($_GET['errors']) && (int)$_GET['errors'] > 0 ? ' · ⚠ '.(int)$_GET['errors'].' ya estaban en otro curso' : '' ?></div>
    <?php elseif(isset($_GET['errors']) && (int)$_GET['errors'] > 0): ?>
        <div class="flash-warning">⚠ <?= (int)$_GET['errors'] ?> estudiante(s) ya estaban matriculados en otro curso</div>
    <?php endif; ?>
    <?php if(isset($_GET['unenrolled'])): ?>
        <div class="flash-success">✓ Estudiante retirado correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="flash-warning">✗ Error: <?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="page-header" style="background:linear-gradient(135deg,#1565c0,#1976d2);">
        <div class="ph-icon">👥</div>
        <div>
            <h1><?= htmlspecialchars($course['name']) ?></h1>
            <p>Jornada: <?= ucfirst($course['shift_name'] ?? '') ?> &nbsp;|&nbsp; Gestión de matrículas</p>
        </div>
    </div>

    <div class="panel">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <span style="font-size:15px; font-weight:700; color:#333;">
                📋 Estudiantes Matriculados
                <span class="badge-count"><?= count($students) ?></span>
            </span>
            <?php if(count($availableStudents) > 0): ?>
            <button class="btn btn-success" onclick="openEnrollModal()" style="font-size:13px; padding:7px 16px;">
                ➕ Matricular estudiantes
            </button>
            <?php else: ?>
            <span style="font-size:12px; color:#999;">Todos los estudiantes ya están matriculados</span>
            <?php endif; ?>
        </div>

        <?php if(count($students) > 0): ?>
        <div style="margin-bottom:10px;">
            <input type="text" id="searchEnrolled" class="search-box" placeholder="🔍 Buscar estudiante matriculado..." oninput="filterEnrolled()">
        </div>
        <table class="enrolled-table" id="enrolledTable">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Apellidos y Nombres</th>
                    <th>Cédula</th>
                    <th>F. Matrícula</th>
                    <th style="text-align:center; width:90px">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php $n = 1; foreach($students as $s): ?>
                <tr data-name="<?= strtolower($s['last_name'].' '.$s['first_name']) ?>">
                    <td><?= $n++ ?></td>
                    <td><strong><?= htmlspecialchars($s['last_name'].' '.$s['first_name']) ?></strong></td>
                    <td><?= $s['dni'] ?? '-' ?></td>
                    <td><?= !empty($s['enrollment_date']) ? date('d/m/Y', strtotime($s['enrollment_date'])) : '-' ?></td>
                    <td style="text-align:center;">
                        <button class="btn-retirar"
                            onclick="confirmUnenroll(<?= $s['id'] ?>, '<?= addslashes($s['last_name'].' '.$s['first_name']) ?>')">
                            ✕ Retirar
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <div class="es-icon">📭</div>
            <p>Ningún estudiante matriculado en este curso aún.</p>
            <?php if(count($availableStudents) > 0): ?>
            <button class="btn btn-success" onclick="openEnrollModal()" style="margin-top:14px; font-size:13px;">
                ➕ Matricular estudiantes
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div style="margin-top:16px;">
        <a href="?action=academic" class="btn btn-outline">← Volver a Configuración Académica</a>
    </div>
</div>

<!-- ══ MODAL: Matricular ══ -->
<div class="modal-overlay" id="modalEnroll">
    <div class="modal-box">
        <div class="modal-header">
            <h3>➕ Matricular en <?= htmlspecialchars($course['name']) ?></h3>
            <button class="modal-close" onclick="closeEnrollModal()">✕</button>
        </div>

        <input type="text" class="search-box" id="searchAvailable" placeholder="🔍 Buscar estudiante disponible..." oninput="filterAvailable()">

        <div class="enroll-toolbar">
            <button type="button" class="btn btn-outline" style="font-size:12px; padding:5px 12px;" onclick="selectAllAvailable()">
                Seleccionar todos
            </button>
            <span class="counter-sel" id="selectedCounter">0 seleccionados</span>
        </div>

        <form method="POST" action="?action=enroll_students" id="enrollForm">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            <input type="hidden" name="redirect_to" value="view_course_students">

            <div class="student-scroll" id="availableList">
                <?php if(count($availableStudents) > 0): ?>
                    <?php foreach($availableStudents as $s): ?>
                    <div class="student-check-item" data-name="<?= strtolower($s['last_name'].' '.$s['first_name']) ?>">
                        <input type="checkbox" name="student_ids[]" value="<?= $s['id'] ?>" id="av<?= $s['id'] ?>" onchange="updateCounter()">
                        <label for="av<?= $s['id'] ?>">
                            <strong><?= htmlspecialchars($s['last_name'].' '.$s['first_name']) ?></strong>
                            <?php if(!empty($s['dni'])): ?>
                                <span style="color:#999; font-size:11px;"> · <?= $s['dni'] ?></span>
                            <?php endif; ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align:center; color:#999; padding:20px 0; font-size:13px;">No hay estudiantes disponibles para matricular.</p>
                <?php endif; ?>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeEnrollModal()">Cancelar</button>
                <button type="submit" class="btn btn-success" id="btnEnrollSubmit" disabled style="font-size:13px;">
                    ✓ Matricular seleccionados
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══ MODAL: Confirmar Retirar ══ -->
<div class="modal-overlay" id="modalConfirmUnenroll">
    <div class="modal-box">
        <div class="modal-header">
            <h3>⚠️ Retirar Estudiante</h3>
            <button class="modal-close" onclick="closeUnenrollModal()">✕</button>
        </div>
        <p id="unenrollMsg" style="color:#555; font-size:14px; margin-bottom:6px;"></p>
        <p style="color:#888; font-size:12px;">Los registros de asistencia se conservarán.</p>
        <form method="POST" action="?action=unenroll_student" id="unenrollForm">
            <input type="hidden" name="student_id" id="unenrollStudentId">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            <input type="hidden" name="redirect_to" value="view_course_students">
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeUnenrollModal()">Cancelar</button>
                <button type="submit" class="btn btn-danger">Sí, Retirar</button>
            </div>
        </form>
    </div>
</div>

<script>
/* ── Modal Matricular ── */
function openEnrollModal() {
    document.getElementById('modalEnroll').classList.add('open');
    document.getElementById('searchAvailable').focus();
}
function closeEnrollModal() {
    document.getElementById('modalEnroll').classList.remove('open');
}
document.getElementById('modalEnroll').addEventListener('click', function(e) {
    if (e.target === this) closeEnrollModal();
});

function filterAvailable() {
    var q = document.getElementById('searchAvailable').value.toLowerCase();
    document.querySelectorAll('#availableList .student-check-item').forEach(function(el) {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
}

function selectAllAvailable() {
    document.querySelectorAll('#availableList .student-check-item').forEach(function(el) {
        if (el.style.display !== 'none') {
            el.querySelector('input[type="checkbox"]').checked = true;
        }
    });
    updateCounter();
}

function updateCounter() {
    var n = document.querySelectorAll('#availableList input[type="checkbox"]:checked').length;
    document.getElementById('selectedCounter').textContent = n + ' seleccionado' + (n !== 1 ? 's' : '');
    document.getElementById('btnEnrollSubmit').disabled = n === 0;
}

/* ── Modal Retirar ── */
function confirmUnenroll(studentId, name) {
    document.getElementById('unenrollStudentId').value = studentId;
    document.getElementById('unenrollMsg').innerHTML = '¿Retirar a <strong>' + name + '</strong> del curso?';
    document.getElementById('modalConfirmUnenroll').classList.add('open');
}
function closeUnenrollModal() {
    document.getElementById('modalConfirmUnenroll').classList.remove('open');
}
document.getElementById('modalConfirmUnenroll').addEventListener('click', function(e) {
    if (e.target === this) closeUnenrollModal();
});

/* ── Filtro tabla matriculados ── */
function filterEnrolled() {
    var q = document.getElementById('searchEnrolled').value.toLowerCase();
    document.querySelectorAll('#enrolledTable tbody tr').forEach(function(tr) {
        tr.style.display = tr.dataset.name.includes(q) ? '' : 'none';
    });
}

/* ── Auto-abrir modal si venimos de matricular con ?enrolled ── */
<?php if(isset($_GET['enrolled']) && (int)$_GET['enrolled'] === 0 && isset($_GET['errors'])): ?>
    // No hacer nada especial
<?php endif; ?>
</script>

</body>
</html>