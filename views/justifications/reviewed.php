<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Justificaciones Revisadas - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; margin-right: 5px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-outline { background: white; color: #007bff; border: 1px solid #007bff; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .filter-bar { margin-bottom: 20px; display: flex; gap: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal-content { background: white; margin: 50px auto; padding: 30px; border-radius: 8px; max-width: 600px; }
        .modal-close { float: right; font-size: 28px; cursor: pointer; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h2 style="margin-bottom: 20px;">📋 Justificaciones Revisadas</h2>

            <!-- Filtros -->
            <div class="filter-bar">
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

            <?php if (count($justifications) > 0): ?>
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
                    <?php $i = 1; foreach ($justifications as $j): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($j['student_name']) ?></td>
                        <td><?= htmlspecialchars($j['course_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($j['subject_name']) ?></td>
                        <td><?= date('d/m/Y', strtotime($j['attendance_date'])) ?></td>
                        <td><?= htmlspecialchars($j['submitted_by_name']) ?></td>
                        <td>
                            <?php if ($j['status'] === 'aprobado'): ?>
                                <span class="badge badge-success">✅ Aprobada</span>
                            <?php else: ?>
                                <span class="badge badge-danger">❌ Rechazada</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($j['reviewer_name'] ?? '-') ?></td>
                        <td>
                            <button class="btn btn-primary" onclick="verDetalle(
                                '<?= addslashes($j['student_name']) ?>',
                                '<?= addslashes($j['reason']) ?>',
                                '<?= addslashes($j['review_notes'] ?? '') ?>',
                                '<?= $j['document_path'] ?? '' ?>',
                                '<?= $j['status'] ?>'
                            )">Ver</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: #666; text-align: center; padding: 40px;">
                No hay justificaciones revisadas aún
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal detalle -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="cerrarModal()">&times;</span>
            <h3 id="modal-titulo">Detalle de Justificación</h3>
            <div id="modal-body" style="margin-top: 15px;"></div>
        </div>
    </div>

    <script>
        function verDetalle(estudiante, motivo, notas, docPath, estado) {
            const badgeColor = estado === 'aprobado' ? '#d4edda' : '#f8d7da';
            const badgeText = estado === 'aprobado' ? '#155724' : '#721c24';
            const estadoLabel = estado === 'aprobado' ? '✅ Aprobada' : '❌ Rechazada';

            let html = '<p><strong>Estudiante:</strong> ' + estudiante + '</p>';
            html += '<p style="margin-top:10px;"><strong>Estado:</strong> <span style="background:' + badgeColor + ';color:' + badgeText + ';padding:3px 8px;border-radius:10px;">' + estadoLabel + '</span></p>';
            html += '<p style="margin-top:10px;"><strong>Motivo:</strong></p><p style="margin-top:5px;">' + motivo + '</p>';

            if (notas && notas.length > 0) {
                html += '<p style="margin-top:10px;"><strong>Observación del revisor:</strong></p><p style="margin-top:5px;">' + notas + '</p>';
            }

            if (docPath && docPath.length > 0) {
                html += '<p style="margin-top:15px;"><strong>Documento:</strong> <a href="<?= BASE_URL ?>/' + docPath + '" target="_blank" style="color:#007bff;">📎 Ver archivo adjunto</a></p>';
            } else {
                html += '<p style="margin-top:15px;color:#999;"><em>Sin documento adjunto</em></p>';
            }

            document.getElementById('modal-body').innerHTML = html;
            document.getElementById('modal').style.display = 'block';
        }

        function cerrarModal() {
            document.getElementById('modal').style.display = 'none';
        }

        window.onclick = function(e) {
            const modal = document.getElementById('modal');
            if (e.target === modal) cerrarModal();
        }
    </script>
</body>
</html>