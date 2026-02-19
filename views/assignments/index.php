<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaciones Docentes - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Asignaciones Docentes
</div>

<div class="container-wide">

    <?php if(isset($_GET['success'])): ?><div class="alert alert-success">✓ Asignación creada correctamente</div><?php endif; ?>
    <?php if(isset($_GET['removed'])): ?><div class="alert alert-success">✓ Asignación eliminada</div><?php endif; ?>
    <?php if(isset($_GET['error'])): ?><div class="alert alert-danger">✗ <?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>

    <!-- Header -->
    <div class="page-header blue">
        <div class="ph-icon">📚</div>
        <div>
            <h1>Asignar Docente — Materia</h1>
            <p>Vincula docentes con cursos y asignaturas del año lectivo activo</p>
        </div>
        <div class="ph-actions">
            <a href="?action=tutor_management" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.5);">
                ⭐ Gestionar Tutores
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;">

        <!-- Formulario -->
        <div class="panel">
            <h3 style="margin-bottom:16px;font-size:1rem;">➕ Nueva Asignación</h3>
            <form method="POST" action="?action=create_assignment">
                <div class="form-group">
                    <label>Docente</label>
                    <select name="teacher_id" class="form-control" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['last_name'] . ' ' . $t['first_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Curso</label>
                    <select name="course_id" class="form-control" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($courses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Asignatura</label>
                    <select name="subject_id" class="form-control" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">✓ Asignar</button>
            </form>
        </div>

        <!-- Tabla asignaciones -->
        <div>
            <!-- Filtros -->
            <?php
            $hasFilter = !empty($_GET['fcourse']) || !empty($_GET['fsubject']) || !empty($_GET['fteacher']);
            if($hasFilter): ?>
            <div class="filter-banner">
                <span>⚠️ Filtros activos</span>
                <a href="?action=assignments">✕ Limpiar</a>
            </div>
            <?php endif; ?>

            <div class="filters" style="margin-bottom:12px;">
                <form method="GET" action="">
                    <input type="hidden" name="action" value="assignments">
                    <div class="filter-grid">
                        <div>
                            <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Curso</label>
                            <select name="fcourse" class="<?= !empty($_GET['fcourse']) ? 'on' : '' ?>" onchange="this.form.submit()">
                                <option value="">— Todos —</option>
                                <?php foreach($courses as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($_GET['fcourse'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Asignatura</label>
                            <select name="fsubject" class="<?= !empty($_GET['fsubject']) ? 'on' : '' ?>" onchange="this.form.submit()">
                                <option value="">— Todas —</option>
                                <?php foreach($subjects as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ($_GET['fsubject'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Docente</label>
                            <select name="fteacher" class="<?= !empty($_GET['fteacher']) ? 'on' : '' ?>" onchange="this.form.submit()">
                                <option value="">— Todos —</option>
                                <?php foreach($teachers as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= ($_GET['fteacher'] ?? '') == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['last_name'] . ' ' . $t['first_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-wrap">
                <div class="table-info">
                    <span>📋 <strong><?= count($assignments) ?></strong> asignaciones</span>
                </div>
                <?php if(empty($assignments)): ?>
                <div class="empty-state" style="padding:30px;">
                    <div class="icon">📋</div>
                    <p>No hay asignaciones registradas.</p>
                </div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Asignatura</th>
                            <th>Docente</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($assignments as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['course_name']) ?></td>
                            <td><?= htmlspecialchars($a['subject_name']) ?></td>
                            <td><?= htmlspecialchars($a['teacher_name']) ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openModal('modalDel<?= $a['id'] ?>')">× Eliminar</button>
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

<!-- Modales eliminar -->
<?php foreach($assignments as $a): ?>
<div class="modal-overlay" id="modalDel<?= $a['id'] ?>">
    <div class="modal-box" style="max-width:400px;">
        <h3>🗑️ Eliminar Asignación</h3>
        <p style="margin:12px 0 20px;color:#555;">
            ¿Eliminar la asignación de <strong><?= htmlspecialchars($a['teacher_name']) ?></strong>
            en <strong><?= htmlspecialchars($a['subject_name']) ?></strong>
            — <?= htmlspecialchars($a['course_name']) ?>?
        </p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalDel<?= $a['id'] ?>')">Cancelar</button>
            <form method="POST" action="?action=remove_assignment" style="display:inline;">
                <input type="hidden" name="assignment_id" value="<?= $a['id'] ?>">
                <?php if($hasFilter): ?>
                <input type="hidden" name="fcourse"  value="<?= htmlspecialchars($_GET['fcourse'] ?? '') ?>">
                <input type="hidden" name="fsubject" value="<?= htmlspecialchars($_GET['fsubject'] ?? '') ?>">
                <input type="hidden" name="fteacher" value="<?= htmlspecialchars($_GET['fteacher'] ?? '') ?>">
                <?php endif; ?>
                <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
function openModal(id)  { document.getElementById(id).classList.add('on'); }
function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if(e.target === m) closeModal(m.id); });
});
</script>
</body>
</html>
