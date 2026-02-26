<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaciones Docentes - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; font-size: 13px; }
        select { width: 100%; padding: 9px 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 11px 13px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background: #f8f9fa; font-weight: bold; color: #555; }
        tr:hover td { background: #f9f9f9; }
        .btn-danger { background: #dc3545; color: white; padding: 5px 12px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-danger:hover { background: #c82333; }
        .btn-clear { padding: 9px 16px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-size: 13px; display: inline-block; }
        .info-banner { background: #e8f4fd; border-left: 4px solid #007bff; padding: 14px 18px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; color: #0056b3; }
        .alert-success { background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
        .alert-error   { background: #f8d7da; color: #721c24; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
        .empty-msg { text-align: center; color: #999; padding: 30px; }
        .filter-grid { display: grid; grid-template-columns: repeat(3,1fr) auto; gap: 14px; align-items: flex-end; }
        @media(max-width:700px) { .filter-grid { grid-template-columns: 1fr 1fr; } }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Asignaciones Docentes
</div>

<div class="container">

    <div class="page-header" style="background:linear-gradient(135deg,#e65100,#f57c00);">
        <div class="ph-icon">📚</div>
        <div>
            <h1>Asignaciones Docente — Materia</h1>
            <p>Consulta global de todas las asignaciones del año lectivo activo</p>
        </div>
        <div class="ph-actions">
            <a href="?action=academic" style="padding:8px 16px;background:rgba(255,255,255,0.2);color:white;text-decoration:none;border-radius:4px;font-size:13px;">
                🎓 Ir a Cursos
            </a>
            <a href="?action=tutor_management" style="padding:8px 16px;background:rgba(255,255,255,0.2);color:white;text-decoration:none;border-radius:4px;font-size:13px;">
                👨‍🏫 Tutores
            </a>
        </div>
    </div>

    <?php if(isset($_GET['removed'])): ?>
        <div class="alert-success">✓ Asignación eliminada correctamente.</div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="alert-error">✗ <?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="info-banner">
        💡 Para <strong>asignar</strong> un docente a una materia, ve a
        <a href="?action=academic" style="color:#0056b3;font-weight:bold;">Configuración Académica</a>
        → selecciona un curso → botón <strong>📚 Asignaturas</strong> → botón <strong>👤 Asignar</strong>.
    </div>

    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <h2 style="color:#333;font-size:18px;">📋 Todas las asignaciones</h2>
            <span style="font-size:13px;color:#888;"><?= count($assignments) ?> registradas</span>
        </div>

        <form method="GET" action="">
            <input type="hidden" name="action" value="assignments">
            <div class="filter-grid">
                <div>
                    <label>Filtrar por Curso</label>
                    <select name="filter_course" onchange="this.form.submit()">
                        <option value="">Todos los cursos</option>
                        <?php foreach($courses as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (!empty($_GET['filter_course']) && $_GET['filter_course'] == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Filtrar por Asignatura</label>
                    <select name="filter_subject" onchange="this.form.submit()">
                        <option value="">Todas las asignaturas</option>
                        <?php foreach($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= (!empty($_GET['filter_subject']) && $_GET['filter_subject'] == $s['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Filtrar por Docente</label>
                    <select name="filter_teacher" onchange="this.form.submit()">
                        <option value="">Todos los docentes</option>
                        <?php foreach($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= (!empty($_GET['filter_teacher']) && $_GET['filter_teacher'] == $t['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['last_name'].' '.$t['first_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if(!empty($_GET['filter_course']) || !empty($_GET['filter_subject']) || !empty($_GET['filter_teacher'])): ?>
                <div style="padding-top:18px;">
                    <a href="?action=assignments" class="btn-clear">✕ Limpiar</a>
                </div>
                <?php endif; ?>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Asignatura</th>
                    <th>Docente</th>
                    <th style="width:110px;text-align:center;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $list = $assignments;
                if (!empty($_GET['filter_course'])) {
                    $id = (int)$_GET['filter_course']; $nm = '';
                    foreach($courses as $c){ if($c['id']==$id){ $nm=$c['name']; break; } }
                    $list = array_filter($list, fn($a) => $a['course_name']==$nm);
                }
                if (!empty($_GET['filter_subject'])) {
                    $id = (int)$_GET['filter_subject']; $nm = '';
                    foreach($subjects as $s){ if($s['id']==$id){ $nm=$s['name']; break; } }
                    $list = array_filter($list, fn($a) => $a['subject_name']==$nm);
                }
                if (!empty($_GET['filter_teacher'])) {
                    $id = (int)$_GET['filter_teacher']; $nm = '';
                    foreach($teachers as $t){ if($t['id']==$id){ $nm=$t['last_name'].' '.$t['first_name']; break; } }
                    $list = array_filter($list, fn($a) => $a['teacher_name']==$nm);
                }
                ?>
                <?php if(empty($list)): ?>
                    <tr><td colspan="4" class="empty-msg">No hay asignaciones que coincidan con los filtros.</td></tr>
                <?php else: ?>
                    <?php foreach($list as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['course_name']) ?></td>
                        <td><?= htmlspecialchars($a['subject_name']) ?></td>
                        <td>👤 <?= htmlspecialchars($a['teacher_name']) ?></td>
                        <td style="text-align:center;">
                            <form method="POST" action="?action=remove_assignment" style="display:inline;">
                                <input type="hidden" name="assignment_id" value="<?= $a['id'] ?>">
                                <button type="submit" class="btn-danger"
                                    onclick="return confirmDelete(event,'<?= htmlspecialchars(addslashes($a['teacher_name'])) ?>','<?= htmlspecialchars(addslashes($a['course_name'])) ?>','<?= htmlspecialchars(addslashes($a['subject_name'])) ?>')">
                                    🗑️ Quitar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmDelete(event, teacher, course, subject) {
    event.preventDefault();
    const m = document.createElement('div');
    m.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:9999;';
    m.innerHTML = `<div style="background:white;padding:28px;border-radius:8px;max-width:460px;width:90%;box-shadow:0 4px 20px rgba(0,0,0,.3);">
        <h3 style="color:#dc3545;margin-bottom:14px;">⚠️ Quitar asignación</h3>
        <p style="color:#555;margin-bottom:6px;"><strong>Docente:</strong> ${teacher}</p>
        <p style="color:#555;margin-bottom:6px;"><strong>Curso:</strong> ${course}</p>
        <p style="color:#555;margin-bottom:18px;"><strong>Asignatura:</strong> ${subject}</p>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button id="bC" style="padding:8px 18px;background:#6c757d;color:white;border:none;border-radius:4px;cursor:pointer;">Cancelar</button>
            <button id="bO" style="padding:8px 18px;background:#dc3545;color:white;border:none;border-radius:4px;cursor:pointer;">Sí, Quitar</button>
        </div></div>`;
    document.body.appendChild(m);
    const form = event.target.closest('form');
    document.getElementById('bO').onclick = () => { document.body.removeChild(m); form.submit(); };
    document.getElementById('bC').onclick = () => document.body.removeChild(m);
    return false;
}
</script>
</body>
</html>