<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Representantes - EcuAsist</title>
    <style>
        .rep-block { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:16px; margin-bottom:12px; }
        .rep-block-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .rep-name { font-size:0.95rem; font-weight:700; }
        .rep-email { font-size:0.8rem; color:#888; }
        .btn-toggle-primary {
            font-size:0.75rem; padding:3px 10px; border-radius:20px; cursor:pointer; border:none;
            transition: background 0.2s;
        }
        .btn-toggle-on  { background:#ffc107; color:#333; }
        .btn-toggle-off { background:#e9ecef; color:#555; }
        .btn-toggle-on:hover  { background:#e0a800; }
        .btn-toggle-off:hover { background:#dee2e6; }
        .actions-cell { display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Gestión de Representantes
</div>

<div class="container">

    <?php if(isset($_GET['success'])): ?><div class="alert alert-success">✓ Relación asignada correctamente</div><?php endif; ?>
    <?php if(isset($_GET['removed'])): ?><div class="alert alert-success">✓ Relación eliminada correctamente</div><?php endif; ?>
    <?php if(isset($_GET['toggled'])): ?><div class="alert alert-success">✓ Tipo de representante actualizado</div><?php endif; ?>
    <?php if(isset($_GET['error'])): ?><div class="alert alert-danger">✗ Error al procesar la solicitud</div><?php endif; ?>
    <?php if(!empty($errorMsg)): ?><div class="alert alert-danger">✗ <?= $errorMsg ?></div><?php endif; ?>

    <!-- Header -->
    <div class="page-header blue">
        <div class="ph-icon">👥</div>
        <div>
            <h1>Gestión de Representantes</h1>
            <p>Vincula representantes con sus estudiantes</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;">

        <!-- Formulario -->
        <div class="panel">
            <h3 style="margin-bottom:16px;font-size:1rem;">➕ Nueva Relación</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Representante *</label>
                    <select name="representative_id" class="form-control" required>
                        <option value="">Seleccionar representante...</option>
                        <?php foreach($representatives as $rep): ?>
                            <option value="<?= $rep['id'] ?>">
                                <?= htmlspecialchars($rep['last_name'] . ' ' . $rep['first_name']) ?>
                                (<?= htmlspecialchars($rep['dni'] ?? 'Sin cédula') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Estudiante *</label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Seleccionar estudiante...</option>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>">
                                <?= htmlspecialchars($s['last_name'] . ' ' . $s['first_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Parentesco *</label>
                    <select name="relationship" class="form-control" required>
                        <option value="">Seleccionar...</option>
                        <option>Padre</option>
                        <option>Madre</option>
                        <option>Tutor Legal</option>
                        <option>Abuelo/a</option>
                        <option>Tío/a</option>
                        <option>Hermano/a</option>
                        <option>Otro</option>
                    </select>
                    <small style="color:#888;font-size:0.77rem;">
                        ⚠️ Solo puede haber un Padre y una Madre por estudiante.
                    </small>
                </div>

                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_primary" value="1"> Representante Principal
                    </label>
                    <small style="color:#888;font-size:0.77rem;">
                        El representante principal recibe las notificaciones prioritarias.
                    </small>
                </div>

                <button type="submit" class="btn btn-success">➕ Asignar Relación</button>
            </form>
        </div>

        <!-- Listado -->
        <div>
            <!-- Filtros -->
            <div class="filters" style="margin-bottom:12px;">
                <h3 style="font-size:0.85rem;font-weight:600;color:#555;margin-bottom:8px;">🔍 Buscar</h3>
                <div class="filter-grid">
                    <div>
                        <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Representante</label>
                        <input type="text" id="filterRep" class="form-control" placeholder="Nombre..." oninput="applyFilters()">
                    </div>
                    <div>
                        <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Estudiante</label>
                        <input type="text" id="filterStu" class="form-control" placeholder="Nombre..." oninput="applyFilters()">
                    </div>
                    <div>
                        <label style="font-size:0.78rem;color:#666;display:block;margin-bottom:4px;">Curso</label>
                        <input type="text" id="filterCourse" class="form-control" placeholder="Curso..." oninput="applyFilters()">
                    </div>
                </div>
                <button class="btn btn-outline btn-sm" style="margin-top:8px;" onclick="clearFilters()">🗑️ Limpiar</button>
            </div>

            <!-- Relaciones -->
            <div id="rep-list">
                <?php
                $hasRelations = false;
                foreach($representatives as $rep):
                    $children = $this->representativeModel->getStudentsByRepresentative($rep['id']);
                    if(!count($children)) continue;
                    $hasRelations = true;
                ?>
                <div class="rep-block"
                     data-repname="<?= strtolower($rep['last_name'] . ' ' . $rep['first_name']) ?>">
                    <div class="rep-block-header">
                        <div>
                            <div class="rep-name">👤 <?= htmlspecialchars($rep['last_name'] . ' ' . $rep['first_name']) ?></div>
                            <div class="rep-email"><?= htmlspecialchars($rep['email']) ?></div>
                        </div>
                        <span class="badge badge-blue"><?= count($children) ?> representado(s)</span>
                    </div>
                    <div class="table-wrap" style="margin-bottom:0;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Parentesco</th>
                                    <th>Curso</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($children as $child): ?>
                                <tr class="student-row"
                                    data-student="<?= strtolower($child['last_name'] . ' ' . $child['first_name']) ?>"
                                    data-course="<?= strtolower($child['course_name'] ?? '') ?>">
                                    <td><strong><?= htmlspecialchars($child['last_name'] . ' ' . $child['first_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($child['relationship']) ?></td>
                                    <td>
                                        <?php if($child['course_name']): ?>
                                            <span class="badge badge-teal"><?= htmlspecialchars($child['course_name']) ?></span>
                                        <?php else: ?>
                                            <span style="color:#ccc;">Sin curso</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($child['is_primary']): ?>
                                            <span class="badge badge-yellow">⭐ Principal</span>
                                        <?php else: ?>
                                            <span class="badge badge-gray">Secundario</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions-cell">
                                            <!-- Toggle Principal / Secundario -->
                                            <button class="btn-toggle-primary <?= $child['is_primary'] ? 'btn-toggle-on' : 'btn-toggle-off' ?>"
                                                onclick="confirmToggle(<?= $rep['id'] ?>, <?= $child['id'] ?>,
                                                    '<?= addslashes(htmlspecialchars($rep['last_name'] . ' ' . $rep['first_name'])) ?>',
                                                    '<?= addslashes(htmlspecialchars($child['last_name'] . ' ' . $child['first_name'])) ?>',
                                                    <?= $child['is_primary'] ? 1 : 0 ?>)"
                                                title="<?= $child['is_primary'] ? 'Cambiar a Secundario' : 'Cambiar a Principal' ?>">
                                                <?= $child['is_primary'] ? '☆ Secundario' : '⭐ Principal' ?>
                                            </button>
                                            <!-- Editar -->
                                            <button class="btn btn-warning btn-sm"
                                                onclick="openEdit(<?= $rep['id'] ?>, <?= $child['id'] ?>,
                                                    '<?= addslashes(htmlspecialchars($rep['last_name'] . ' ' . $rep['first_name'])) ?>',
                                                    '<?= addslashes(htmlspecialchars($child['last_name'] . ' ' . $child['first_name'])) ?>',
                                                    '<?= addslashes(htmlspecialchars($child['relationship'])) ?>',
                                                    <?= $child['is_primary'] ? 1 : 0 ?>)"
                                                title="Editar relación">✏️</button>
                                            <!-- Eliminar -->
                                            <button class="btn btn-danger btn-sm"
                                                onclick="confirmRemove(<?= $rep['id'] ?>, <?= $child['id'] ?>,
                                                    '<?= addslashes(htmlspecialchars($rep['last_name'] . ' ' . $rep['first_name'])) ?>',
                                                    '<?= addslashes(htmlspecialchars($child['last_name'] . ' ' . $child['first_name'])) ?>')"
                                                title="Eliminar relación">✕</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if(!$hasRelations): ?>
                <div class="empty-state">
                    <div class="icon">📂</div>
                    <p>No hay relaciones representante-estudiante registradas.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Modal confirmar eliminar -->
<div class="modal-overlay" id="modalRemove">
    <div class="modal-box" style="max-width:460px;">
        <h3>⚠️ Eliminar Relación</h3>
        <div id="modalRemoveBody" style="margin:12px 0;color:#555;font-size:0.88rem;"></div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalRemove')">Cancelar</button>
            <a id="confirmRemoveLink" href="#" class="btn btn-danger">✗ Sí, Eliminar</a>
        </div>
    </div>
</div>

<!-- Modal editar relación -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box" style="max-width:460px;">
        <h3>✏️ Editar Relación</h3>
        <form method="POST" id="formEdit">
            <input type="hidden" name="representative_id" id="editRepId">
            <input type="hidden" name="student_id" id="editStuId">
            <div style="margin:12px 0;padding:10px;background:#f8f9fa;border-radius:6px;font-size:0.88rem;">
                <strong>Representante:</strong> <span id="editRepName"></span><br>
                <strong>Estudiante:</strong> <span id="editStuName"></span>
            </div>
            <div class="form-group">
                <label>Parentesco *</label>
                <select name="relationship" id="editRelationship" class="form-control" required>
                    <option>Padre</option>
                    <option>Madre</option>
                    <option>Tutor Legal</option>
                    <option>Abuelo/a</option>
                    <option>Tío/a</option>
                    <option>Hermano/a</option>
                    <option>Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_primary" id="editIsPrimary" value="1"> Representante Principal
                </label>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Cancelar</button>
                <button type="submit" class="btn btn-success">✓ Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal confirmar toggle -->
<div class="modal-overlay" id="modalToggle">
    <div class="modal-box" style="max-width:460px;">
        <h3 id="modalToggleTitle">🔄 Cambiar Tipo de Representante</h3>
        <div id="modalToggleBody" style="margin:12px 0;color:#555;font-size:0.88rem;"></div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalToggle')">Cancelar</button>
            <a id="confirmToggleLink" href="#" class="btn btn-primary">✓ Confirmar</a>
        </div>
    </div>
</div>

<script>
function openEdit(repId, stuId, repName, stuName, relationship, isPrimary) {
    document.getElementById('editRepId').value       = repId;
    document.getElementById('editStuId').value       = stuId;
    document.getElementById('editRepName').textContent = repName;
    document.getElementById('editStuName').textContent = stuName;
    const sel = document.getElementById('editRelationship');
    for (let o of sel.options) o.selected = (o.value === relationship);
    document.getElementById('editIsPrimary').checked = isPrimary == 1;
    document.getElementById('modalEdit').classList.add('on');
}
function applyFilters() {
    const rep    = document.getElementById('filterRep').value.toLowerCase();
    const stu    = document.getElementById('filterStu').value.toLowerCase();
    const course = document.getElementById('filterCourse').value.toLowerCase();
    document.querySelectorAll('.rep-block').forEach(block => {
        const repName = block.dataset.repname;
        const rows = block.querySelectorAll('.student-row');
        let vis = false;
        rows.forEach(row => {
            const ok = (!rep || repName.includes(rep)) &&
                       (!stu || row.dataset.student.includes(stu)) &&
                       (!course || row.dataset.course.includes(course));
            row.style.display = ok ? '' : 'none';
            if(ok) vis = true;
        });
        block.style.display = vis ? '' : 'none';
    });
}
function clearFilters() {
    ['filterRep','filterStu','filterCourse'].forEach(id => document.getElementById(id).value = '');
    applyFilters();
}
function confirmRemove(repId, stuId, repName, stuName) {
    document.getElementById('modalRemoveBody').innerHTML =
        '<p>¿Eliminar la relación entre:</p>' +
        '<p style="margin:10px 0;padding:10px;background:#f8f9fa;border-radius:6px;">' +
        '<strong>Representante:</strong> ' + repName + '<br>' +
        '<strong>Estudiante:</strong> ' + stuName + '</p>' +
        '<p style="color:#dc3545;font-size:0.82rem;">El representante ya no podrá ver la información del estudiante.</p>';
    document.getElementById('confirmRemoveLink').href =
        '?action=remove_representative&rep_id=' + repId + '&student_id=' + stuId;
    document.getElementById('modalRemove').classList.add('on');
}
function confirmToggle(repId, stuId, repName, stuName, isPrimary) {
    const accion = isPrimary
        ? 'pasar a <strong>Secundario</strong>'
        : 'marcar como <strong>Principal</strong> (los demás pasarán a Secundario)';
    document.getElementById('modalToggleTitle').textContent =
        isPrimary ? '🔄 Cambiar a Secundario' : '⭐ Marcar como Principal';
    document.getElementById('modalToggleBody').innerHTML =
        '<p>' + repName + ' → ' + stuName + '</p>' +
        '<p style="margin:10px 0;padding:10px;background:#fff3cd;border-radius:6px;">' +
        'Se va a ' + accion + '.</p>';
    document.getElementById('confirmToggleLink').href =
        '?action=toggle_primary_representative&rep_id=' + repId + '&student_id=' + stuId;
    document.getElementById('modalToggle').classList.add('on');
}
function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if(e.target === m) closeModal(m.id); });
});
</script>
</body>
</html>