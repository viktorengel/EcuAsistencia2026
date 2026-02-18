<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justificaciones Pendientes - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=pending_justifications">Justificaciones</a> &rsaquo;
    Pendientes
</div>

<div class="container">

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ Justificación procesada correctamente</div>
    <?php endif; ?>

    <!-- Header -->
    <div class="page-header orange">
        <div class="ph-icon">✅</div>
        <div>
            <h1>Justificaciones Pendientes</h1>
            <p>Revisa, aprueba o rechaza las solicitudes de justificación</p>
        </div>
        <div class="ph-actions">
            <a href="?action=reviewed_justifications" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.5);">
                📋 Ver Revisadas
            </a>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-section">
        <div class="table-wrap">
            <div class="table-info">
                <span>📋 <strong><?= count($justifications) ?></strong> justificaciones pendientes</span>
            </div>

            <?php if(empty($justifications)): ?>
                <div class="empty-state">
                    <div class="icon">✅</div>
                    <p>No hay justificaciones pendientes por revisar</p>
                </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Curso</th>
                        <th>Fecha Ausencia</th>
                        <th>Asignatura</th>
                        <th>Presentado por</th>
                        <th>Fecha Solicitud</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($justifications as $just): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($just['student_name']) ?></strong></td>
                        <td><?= htmlspecialchars($just['course_name'] ?? '—') ?></td>
                        <td><?= date('d/m/Y', strtotime($just['attendance_date'])) ?></td>
                        <td><?= htmlspecialchars($just['subject_name']) ?></td>
                        <td style="color:#666;"><?= htmlspecialchars($just['submitted_by_name']) ?></td>
                        <td style="color:#888;font-size:0.82rem;"><?= date('d/m/Y H:i', strtotime($just['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-info btn-sm"
                                onclick="viewJustification(<?= $just['id'] ?>, '<?= addslashes(htmlspecialchars($just['reason'])) ?>', '<?= $just['document_path'] ?? '' ?>')">
                                👁 Revisar
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Modal Revisar Justificación -->
<div class="modal-overlay" id="modalReview">
    <div class="modal-box" style="max-width:560px;">
        <h3>📄 Revisar Justificación</h3>
        <div id="modal-body" style="margin-bottom:16px;padding:12px;background:#f8f9fa;border-radius:6px;font-size:0.88rem;"></div>

        <form method="POST" action="?action=review_justification">
            <input type="hidden" name="justification_id" id="justification_id">
            <div class="form-group">
                <label>Notas de revisión (opcional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Escribe tus observaciones..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalReview')">Cancelar</button>
                <button type="submit" name="review_action" value="reject" class="btn btn-danger">✗ Rechazar</button>
                <button type="submit" name="review_action" value="approve" class="btn btn-success">✓ Aprobar</button>
            </div>
        </form>
    </div>
</div>

<script>
function viewJustification(id, reason, docPath) {
    document.getElementById('justification_id').value = id;
    var html = '<p><strong>Motivo:</strong></p><p style="margin-top:6px;">' + reason + '</p>';
    if (docPath && docPath.length > 0) {
        html += '<p style="margin-top:12px;"><strong>Documento adjunto:</strong> <a href="<?= BASE_URL ?>/' + docPath + '" target="_blank" style="color:#007bff;">📎 Ver archivo</a></p>';
    } else {
        html += '<p style="margin-top:12px;color:#999;"><em>Sin documento adjunto</em></p>';
    }
    document.getElementById('modal-body').innerHTML = html;
    document.getElementById('modalReview').classList.add('on');
}
function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.modal-overlay').forEach(function(m) {
    m.addEventListener('click', function(e) { if(e.target === m) closeModal(m.id); });
});
</script>

</body>
</html>
