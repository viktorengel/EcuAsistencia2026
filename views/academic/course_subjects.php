<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaturas del Curso - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .course-header { background: linear-gradient(135deg, #007bff, #0056b3); color: white; padding: 20px 30px; border-radius: 8px; margin-bottom: 20px; }
        .course-header h2 { font-size: 22px; margin-bottom: 6px; }
        .course-header p { font-size: 14px; opacity: 0.9; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 11px 13px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background: #f8f9fa; font-weight: bold; color: #555; }
        tr:hover td { background: #f0f7ff; }
        .btn { display: inline-block; padding: 5px 12px; border-radius: 4px; border: none; cursor: pointer; font-size: 12px; text-decoration: none; white-space: nowrap; }
        .btn-edit     { background: #ffc107; color: #212529; }
        .btn-delete   { background: #dc3545; color: white; }
        .btn-back     { background: #6c757d; color: white; padding: 8px 18px; font-size: 14px; }
        .btn-add      { background: #28a745; color: white; padding: 10px 20px; font-size: 14px; }
        .btn-assign   { background: #e65100; color: white; }
        .btn-unassign { background: #6c757d; color: white; }
        .btn:hover    { opacity: 0.88; }
        .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error   { background: #f8d7da; color: #721c24; }
        .form-row { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; flex: 1; min-width: 180px; }
        .form-group label { font-size: 13px; font-weight: bold; color: #555; margin-bottom: 5px; }
        .form-group input, .form-group select { padding: 9px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .badge-count { background: #007bff; color: white; border-radius: 12px; padding: 2px 10px; font-size: 13px; margin-left: 8px; }
        .empty-msg { text-align: center; color: #999; padding: 30px; }
        .teacher-ok  { color: #2e7d32; font-weight: bold; font-size: 13px; }
        .teacher-no  { color: #bbb; font-style: italic; font-size: 13px; }
        .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); align-items:center; justify-content:center; z-index:9999; }
        .modal-overlay.active { display:flex; }
        .modal-box { background:white; padding:28px; border-radius:8px; max-width:420px; width:90%; box-shadow:0 4px 20px rgba(0,0,0,.3); }
        .modal-box h3 { margin-bottom:14px; color:#e65100; }
        .modal-box select { width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px; margin-bottom:18px; }
        .modal-btns { display:flex; gap:10px; justify-content:flex-end; }
        .modal-btns button { padding:8px 18px; border:none; border-radius:4px; cursor:pointer; font-size:14px; }
        .modal-box input:focus {
            outline: none;
            border-color: #28a745 !important;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }
        .btn-add {
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-add:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    🏠 Inicio &rsaquo; <a href="?action=academic">Configuración Académica</a> &rsaquo; Asignaturas del Curso
</div>

<div class="container">

    <div class="course-header">
        <h2>📚 <?= htmlspecialchars($course['name']) ?></h2>
        <p>
            <strong>Nivel:</strong> <?= htmlspecialchars($course['grade_level']) ?> &nbsp;|&nbsp;
            <strong>Paralelo:</strong> <?= htmlspecialchars($course['parallel']) ?> &nbsp;|&nbsp;
            <strong>Jornada:</strong> <?= ucfirst($course['shift_name']) ?> &nbsp;|&nbsp;
            <strong>Año Lectivo:</strong> <?= htmlspecialchars($course['year_name']) ?>
        </p>
    </div>

    <!-- Sistema de Toast -->
    <div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

    <!-- Mensajes convertidos a Toast -->
    <?php if (isset($_GET['added'])): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('➕ Asignatura agregada correctamente', 'success'); });</script>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('✓ Horas de asignatura actualizadas', 'success'); });</script>
    <?php endif; ?>
    
    <?php if (isset($_GET['removed'])): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('🗑️ Asignatura quitada del curso', 'warning'); });</script>
    <?php endif; ?>
    
    <?php if (isset($_GET['assigned'])): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('👨‍🏫 Docente asignado correctamente', 'success'); });</script>
    <?php endif; ?>
    
    <?php if (isset($_GET['unassigned'])): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('👤 Docente removido', 'info'); });</script>
    <?php endif; ?>
    
    <?php if (isset($_GET['error']) && $_GET['error'] === 'has_teacher'): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('❌ No se puede quitar: esta asignatura tiene un docente asignado', 'error'); });</script>
    <?php endif; ?>
    
    <?php if (isset($_GET['assign_error'])): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('❌ <?= htmlspecialchars(addslashes($_GET['assign_error'])) ?>', 'error'); });</script>
    <?php endif; ?>
    
    <?php if (isset($_GET['error']) && $_GET['error'] == 'hours_exceeded'): ?>
        <script>document.addEventListener('DOMContentLoaded', function() { showToast('⚠️ <?= htmlspecialchars(addslashes(urldecode($_GET['msg'] ?? 'Error al asignar horas'))) ?>', 'error'); });</script>
    <?php endif; ?>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="color:#333; margin:0;">
                Asignaturas y Docentes
                <span class="badge-count"><?= count($subjects) ?></span>
            </h3>
            <button onclick="abrirModalAgregar()" class="btn btn-add" style="padding:10px 20px; font-size:14px;">
                ➕ Agregar asignatura
            </button>
        </div>

        <?php if (empty($subjects)): ?>
            <div class="empty-msg">📭 Este curso aún no tiene asignaturas asignadas.</div>
        <?php else: ?>
        <div class="table-info" style="padding:10px 0;">
            <div id="total-hours-indicator" style="padding:10px;border-radius:6px;font-weight:bold;"></div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Asignatura</th>
                    <th>Código</th>
                    <th style="text-align:center;">Hrs/semana</th>
                    <th>Docente</th>
                    <th style="width:220px; text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $i => $sub): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= htmlspecialchars($sub['name']) ?></strong></td>
                    <td><?= htmlspecialchars($sub['code']) ?: '<span style="color:#bbb;">—</span>' ?></td>
                    <td style="text-align:center;">
                        <form method="POST" action="?action=set_subject_hours&course_id=<?= $course['id'] ?>" style="display:inline-flex;align-items:center;gap:4px;">
                            <input type="hidden" name="subject_id" value="<?= $sub['id'] ?>">
                            <input type="number" name="hours_per_week" value="<?= (int)($sub['hours_per_week'] ?? 1) ?>"
                                   min="1" max="20" style="width:52px;padding:4px 6px;border:1px solid #ddd;border-radius:4px;font-size:13px;text-align:center;">
                            <button type="submit" style="padding:4px 8px;background:#007bff;color:white;border:none;border-radius:4px;font-size:11px;cursor:pointer;">✓</button>
                        </form>
                    </td>
                    <td>
                        <?php if (!empty($sub['teacher_name'])): ?>
                            <span class="teacher-ok">👤 <?= htmlspecialchars($sub['teacher_name']) ?></span>
                        <?php else: ?>
                            <span class="teacher-no">Sin docente</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center; white-space:nowrap;">
                        <button class="btn btn-assign"
                            onclick="abrirModal(<?= $sub['id'] ?>, '<?= htmlspecialchars(addslashes($sub['name'])) ?>', <?= $sub['assignment_id'] ?? 'null' ?>, <?= $sub['teacher_id'] ?? 'null' ?>)">
                            <?= empty($sub['teacher_name']) ? '👤' : '🔄👤' ?>
                        </button>

                        <?php if (!empty($sub['teacher_name'])): ?>
                        <form method="POST" action="?action=unassign_subject_teacher" style="display:inline;">
                            <input type="hidden" name="assignment_id" value="<?= $sub['assignment_id'] ?>">
                            <input type="hidden" name="course_id"     value="<?= $course['id'] ?>">
                            <button type="submit" class="btn btn-unassign"
                                onclick="return confirm('¿Quitar docente de esta asignatura?')">✖</button>
                        </form>
                        <?php endif; ?>

                        <a href="?action=edit_course_subject&subject_id=<?= $sub['id'] ?>&course_id=<?= $course['id'] ?>"
                           class="btn btn-edit">✏️</a>

                        <form method="POST" action="?action=remove_course_subject" style="display:inline;"
                              onsubmit="return confirmRemove(event, '<?= htmlspecialchars(addslashes($sub['name'])) ?>')">
                            <input type="hidden" name="course_id"  value="<?= $course['id'] ?>">
                            <input type="hidden" name="subject_id" value="<?= $sub['id'] ?>">
                            <button type="submit" class="btn btn-delete">🗑️</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Modal: Agregar Asignatura -->
    <div class="modal-overlay" id="modalAgregarAsignatura">
        <div class="modal-box" style="max-width:500px;">
            <h3 style="color:#28a745; margin-bottom:16px;">➕ Agregar nueva asignatura</h3>
            <form method="POST" action="?action=course_subjects&course_id=<?= $course['id'] ?>" id="formAgregarAsignatura">
                <div style="margin-bottom:20px;">
                    <label style="font-size:13px; font-weight:bold; color:#555; display:block; margin-bottom:6px;">Nombre de la asignatura *</label>
                    <input type="text" name="new_subject_name" id="new_subject_name" class="form-control" 
                        placeholder="Ej: Emprendimiento" required 
                        style="width:100%; padding:10px 12px; border:1.5px solid #ddd; border-radius:6px; font-size:14px;">
                </div>
                
                <div style="margin-bottom:20px;">
                    <label style="font-size:13px; font-weight:bold; color:#555; display:block; margin-bottom:6px;">
                        Código <span style="font-weight:normal; color:#999;">(opcional)</span>
                    </label>
                    <input type="text" name="new_subject_code" id="new_subject_code" class="form-control"
                        placeholder="Ej: EMP" 
                        style="width:100%; padding:10px 12px; border:1.5px solid #ddd; border-radius:6px; font-size:14px;">
                    <small style="color:#888; display:block; margin-top:4px;">Código corto para identificar la asignatura</small>
                </div>
                
                <div class="modal-btns" style="justify-content:flex-end; margin-top:24px;">
                    <button type="button" onclick="cerrarModalAgregar()" style="background:#6c757d; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer; font-size:14px;">Cancelar</button>
                    <button type="submit" style="background:#28a745; color:white; padding:10px 20px; border:none; border-radius:6px; cursor:pointer; font-size:14px; font-weight:600;">✓ Agregar asignatura</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Asignar Docente -->
    <div class="modal-overlay" id="modalAsignar">
        <div class="modal-box">
            <h3>👤 Asignar Docente</h3>
            <p id="modalSubjectLabel" style="font-size:15px; color:#333; margin-bottom:14px;"></p>
            <form method="POST" action="?action=assign_subject_teacher">
                <input type="hidden" name="course_id"     value="<?= $course['id'] ?>">
                <input type="hidden" name="subject_id"    id="modalSubjectId">
                <input type="hidden" name="assignment_id" id="modalAssignmentId">
                <label style="font-size:13px; font-weight:bold; color:#555; display:block; margin-bottom:6px;">Docente *</label>
                <select name="teacher_id" id="modalTeacherSelect" required>
                    <option value="">Seleccionar docente...</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>">
                            <?= htmlspecialchars($t['last_name'] . ' ' . $t['first_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="modal-btns">
                    <button type="button" onclick="cerrarModal()" style="background:#6c757d;color:white;">Cancelar</button>
                    <button type="submit" style="background:#e65100;color:white;">✓ Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <a href="?action=academic" class="btn btn-back">← Volver a Configuración Académica</a>
</div>

<!-- Sistema de Toast Script -->
<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    
    const toast = document.createElement('div');
    
    const styles = {
        success: { bg: '#d4edda', color: '#155724', border: '#c3e6cb', icon: '✅' },
        error: { bg: '#f8d7da', color: '#721c24', border: '#f5c6cb', icon: '❌' },
        warning: { bg: '#fff3cd', color: '#856404', border: '#ffeeba', icon: '⚠️' },
        info: { bg: '#d1ecf1', color: '#0c5460', border: '#bee5eb', icon: 'ℹ️' }
    };
    
    const s = styles[type] || styles.success;
    
    toast.style.cssText = `
        min-width: 300px;
        max-width: 400px;
        background: ${s.bg};
        color: ${s.color};
        border: 1px solid ${s.border};
        border-left: 5px solid ${s.color};
        border-radius: 8px;
        padding: 14px 18px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        pointer-events: all;
        opacity: 0;
        transform: translateX(40px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    `;
    
    toast.innerHTML = `
        <span style="font-size:18px;">${s.icon}</span>
        <span style="flex:1;">${message}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:${s.color};font-size:16px;cursor:pointer;opacity:0.6;padding:0;">✕</button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(40px)';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

function abrirModal(subjectId, subjectName, assignmentId, teacherId) {
    document.getElementById('modalSubjectId').value = subjectId;
    document.getElementById('modalSubjectLabel').textContent = '📖 ' + subjectName;
    document.getElementById('modalAssignmentId').value = assignmentId ?? '';
    const sel = document.getElementById('modalTeacherSelect');
    sel.value = teacherId ?? '';
    document.getElementById('modalAsignar').classList.add('active');
}

function cerrarModal() {
    document.getElementById('modalAsignar').classList.remove('active');
}

document.getElementById('modalAsignar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

function confirmRemove(event, nombre) {
    event.preventDefault();
    const m = document.createElement('div');
    m.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:9999;';
    m.innerHTML = `<div style="background:white;padding:28px;border-radius:8px;max-width:420px;width:90%;box-shadow:0 4px 20px rgba(0,0,0,.3);">
        <h3 style="color:#dc3545;margin-bottom:12px;">⚠️ Quitar asignatura</h3>
        <p style="color:#555;margin-bottom:18px;">¿Quitar <strong>${nombre}</strong> de este curso?</p>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button id="bC" style="padding:8px 18px;background:#6c757d;color:white;border:none;border-radius:4px;cursor:pointer;">Cancelar</button>
            <button id="bO" style="padding:8px 18px;background:#dc3545;color:white;border:none;border-radius:4px;cursor:pointer;">Sí, Quitar</button>
        </div></div>`;
    document.body.appendChild(m);
    const form = event.target;
    document.getElementById('bO').onclick = () => { document.body.removeChild(m); form.submit(); };
    document.getElementById('bC').onclick = () => document.body.removeChild(m);
    return false;
}

document.addEventListener('DOMContentLoaded', function() {
    calcularTotalHoras();
});

function calcularTotalHoras() {
    let total = 0;
    const horasDisponibles = <?= $this->courseModel->getTotalWeeklyHoursAvailable($course['id']) ?>;
    const horasPorDia = <?= $this->courseModel->getMaxHoursPerDay($course['id']) ?>;
    const diasLaborables = <?= $this->courseModel->getWorkingDaysCount() ?>;
    
    document.querySelectorAll('input[name="hours_per_week"]').forEach(function(input) {
        total += parseInt(input.value) || 0;
    });
    
    let indicator = document.getElementById('total-hours-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'total-hours-indicator';
        indicator.style.marginTop = '10px';
        indicator.style.padding = '10px';
        indicator.style.borderRadius = '6px';
        indicator.style.fontWeight = 'bold';
        
        const tableInfo = document.querySelector('.table-info');
        if (tableInfo) {
            tableInfo.appendChild(indicator);
        }
    }
    
    indicator.textContent = `Total horas asignadas: ${total} de ${horasDisponibles} disponibles ` +
                           `(${horasPorDia} horas/día × ${diasLaborables} días)`;
    
    if (total > horasDisponibles) {
        indicator.style.backgroundColor = '#f8d7da';
        indicator.style.color = '#721c24';
        indicator.style.border = '1px solid #f5c6cb';
    } else if (total === horasDisponibles) {
        indicator.style.backgroundColor = '#d4edda';
        indicator.style.color = '#155724';
        indicator.style.border = '1px solid #c3e6cb';
    } else {
        indicator.style.backgroundColor = '#fff3cd';
        indicator.style.color = '#856404';
        indicator.style.border = '1px solid #ffeeba';
    }
}

document.querySelectorAll('input[name="hours_per_week"]').forEach(function(input) {
    input.addEventListener('change', function() {
        const maxHorasDisponibles = <?= $this->courseModel->getTotalWeeklyHoursAvailable($course['id']) ?>;
        let totalActual = 0;
        
        document.querySelectorAll('input[name="hours_per_week"]').forEach(function(inp) {
            if (inp !== input) {
                totalActual += parseInt(inp.value) || 0;
            }
        });
        
        const nuevasHoras = parseInt(this.value) || 0;
        const maxPermitido = maxHorasDisponibles - totalActual;
        
        if (nuevasHoras > maxPermitido) {
            showToast('⚠️ No puedes asignar ' + nuevasHoras + ' horas. Te quedan solo ' + maxPermitido + ' horas disponibles.', 'warning');
            this.value = maxPermitido;
        }
        
        calcularTotalHoras();
    });
});

function abrirModalAgregar() {
    document.getElementById('modalAgregarAsignatura').classList.add('active');
    document.getElementById('new_subject_name').focus();
}

function cerrarModalAgregar() {
    document.getElementById('modalAgregarAsignatura').classList.remove('active');
    document.getElementById('new_subject_name').value = '';
    document.getElementById('new_subject_code').value = '';
}

document.getElementById('modalAgregarAsignatura').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalAgregar();
});

document.getElementById('formAgregarAsignatura').addEventListener('submit', function(e) {
    const nombre = document.getElementById('new_subject_name').value.trim();
    if (nombre === '') {
        e.preventDefault();
        showToast('❌ El nombre de la asignatura es obligatorio', 'error');
    }
});
</script>

</body>
</html>