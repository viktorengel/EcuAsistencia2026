<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Justificaciones - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=my_attendance">Mi Asistencia</a> &rsaquo;
    Mis Justificaciones
</div>

<div class="container">

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ Justificación enviada correctamente. Está pendiente de revisión.</div>
    <?php endif; ?>

    <!-- Header -->
    <div class="page-header blue">
        <div class="ph-icon">📝</div>
        <div>
            <h1>Mis Justificaciones</h1>
            <p>Historial de solicitudes de justificación enviadas</p>
        </div>
        <div class="ph-actions">
            <a href="?action=my_attendance" class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,0.5);">
                ← Volver a Mi Asistencia
            </a>
        </div>
    </div>

    <?php if(empty($justifications)): ?>
    <div class="empty-state">
        <div class="icon">📄</div>
        <p>No tienes justificaciones enviadas.</p>
        <small style="color:#bbb;">Las justificaciones que envíes aparecerán aquí. Puedes justificar ausencias desde "Mi Asistencia".</small>
    </div>
    <?php else:
        $pendientes = count(array_filter($justifications, fn($j) => $j['status'] === 'pendiente'));
        $aprobadas  = count(array_filter($justifications, fn($j) => $j['status'] === 'aprobado'));
        $rechazadas = count(array_filter($justifications, fn($j) => $j['status'] === 'rechazado'));
    ?>

    <!-- Stat cards -->
    <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
        <div class="stat-card yellow">
            <div class="number"><?= $pendientes ?></div>
            <div class="label">Pendientes</div>
        </div>
        <div class="stat-card green">
            <div class="number"><?= $aprobadas ?></div>
            <div class="label">Aprobadas</div>
        </div>
        <div class="stat-card red">
            <div class="number"><?= $rechazadas ?></div>
            <div class="label">Rechazadas</div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-wrap">
        <div class="table-info">
            <span>📋 <strong><?= count($justifications) ?></strong> justificaciones</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha Ausencia</th>
                    <th>Motivo</th>
                    <th>Documento</th>
                    <th>Estado</th>
                    <th>Enviado</th>
                    <th>Revisado por</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($justifications as $just):
                    [$badgeClass, $badgeLabel] = match($just['status']) {
                        'pendiente' => ['badge-yellow', '⏳ Pendiente'],
                        'aprobado'  => ['badge-green',  '✓ Aprobado'],
                        default     => ['badge-red',    '✗ Rechazado'],
                    };
                ?>
                <tr>
                    <td style="color:#999;"><?= $i++ ?></td>
                    <td><?= date('d/m/Y', strtotime($just['attendance_date'])) ?></td>
                    <td style="max-width:200px;font-size:0.83rem;"><?= htmlspecialchars($just['reason']) ?></td>
                    <td>
                        <?php if($just['document_path']): ?>
                            <a href="<?= BASE_URL ?>/<?= $just['document_path'] ?>" target="_blank"
                               class="btn btn-outline btn-sm">📎 Ver</a>
                        <?php else: ?>
                            <span style="color:#ccc;">—</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                    <td style="color:#888;font-size:0.82rem;"><?= date('d/m/Y H:i', strtotime($just['submitted_at'])) ?></td>
                    <td style="font-size:0.82rem;">
                        <?php if($just['reviewed_by'] ?? false): ?>
                            <?= htmlspecialchars($just['reviewer_name']) ?>
                            <br><small style="color:#999;"><?= date('d/m/Y H:i', strtotime($just['reviewed_at'])) ?></small>
                        <?php else: ?>
                            <span style="color:#ccc;">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="color:#888;font-size:0.82rem;">
                        <?= $just['review_notes'] ? htmlspecialchars($just['review_notes']) : '<span style="color:#ccc;">—</span>' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
