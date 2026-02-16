<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Justificaciones - EcuAsist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 70px; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 30px auto; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .badge-pending { background: #ffc107; color: #000; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-approved { background: #28a745; color: white; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-rejected { background: #dc3545; color: white; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .table th { background: #f8f9fa; font-weight: bold; }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state .icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">📝 Mis Justificaciones</h2>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    ✓ Justificación enviada correctamente. Está pendiente de revisión.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(empty($justifications)): ?>
                <div class="empty-state">
                    <div class="icon">📄</div>
                    <h4>No tienes justificaciones enviadas</h4>
                    <p class="text-muted">
                        Las justificaciones que envíes aparecerán aquí.<br>
                        Puedes justificar ausencias desde "Mi Asistencia".
                    </p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Motivo</th>
                                <th>Documento</th>
                                <th>Estado</th>
                                <th>Fecha Envío</th>
                                <th>Revisado Por</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 1;
                            foreach($justifications as $just): 
                            ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td><?= date('d/m/Y', strtotime($just['attendance_date'])) ?></td>
                                <td><?= htmlspecialchars($just['reason']) ?></td>
                                <td>
                                    <?php if($just['document_path']): ?>
                                        <a href="<?= BASE_URL ?>/<?= $just['document_path'] ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            📎 Ver Documento
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Sin documento</span>
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
                                <td><?= date('d/m/Y H:i', strtotime($just['submitted_at'])) ?></td>
                                <td>
                                    <?php if($just['reviewed_by']): ?>
                                        <?= htmlspecialchars($just['reviewer_name']) ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($just['reviewed_at'])) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($just['review_notes']): ?>
                                        <small><?= htmlspecialchars($just['review_notes']) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Resumen estadístico -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-warning bg-opacity-10 border-warning">
                            <div class="card-body text-center">
                                <h3 class="mb-0">
                                    <?= count(array_filter($justifications, fn($j) => $j['status'] === 'pendiente')) ?>
                                </h3>
                                <small class="text-muted">Pendientes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success bg-opacity-10 border-success">
                            <div class="card-body text-center">
                                <h3 class="mb-0">
                                    <?= count(array_filter($justifications, fn($j) => $j['status'] === 'aprobado')) ?>
                                </h3>
                                <small class="text-muted">Aprobadas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger bg-opacity-10 border-danger">
                            <div class="card-body text-center">
                                <h3 class="mb-0">
                                    <?= count(array_filter($justifications, fn($j) => $j['status'] === 'rechazado')) ?>
                                </h3>
                                <small class="text-muted">Rechazadas</small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center">
            <a href="?action=my_attendance" class="btn btn-secondary">
                ← Volver a Mi Asistencia
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>