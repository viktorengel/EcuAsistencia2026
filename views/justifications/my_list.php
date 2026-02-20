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

    <div class="page-header" style="background:linear-gradient(135deg,#e65100,#ef6c00);">
        <div class="ph-icon">📝</div>
        <div>
            <h1>Mis Justificaciones</h1>
            <p>Historial de justificaciones enviadas</p>
        </div>
    </div>

    <?php if(empty($justifications)): ?>
        <div class="empty-state">
            <div class="icon">📄</div>
            <p>No tienes justificaciones enviadas aún.</p>
            <p style="font-size:13px;color:#aaa;margin-top:6px;">Puedes justificar ausencias desde "Mi Asistencia".</p>
            <a href="?action=my_attendance" class="btn btn-primary" style="margin-top:16px;">← Ir a Mi Asistencia</a>
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
                                <a href="<?= BASE_URL ?>/<?= ltrim($just['document_path'],'/') ?>"
                                   target="_blank"
                                   style="padding:5px 10px;background:#007bff;color:white;border-radius:4px;
                                          text-decoration:none;font-size:12px;display:inline-block;">
                                    📎 Ver
                                </a>
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
            <a href="?action=my_attendance" class="btn btn-outline">← Volver a Mi Asistencia</a>
        </div>

    <?php endif; ?>

</div>
</body>
</html>