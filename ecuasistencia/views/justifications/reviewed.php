<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justificaciones Revisadas - EcuAsist</title>
    <style>
        .badge-aprobado { background:#d4edda; color:#155724; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold; }
        .badge-rechazado{ background:#f8d7da; color:#721c24; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold; }
        .filter-bar { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
        .filter-bar a { padding:6px 16px; border-radius:20px; text-decoration:none; font-size:13px;
                        font-weight:600; border:2px solid; transition:all .15s; }
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%;
                 background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; }
        .modal.open { display:flex; }
        .modal-content { background:white; padding:30px; border-radius:8px; max-width:560px;
                         width:90%; box-shadow:0 4px 20px rgba(0,0,0,.3); position:relative; }
        .modal-close { position:absolute; top:12px; right:16px; font-size:24px;
                       cursor:pointer; color:#888; line-height:1; }
        .modal-close:hover { color:#333; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Justificaciones Revisadas
</div>

<div class="container">

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ Acción realizada correctamente</div>
    <?php endif; ?>

    <div class="page-header" style="background:linear-gradient(135deg,#1b5e20,#388e3c);display:flex;justify-content:space-between;align-items:center;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div class="ph-icon">📋</div>
            <div>
                <h1>Justificaciones Revisadas</h1>
                <p>Historial de justificaciones aprobadas y rechazadas</p>
            </div>
        </div>
        <a href="?action=pending_justifications"
           style="padding:9px 18px;background:white;color:#1b5e20;text-decoration:none;
                  border-radius:6px;font-weight:600;font-size:13px;white-space:nowrap;">
            ⏳ Ver Pendientes
        </a>
    </div>

    <!-- Filtros -->
    <div class="filter-bar">
        <a href="?action=reviewed_justifications"
           style="<?= ($filter==='all') ? 'background:#343a40;color:white;border-color:#343a40;' : 'background:white;color:#343a40;border-color:#343a40;' ?>">
            📋 Todas (<?= count($justifications) ?>)
        </a>
        <a href="?action=reviewed_justifications&filter=aprobado"
           style="<?= ($filter==='aprobado') ? 'background:#28a745;color:white;border-color:#28a745;' : 'background:white;color:#28a745;border-color:#28a745;' ?>">
            ✅ Aprobadas
        </a>
        <a href="?action=reviewed_justifications&filter=rechazado"
           style="<?= ($filter==='rechazado') ? 'background:#dc3545;color:white;border-color:#dc3545;' : 'background:white;color:#dc3545;border-color:#dc3545;' ?>">
            ❌ Rechazadas
        </a>
    </div>

    <?php if(empty($justifications)): ?>
        <div class="empty-state">
            <div class="icon">📋</div>
            <p>No hay justificaciones revisadas con este filtro.</p>
        </div>
    <?php else: ?>
    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estudiante</th>
                    <th>Curso</th>
                    <th>Asignatura</th>
                    <th>Fecha Falta</th>
                    <th>Enviado por</th>
                    <th>Estado</th>
                    <th>Revisado por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($justifications as $j):
                    $jData = htmlspecialchars(json_encode([
                        'student' => $j['student_name'],
                        'reason'  => $j['reason'],
                        'notes'   => $j['review_notes'] ?? '',
                        'doc'     => $j['document_path'] ?? '',
                        'status'  => $j['status'],
                    ]), ENT_QUOTES);
                ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($j['student_name']) ?></td>
                    <td><?= htmlspecialchars($j['course_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($j['subject_name']) ?></td>
                    <td><?= date('d/m/Y', strtotime($j['attendance_date'])) ?></td>
                    <td><?= htmlspecialchars($j['submitted_by_name']) ?></td>
                    <td>
                        <?php if($j['status'] === 'aprobado'): ?>
                            <span class="badge-aprobado">✅ Aprobada</span>
                        <?php else: ?>
                            <span class="badge-rechazado">❌ Rechazada</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($j['reviewer_name'] ?? '-') ?></td>
                    <td>
                        <button class="btn btn-primary"
                                style="padding:5px 12px;font-size:12px;"
                                onclick="verDetalle('<?= $jData ?>')">🔍 Ver</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

<!-- Modal detalle -->
<div id="modal" class="modal" onclick="if(event.target===this)cerrarModal()">
    <div class="modal-content">
        <span class="modal-close" onclick="cerrarModal()">&times;</span>
        <h3 style="margin-bottom:16px;">📄 Detalle de Justificación</h3>
        <div id="modal-body"></div>
    </div>
</div>

<script>
function verDetalle(jsonStr) {
    const d = JSON.parse(jsonStr);
    const badge = d.status === 'aprobado'
        ? '<span style="background:#d4edda;color:#155724;padding:3px 10px;border-radius:10px;">✅ Aprobada</span>'
        : '<span style="background:#f8d7da;color:#721c24;padding:3px 10px;border-radius:10px;">❌ Rechazada</span>';

    let html = `<p><strong>Estudiante:</strong> ${d.student}</p>`;
    html += `<p style="margin-top:10px;"><strong>Estado:</strong> ${badge}</p>`;
    html += `<p style="margin-top:10px;"><strong>Motivo:</strong></p><p style="margin-top:4px;color:#555;">${d.reason}</p>`;

    if (d.notes) {
        html += `<p style="margin-top:10px;"><strong>Observación del revisor:</strong></p><p style="margin-top:4px;color:#555;">${d.notes}</p>`;
    }

    if (d.doc) {
        html += `<p style="margin-top:14px;"><strong>Documento:</strong>
                 <a href="<?= BASE_URL ?>/${d.doc}" target="_blank"
                    style="color:#007bff;">📎 Ver archivo adjunto</a></p>`;
    } else {
        html += `<p style="margin-top:14px;color:#999;"><em>Sin documento adjunto</em></p>`;
    }

    document.getElementById('modal-body').innerHTML = html;
    document.getElementById('modal').classList.add('open');
}

function cerrarModal() {
    document.getElementById('modal').classList.remove('open');
}
</script>

</body>
</html>