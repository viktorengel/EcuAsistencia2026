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
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Justificaciones Pendientes
</div>

<div class="container">

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ Justificación procesada correctamente</div>
    <?php endif; ?>

    <div class="page-header" style="background:linear-gradient(135deg,#1b5e20,#2e7d32);">
        <div class="ph-icon">✅</div>
        <div>
            <h1>Justificaciones Pendientes</h1>
            <p><?= count($justifications) ?> solicitud<?= count($justifications)!=1?'es':'' ?> por revisar</p>
        </div>
    </div>

    <?php if(empty($justifications)): ?>
    <div class="empty-state">
        <div class="icon">📭</div>
        <p>No hay justificaciones pendientes de revisión.</p>
    </div>

    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estudiante</th>
                    <th>Curso</th>
                    <th>Período de ausencia</th>
                    <th style="text-align:center;">Días Lab.</th>
                    <th style="text-align:center;">Revisión</th>
                    <th>Enviado por</th>
                    <th>Fecha solicitud</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($justifications as $just):
                    $days = (int)($just['working_days'] ?? 1);
                    $canApprove = $just['can_approve'] ?? 'inspector';

                    if ($canApprove === 'tutor') {
                        $approverLabel = '🎓 Tutor';
                        $approverColor = 'badge-green';
                    } elseif ($canApprove === 'inspector') {
                        $approverLabel = '👁 Inspector';
                        $approverColor = 'badge-yellow';
                    } else {
                        $approverLabel = '⚙️ Autoridad';
                        $approverColor = 'badge-red';
                    }

                    // Período de ausencia
                    if (!empty($just['date_from'])) {
                        $dateFrom = date('d/m/Y', strtotime($just['date_from']));
                        $dateTo   = !empty($just['date_to']) && $just['date_to'] !== $just['date_from']
                            ? date('d/m/Y', strtotime($just['date_to']))
                            : null;
                        $period   = $dateTo ? "$dateFrom al $dateTo" : $dateFrom;
                    } elseif (!empty($just['attendance_date'])) {
                        $period = date('d/m/Y', strtotime($just['attendance_date']));
                    } else {
                        $period = '—';
                    }
                ?>
                <tr>
                    <td style="color:#999;font-size:12px;"><?= $i++ ?></td>
                    <td><strong><?= htmlspecialchars($just['student_name']) ?></strong></td>
                    <td style="font-size:13px;"><?= htmlspecialchars($just['course_name'] ?? '—') ?></td>
                    <td style="font-size:13px;"><?= $period ?></td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;color:<?= $days<=3?'#2e7d32':'#f57f17' ?>">
                            <?= $days ?>
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <span class="badge <?= $approverColor ?>"><?= $approverLabel ?></span>
                    </td>
                    <td style="font-size:13px;color:#666;"><?= htmlspecialchars($just['submitted_by_name']) ?></td>
                    <td style="font-size:12px;color:#999;"><?= date('d/m/Y H:i', strtotime($just['created_at'])) ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm"
                            onclick="openReview(
                                <?= $just['id'] ?>,
                                <?= json_encode($just['reason'] ?? '') ?>,
                                <?= json_encode($just['document_path'] ?? '') ?>,
                                <?= json_encode($period) ?>,
                                <?= $days ?>
                            )">👁 Revisar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

<!-- Modal revisar -->
<div class="modal-overlay" id="modalReview">
    <div class="modal-box" style="max-width:520px;">
        <h3 style="margin-bottom:16px;">📋 Revisar Justificación</h3>

        <div style="background:#f8f9fa;border-radius:6px;padding:12px;margin-bottom:14px;font-size:13px;">
            <div><strong>Período:</strong> <span id="rv-period">—</span></div>
            <div style="margin-top:4px;"><strong>Días laborables:</strong> <span id="rv-days">—</span></div>
        </div>

        <div style="margin-bottom:12px;">
            <strong style="font-size:13px;">Motivo:</strong>
            <p id="rv-reason" style="margin-top:6px;font-size:14px;color:#444;line-height:1.5;"></p>
        </div>

        <div id="rv-doc" style="margin-bottom:14px;display:none;">
            <strong style="font-size:13px;">Documento adjunto:</strong>
            <div style="margin-top:6px;">
                <a id="rv-doc-link" href="#" target="_blank" class="btn btn-info btn-sm">📎 Ver documento</a>
            </div>
        </div>

        <form method="POST" action="?action=review_justification">
            <input type="hidden" name="justification_id" id="rv-id">
            <div class="form-group">
                <label style="font-size:13px;">Observaciones <span style="color:#999;font-weight:400;">(opcional)</span></label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Notas para el estudiante..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalReview')">Cancelar</button>
                <button type="submit" name="review_action" value="reject"  class="btn btn-danger">✗ Rechazar</button>
                <button type="submit" name="review_action" value="approve" class="btn btn-success">✓ Aprobar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReview(id, reason, docPath, period, days) {
    document.getElementById('rv-id').value       = id;
    document.getElementById('rv-period').textContent = period;
    document.getElementById('rv-days').textContent   = days + ' día' + (days !== 1 ? 's' : '');
    document.getElementById('rv-reason').textContent = reason || '(sin descripción)';

    var docBlock = document.getElementById('rv-doc');
    if (docPath) {
        document.getElementById('rv-doc-link').href = '<?= BASE_URL ?>/' + docPath;
        docBlock.style.display = 'block';
    } else {
        docBlock.style.display = 'none';
    }
    document.getElementById('modalReview').classList.add('on');
}
function openModal(id)  { document.getElementById(id).classList.add('on'); }
function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.modal-overlay').forEach(function(m) {
    m.addEventListener('click', function(e){ if(e.target === m) closeModal(m.id); });
});
</script>

</body>
</html>