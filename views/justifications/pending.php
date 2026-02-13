<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Justificaciones Pendientes - EcuAsist</title>
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
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; margin-right: 5px; }
        .btn-approve { background: #28a745; color: white; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-view { background: #007bff; color: white; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal-content { background: white; margin: 50px auto; padding: 30px; border-radius: 8px; max-width: 600px; }
        .modal-close { float: right; font-size: 28px; cursor: pointer; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Justificación procesada correctamente</div>
        <?php endif; ?>

        <div class="card">
            <h2 style="margin-bottom: 20px;">Justificaciones por Revisar (<?= count($justifications) ?>)</h2>
            
            <?php if(count($justifications) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Fecha Ausencia</th>
                        <th>Asignatura</th>
                        <th>Presentado por</th>
                        <th>Fecha Solicitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($justifications as $just): ?>
                    <tr>
                        <td><?= $just['student_name'] ?></td>
                        <td><?= date('d/m/Y', strtotime($just['attendance_date'])) ?></td>
                        <td><?= $just['subject_name'] ?></td>
                        <td><?= $just['submitted_by_name'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($just['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-view" onclick="viewJustification(<?= $just['id'] ?>, '<?= addslashes($just['reason']) ?>', '<?= $just['document_path'] ?>')">Ver</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: #666; text-align: center; padding: 40px;">No hay justificaciones pendientes</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h3>Revisar Justificación</h3>
            <div id="modal-body"></div>
            
            <form method="POST" action="?action=review_justification">
                <input type="hidden" name="justification_id" id="justification_id">
                <textarea name="notes" placeholder="Notas de revisión (opcional)"></textarea>
                
                <div style="margin-top: 15px;">
                    <button type="submit" name="review_action" value="approve" class="btn btn-approve">✓ Aprobar</button>
                    <button type="submit" name="review_action" value="reject" class="btn btn-reject">✗ Rechazar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function viewJustification(id, reason, document) {
            document.getElementById('justification_id').value = id;
            
            let html = '<p><strong>Motivo:</strong></p><p>' + reason + '</p>';
            
            if (document) {
                html += '<p style="margin-top: 15px;"><strong>Documento:</strong> <a href="/' + document + '" target="_blank">Ver archivo adjunto</a></p>';
            }
            
            document.getElementById('modal-body').innerHTML = html;
            document.getElementById('modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('modal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>