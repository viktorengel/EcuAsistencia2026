<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Justificaciones - EcuAsist</title>
    <style>
        .badge-pending  { background:#ffc107; color:#000;   padding:5px 12px; border-radius:12px; font-size:12px; font-weight:bold; }
        .badge-approved { background:#28a745; color:white;  padding:5px 12px; border-radius:12px; font-size:12px; font-weight:bold; }
        .badge-rejected { background:#dc3545; color:white;  padding:5px 12px; border-radius:12px; font-size:12px; font-weight:bold; }
        .stat-box { background:white; border-radius:8px; padding:20px; text-align:center;
                    box-shadow:0 2px 8px rgba(0,0,0,.08); flex:1; }
        .stat-box .num { font-size:28px; font-weight:700; }
        .stat-box .lbl { font-size:12px; color:#888; margin-top:4px; }

        /* Modal documento */
        .modal-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(0,0,0,.75); z-index:9999;
            align-items:center; justify-content:center;
        }
        .modal-overlay.on { display:flex; }
        .modal-doc-box {
            background:#fff; border-radius:10px;
            width:90%; max-width:860px; max-height:90vh;
            display:flex; flex-direction:column; overflow:hidden;
            box-shadow:0 8px 40px rgba(0,0,0,.4);
        }
        .modal-doc-header {
            padding:14px 20px; background:#1a237e; color:#fff;
            display:flex; justify-content:space-between; align-items:center;
            font-size:15px; font-weight:600;
        }
        .modal-doc-header a {
            color:#fff; font-size:12px; text-decoration:none;
            padding:5px 12px; border:1px solid rgba(255,255,255,.5);
            border-radius:4px;
        }
        .modal-doc-header a:hover { background:rgba(255,255,255,.15); }
        .modal-doc-body {
            flex:1; overflow:auto; padding:16px;
            display:flex; align-items:center; justify-content:center;
            background:#f5f5f5; min-height:400px;
        }
        .modal-doc-body img  { max-width:100%; max-height:70vh; border-radius:4px; box-shadow:0 2px 12px rgba(0,0,0,.2); }
        .modal-doc-body iframe { width:100%; height:70vh; border:none; border-radius:4px; }
        .modal-close-btn {
            background:none; border:none; color:#fff;
            font-size:22px; cursor:pointer; line-height:1; padding:0 4px;
        }
        .modal-close-btn:hover { opacity:.7; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Mis Justificaciones
</div>

<div class="container">

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ Justificación enviada correctamente. Está pendiente de revisión.</div>
    <?php endif; ?>

    <div class="page-header" style="background:linear-gradient(135deg,#e65100,#ef6c00);display:flex;justify-content:space-between;align-items:center;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div class="ph-icon">📝</div>
            <div>
                <h1>Mis Justificaciones</h1>
                <p>Historial de justificaciones enviadas</p>
            </div>
        </div>
        <a href="?action=submit_justification"
           style="padding:9px 18px;background:white;color:#e65100;text-decoration:none;
                  border-radius:6px;font-weight:600;font-size:13px;white-space:nowrap;">
            ➕ Nueva Justificación
        </a>
    </div>

    <?php if(empty($justifications)): ?>
        <div class="empty-state">
            <div class="icon">📄</div>
            <p>No tienes justificaciones enviadas aún.</p>
            <p style="font-size:13px;color:#aaa;margin-top:6px;">Puedes justificar ausencias desde "Mi Asistencia".</p>
            <?php if(Security::hasRole('representante')): ?>
            <a href="?action=my_children" class="btn btn-primary" style="margin-top:16px;">← Ir a Mis Representados</a>
        <?php else: ?>
            <a href="?action=my_attendance" class="btn btn-primary" style="margin-top:16px;">← Ir a Mi Asistencia</a>
        <?php endif; ?>
        </div>

    <?php else: ?>

        <!-- Resumen -->
        <div style="display:flex;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
            <div class="stat-box">
                <div class="num" style="color:#f57c00;">
                    <?= count(array_filter($justifications, fn($j) => $j['status'] === 'pendiente')) ?>
                </div>
                <div class="lbl">⏳ Pendientes</div>
            </div>
            <div class="stat-box">
                <div class="num" style="color:#2e7d32;">
                    <?= count(array_filter($justifications, fn($j) => $j['status'] === 'aprobado')) ?>
                </div>
                <div class="lbl">✓ Aprobadas</div>
            </div>
            <div class="stat-box">
                <div class="num" style="color:#c62828;">
                    <?= count(array_filter($justifications, fn($j) => $j['status'] === 'rechazado')) ?>
                </div>
                <div class="lbl">✗ Rechazadas</div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Motivo</th>
                        <th>Documento</th>
                        <th>Estado</th>
                        <th>Envío</th>
                        <th>Revisado por</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach($justifications as $just): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= date('d/m/Y', strtotime($just['attendance_date'])) ?></td>
                        <td><?= htmlspecialchars($just['reason']) ?></td>
                        <td>
                            <?php if($just['document_path']): ?>
                                <?php
                                    $filePart = preg_replace('/^uploads\//', '', $just['document_path']);
                                    $docUrl   = BASE_URL . '/img.php?f=' . urlencode($filePart);
                                    $ext      = strtolower(pathinfo($filePart, PATHINFO_EXTENSION));
                                ?>
                                <button type="button"
                                        onclick="openDocModal('<?= addslashes($docUrl) ?>','<?= $ext ?>')"
                                        style="padding:5px 10px;background:#007bff;color:white;border-radius:4px;
                                               border:none;font-size:12px;cursor:pointer;">
                                    📎 Ver
                                </button>
                            <?php else: ?>
                                <span style="color:#aaa;font-size:12px;">Sin documento</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($just['status'] === 'pendiente'): ?>
                                <span class="badge-pending">⏳ Pendiente</span>
                            <?php elseif($just['status'] === 'aprobado'): ?>
                                <span class="badge-approved">✓ Aprobado</span>
                            <?php else: ?>
                                <span class="badge-rejected">✗ Rechazado</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;"><?= date('d/m/Y H:i', strtotime($just['submitted_at'])) ?></td>
                        <td style="font-size:12px;">
                            <?php if($just['reviewed_by']): ?>
                                <?= htmlspecialchars($just['reviewer_name']) ?><br>
                                <span style="color:#888;"><?= date('d/m/Y H:i', strtotime($just['reviewed_at'])) ?></span>
                            <?php else: ?>
                                <span style="color:#aaa;">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;">
                            <?= $just['review_notes'] ? htmlspecialchars($just['review_notes']) : '<span style="color:#aaa;">—</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:16px;">
            <?php if(Security::hasRole('representante')): ?>
            <a href="?action=my_children" class="btn btn-outline">← Volver a Mis Representados</a>
        <?php else: ?>
            <a href="?action=my_attendance" class="btn btn-outline">← Volver a Mi Asistencia</a>
        <?php endif; ?>
        </div>

    <?php endif; ?>

</div>

<!-- Modal Documento -->
<div class="modal-overlay" id="modalDoc">
    <div class="modal-doc-box">
        <div class="modal-doc-header">
            <span>📎 Documento adjunto</span>
            <div style="display:flex;align-items:center;gap:10px;">
                <a id="doc-open-link" href="#" target="_blank">⬇ Abrir en nueva pestaña</a>
                <button class="modal-close-btn" onclick="closeDocModal()">✕</button>
            </div>
        </div>
        <div class="modal-doc-body" id="doc-container">
            <!-- contenido inyectado por JS -->
        </div>
    </div>
</div>

<script>
function openDocModal(url, ext) {
    var container = document.getElementById('doc-container');
    document.getElementById('doc-open-link').href = url;

    if (ext === 'pdf') {
        container.innerHTML = '<iframe src="' + url + '"></iframe>';
    } else {
        container.innerHTML = '<img src="' + url + '" alt="Documento de justificación">';
    }

    document.getElementById('modalDoc').classList.add('on');
}

function closeDocModal() {
    document.getElementById('modalDoc').classList.remove('on');
    document.getElementById('doc-container').innerHTML = '';
}

// Cerrar al hacer clic fuera del modal
document.getElementById('modalDoc').addEventListener('click', function(e) {
    if (e.target === this) closeDocModal();
});

// Cerrar con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDocModal();
});
</script>

</body>
</html>