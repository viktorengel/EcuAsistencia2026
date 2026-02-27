<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Representados - EcuAsist</title>
    <style>
        .children-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
        .child-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 20px; border-top: 4px solid #007bff; }
        .child-card h3 { font-size: 1rem; font-weight: 700; color: #007bff; margin-bottom: 16px; }
        .child-info { display: flex; flex-direction: column; gap: 8px; }
        .child-row { display: flex; font-size: 0.85rem; padding: 6px 0; border-bottom: 1px solid #f0f0f0; }
        .child-row:last-child { border-bottom: none; }
        .child-label { font-weight: 600; color: #888; width: 100px; flex-shrink: 0; }
        .child-val { color: #333; }

        /* Modal solicitud */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9999; align-items:center; justify-content:center; }
        .modal-overlay.on { display:flex; }
        .modal-box { background:#fff; border-radius:12px; width:90%; max-width:560px; box-shadow:0 8px 40px rgba(0,0,0,.3); overflow:hidden; }
        .modal-header { padding:16px 22px; background:#1a237e; color:#fff; display:flex; justify-content:space-between; align-items:center; }
        .modal-header h3 { font-size:15px; margin:0; }
        .modal-close { background:none; border:none; color:#fff; font-size:20px; cursor:pointer; line-height:1; }
        .modal-body { padding:22px; }
        .search-input-wrap { position:relative; }
        .search-input-wrap input { width:100%; padding:10px 14px 10px 38px; border:1.5px solid #ddd; border-radius:8px; font-size:14px; }
        .search-input-wrap input:focus { border-color:#1a237e; outline:none; box-shadow:0 0 0 3px rgba(26,35,126,.1); }
        .search-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:15px; }
        #search-results { margin-top:10px; max-height:220px; overflow-y:auto; border:1px solid #eee; border-radius:8px; }
        .result-item { display:flex; align-items:center; gap:12px; padding:10px 14px; cursor:pointer; transition:background .15s; border-bottom:1px solid #f5f5f5; }
        .result-item:last-child { border-bottom:none; }
        .result-item:hover { background:#f0f4ff; }
        .result-avatar { width:34px; height:34px; border-radius:50%; background:#1a237e; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; }
        .result-name { font-size:14px; font-weight:600; color:#333; }
        .result-meta { font-size:12px; color:#888; }
        #selected-student { display:none; background:#e8f5e9; border:1.5px solid #a5d6a7; border-radius:8px; padding:12px 16px; margin-top:10px; font-size:14px; color:#2e7d32; font-weight:600; }
        .form-group { margin-top:14px; }
        .form-group label { font-size:13px; font-weight:600; color:#555; display:block; margin-bottom:5px; }
        .form-group select, .form-group textarea { width:100%; padding:9px 12px; border:1.5px solid #ddd; border-radius:8px; font-size:14px; }
        .form-group select:focus, .form-group textarea:focus { border-color:#1a237e; outline:none; }
        .modal-footer { padding:14px 22px; background:#f8f9fa; display:flex; gap:10px; justify-content:flex-end; border-top:1px solid #eee; }
        .btn-send { padding:9px 22px; background:#1a237e; color:#fff; border:none; border-radius:7px; font-size:14px; font-weight:600; cursor:pointer; }
        .btn-send:hover { background:#283593; }
        .btn-send:disabled { opacity:.5; cursor:not-allowed; }

        /* Solicitudes pendientes */
        .requests-list { margin-top:20px; }
        .request-item { background:#fff; border:1px solid #e0e0e0; border-left:4px solid #ffc107; border-radius:8px; padding:14px 18px; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center; }
        .request-item.rechazado { border-left-color:#dc3545; }
        .request-name { font-weight:700; font-size:14px; color:#333; }
        .request-meta { font-size:12px; color:#888; margin-top:3px; }
        .badge-pend { background:#ffc107; color:#000; padding:3px 10px; border-radius:10px; font-size:11px; font-weight:700; }
        .badge-rech { background:#dc3545; color:#fff; padding:3px 10px; border-radius:10px; font-size:11px; font-weight:700; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Mis Representados
</div>

<div class="container">

    <?php if(isset($_GET['unlinked'])): ?>
    <div class="alert alert-success">✓ Estudiante retirado de tu lista de representados.</div>
    <?php endif; ?>
    <?php if(isset($_GET['request_sent'])): ?>
    <div class="alert alert-success">✓ Solicitud enviada. La autoridad revisará tu pedido pronto.</div>
    <?php endif; ?>
    <?php if(isset($_GET['request_exists'])): ?>
    <div class="alert alert-warning">⚠️ Ya tienes una solicitud pendiente para ese estudiante.</div>
    <?php endif; ?>

    <!-- Header -->
    <div class="page-header teal" style="display:flex;justify-content:space-between;align-items:center;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div class="ph-icon">👨‍👩‍👧‍👦</div>
            <div>
                <h1>Mis Representados</h1>
                <p>Estudiantes vinculados a tu cuenta</p>
            </div>
        </div>
        <button onclick="openModal()" style="background:#fff;color:#0e7490;border:none;border-radius:8px;padding:9px 18px;font-size:14px;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0;">
            ➕ Solicitar Vinculación
        </button>
    </div>

    <?php if(empty($children)): ?>
    <div class="empty-state">
        <div class="icon">👧</div>
        <p>No tienes estudiantes vinculados aún.</p>
        <small style="color:#bbb;display:block;margin-top:6px;">Usa el botón <strong>"Solicitar Vinculación"</strong> para pedir que te asignen un representado.</small>
    </div>
    <?php else: ?>
    <div class="children-grid">
        <?php foreach($children as $child): ?>
        <div class="child-card">
            <h3>👤 <?= htmlspecialchars($child['last_name'] . ' ' . $child['first_name']) ?>
                <?php if($child['is_primary']): ?>
                    <span class="badge badge-yellow" style="font-size:0.7rem;margin-left:6px;">Principal</span>
                <?php endif; ?>
            </h3>
            <div class="child-info">
                <div class="child-row">
                    <span class="child-label">Cédula</span>
                    <span class="child-val"><?= htmlspecialchars($child['dni'] ?? 'No registrada') ?></span>
                </div>
                <div class="child-row">
                    <span class="child-label">Parentesco</span>
                    <span class="child-val"><?= htmlspecialchars($child['relationship']) ?></span>
                </div>
                <div class="child-row">
                    <span class="child-label">Curso</span>
                    <span class="child-val"><?= htmlspecialchars($child['course_name'] ?? 'Sin asignar') ?></span>
                </div>
                <div class="child-row">
                    <span class="child-label">Jornada</span>
                    <span class="child-val"><?= $child['shift_name'] ? ucfirst($child['shift_name']) : '—' ?></span>
                </div>
            </div>
            <div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap;justify-content:space-between;align-items:center;">
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="?action=child_attendance&student_id=<?= $child['id'] ?>" class="btn btn-primary btn-sm">
                        📋 Ver Asistencia
                    </a>
                    <a href="?action=submit_justification&student_id=<?= $child['id'] ?>" class="btn btn-warning btn-sm">
                        ✏️ Justificar
                    </a>
                </div>
                <form method="POST" action="?action=unlink_student" style="margin:0;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="student_id" value="<?= $child['id'] ?>">
                    <button type="button" class="btn btn-sm"
                            style="background:#fff0f0;color:#dc3545;border:1px solid #f5c6cb;"
                            onclick="ecConfirm({icon:'⚠️',title:'Retirar representado',message:'¿Quieres desvincular a <?= htmlspecialchars(addslashes($child['first_name'] . ' ' . $child['last_name'])) ?> de tu cuenta?',okText:'Retirar',onOk:function(){ this.closest('form').submit(); }.bind(this)})">
                        🔗 Retirar
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Solicitudes enviadas -->
    <?php if(!empty($myRequests)): ?>
    <div class="requests-list">
        <h3 style="font-size:14px;color:#888;margin-bottom:12px;font-weight:600;">📋 SOLICITUDES ENVIADAS</h3>
        <?php foreach($myRequests as $req): ?>
        <div class="request-item <?= $req['status'] === 'rechazado' ? 'rechazado' : '' ?>">
            <div>
                <div class="request-name">👤 <?= htmlspecialchars($req['student_name']) ?></div>
                <div class="request-meta">
                    Parentesco: <?= htmlspecialchars($req['relationship']) ?> ·
                    Enviada: <?= date('d/m/Y', strtotime($req['created_at'])) ?>
                    <?php if($req['status'] === 'rechazado' && $req['review_notes']): ?>
                        · <span style="color:#dc3545;">Motivo: <?= htmlspecialchars($req['review_notes']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <?php if($req['status'] === 'pendiente'): ?>
                    <span class="badge-pend">⏳ Pendiente</span>
                <?php else: ?>
                    <span class="badge-rech">✗ Rechazada</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<!-- Modal Solicitud de Vinculación -->
<div class="modal-overlay" id="modalRequest">
    <div class="modal-box">
        <div class="modal-header">
            <h3>➕ Solicitar vinculación con un estudiante</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <form method="POST" action="?action=request_link">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="student_id" id="hidden-student-id">

            <div class="modal-body">
                <p style="font-size:13px;color:#666;margin-bottom:14px;">
                    Busca al estudiante por nombre o cédula. La autoridad revisará y aprobará tu solicitud.
                </p>

                <div class="search-input-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="student-search" placeholder="Escribe nombre o cédula del estudiante..."
                           autocomplete="off" oninput="searchStudents(this.value)">
                </div>
                <div id="search-results"></div>
                <div id="selected-student">✓ Estudiante seleccionado: <span id="selected-name"></span></div>

                <div class="form-group">
                    <label>Parentesco *</label>
                    <select name="relationship" required>
                        <option value="">— Selecciona —</option>
                        <option value="Padre">Padre</option>
                        <option value="Madre">Madre</option>
                        <option value="Abuelo/a">Abuelo/a</option>
                        <option value="Tío/a">Tío/a</option>
                        <option value="Hermano/a">Hermano/a</option>
                        <option value="Tutor legal">Tutor legal</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Mensaje para la autoridad <span style="font-weight:400;color:#aaa;">(opcional)</span></label>
                    <textarea name="message" rows="3" placeholder="Puedes agregar información adicional que justifique tu solicitud..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeModal()" style="padding:9px 18px;background:#fff;border:1.5px solid #ddd;border-radius:7px;font-size:14px;cursor:pointer;">Cancelar</button>
                <button type="submit" class="btn-send" id="btn-send" disabled>📤 Enviar Solicitud</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modalRequest').classList.add('on');
}
function closeModal() {
    document.getElementById('modalRequest').classList.remove('on');
    document.getElementById('student-search').value = '';
    document.getElementById('search-results').innerHTML = '';
    document.getElementById('selected-student').style.display = 'none';
    document.getElementById('hidden-student-id').value = '';
    document.getElementById('btn-send').disabled = true;
}

document.getElementById('modalRequest').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });

var searchTimeout;
var _searchBase = '<?= rtrim(BASE_URL, '/') ?>/?action=search_students_json';

function searchStudents(q) {
    clearTimeout(searchTimeout);
    var results = document.getElementById('search-results');
    if (q.length < 2) { results.innerHTML = ''; return; }

    results.innerHTML = '<div style="padding:10px 14px;color:#aaa;font-size:13px;">Buscando...</div>';

    searchTimeout = setTimeout(function() {
        fetch(_searchBase + '&q=' + encodeURIComponent(q), { credentials: 'same-origin' })
            .then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function(data) {
                if (!data.length) {
                    results.innerHTML = '<div style="padding:12px 14px;color:#aaa;font-size:13px;">No se encontraron estudiantes.</div>';
                    return;
                }
                results.innerHTML = data.map(function(s) {
                    var initials = ((s.last_name||'').charAt(0) + (s.first_name||'').charAt(0)).toUpperCase();
                    var safeName = (s.last_name + ' ' + s.first_name).replace(/\\/g,'\\\\').replace(/'/g,"\\'");
                    return '<div class="result-item" onclick="selectStudent(' + s.id + ', \'' + safeName + '\')">' +
                        '<div class="result-avatar">' + initials + '</div>' +
                        '<div>' +
                            '<div class="result-name">' + s.last_name + ' ' + s.first_name + '</div>' +
                            '<div class="result-meta">CI: ' + (s.dni || 'Sin cédula') + ' &middot; ' + (s.course_name || 'Sin curso') + '</div>' +
                        '</div>' +
                    '</div>';
                }).join('');
            })
            .catch(function(err) {
                results.innerHTML = '<div style="padding:12px 14px;color:#dc3545;font-size:13px;">Error al buscar: ' + err.message + '</div>';
            });
    }, 350);
}

function selectStudent(id, name) {
    document.getElementById('hidden-student-id').value = id;
    document.getElementById('selected-name').textContent = name;
    document.getElementById('selected-student').style.display = 'block';
    document.getElementById('search-results').innerHTML = '';
    document.getElementById('student-search').value = name;
    document.getElementById('btn-send').disabled = false;
}
</script>

</body>
</html>