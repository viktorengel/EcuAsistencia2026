<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justificaciones Revisadas - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=pending_justifications">Justificaciones</a> &rsaquo;
    Revisadas
</div>

<div class="container">

    <!-- Header -->
    <div class="page-header green">
        <div class="ph-icon">✅</div>
        <div>
            <h1>Justificaciones Revisadas</h1>
            <p>Historial de justificaciones aprobadas y rechazadas</p>
        </div>
        <div class="ph-actions">
            <a href="?action=pending_justifications" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.5);">
                ⏳ Ver Pendientes
            </a>
        </div>
    </div>

    <!-- Filtros tab-like -->
    <div style="display:flex;gap:8px;margin-bottom:16px;">
        <a href="?action=reviewed_justifications"
           class="btn <?= ($filter === 'all') ? 'btn-primary' : 'btn-outline' ?>">
            Todas (<?= count($justifications) ?>)
        </a>
        <a href="?action=reviewed_justifications&filter=aprobado"
           class="btn <?= ($filter === 'aprobado') ? 'btn-success' : 'btn-outline' ?>">
            ✅ Aprobadas
        </a>
        <a href="?action=reviewed_justifications&filter=rechazado"
           class="btn <?= ($filter === 'rechazado') ? 'btn-danger' : 'btn-outline' ?>">
            ❌ Rechazadas
        </a>
    </div>

    <!-- Tabla -->
    <?php if(empty($justifications)): ?>
    <div class="empty-state">
        <div class="icon">📋</div>
        <p>No hay justificaciones revisadas aún.</p>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <div class="table-info">
            <span>📋 <strong><?= count($justifications) ?></strong> registros</span>
        </div>
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
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($justifications as $j):
                    $badgeClass = $j['status'] === 'aprobado' ? 'badge-green' : 'badge-red';
                    $badgeLabel = $j['status'] === 'aprobado' ? '✅ Aprobada' : '❌ Rechazada';
                    $jData = htmlspecialchars(json_encode([
                        'student' => $j['student_name'],
                        'reason'  => $j['reason'],
                        'notes'   => $j['review_notes'] ?? '',
                        'doc'     => $j['document_path'] ?? '',
                        'status'  => $j['status']
                    ]), ENT_QUOTES);
                ?>
                <tr>
                    <td style="color:#999;"><?= $i++ ?></td>
                    <td><strong><?= htmlspecialchars($j['student_name']) ?></strong></td>
                    <td><?= htmlspecialchars($j['course_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($j['subject_name']) ?></td>
                    <td><?= date('d/m/Y', strtotime($j['attendance_date'])) ?></td>
                    <td style="color:#666;"><?= htmlspecialchars($j['submitted_by_name']) ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                    <td style="font-size:0.82rem;color:#666;"><?= htmlspecialchars($j['reviewer_name'] ?? '—') ?></td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="verDetalle('<?= $jData ?>')">
                            👁 Ver
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

<!-- Modal detalle -->
<div class="modal-overlay" id="modalDetalle">
    <div class="modal-box" style="max-width:520px;">
        <h3>📄 Detalle de Justificación</h3>
        <div id="modal-body" style="margin:16px 0;padding:12px;background:#f8f9fa;border-radius:6px;font-size:0.88rem;"></div>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalDetalle')">Cerrar</button>
        </div>
    </div>
</div>

<script>
function verDetalle(jsonStr) {
    const d = JSON.parse(jsonStr);
    const bc = d.status === 'aprobado' ? '#d4edda' : '#f8d7da';
    const tc = d.status === 'aprobado' ? '#155724' : '#721c24';
    const lbl = d.status === 'aprobado' ? '✅ Aprobada' : '❌ Rechazada';
    let html = '<p><strong>Estudiante:</strong> ' + d.student + '</p>';
    html += '<p style="margin-top:10px;"><strong>Estado:</strong> <span style="background:' + bc + ';color:' + tc + ';padding:3px 10px;border-radius:12px;font-size:0.78rem;">' + lbl + '</span></p>';
    html += '<p style="margin-top:10px;"><strong>Motivo:</strong></p><p style="margin-top:4px;color:#555;">' + d.reason + '</p>';
    if (d.notes) html += '<p style="margin-top:10px;"><strong>Observación del revisor:</strong></p><p style="margin-top:4px;color:#555;">' + d.notes + '</p>';
    if (d.doc) html += '<p style="margin-top:12px;"><strong>Documento:</strong> <a href="<?= BASE_URL ?>/' + d.doc + '" target="_blank" style="color:#007bff;">📎 Ver archivo</a></p>';
    else html += '<p style="margin-top:12px;color:#999;"><em>Sin documento adjunto</em></p>';
    document.getElementById('modal-body').innerHTML = html;
    document.getElementById('modalDetalle').classList.add('on');
}
function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if(e.target === m) closeModal(m.id); });
});
</script>
</body>
</html>
