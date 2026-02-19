<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justificar Ausencia - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=my_attendance">Mi Asistencia</a> &rsaquo;
    Justificar Ausencia
</div>

<div class="container" style="max-width:700px;">

    <!-- Header -->
    <div class="page-header orange">
        <div class="ph-icon">📝</div>
        <div>
            <h1>Justificar Ausencia</h1>
            <p>Complete el formulario para solicitar la justificación</p>
        </div>
    </div>

    <div class="panel">
        <div class="alert alert-info" style="margin-bottom:20px;">
            📌 Puede adjuntar documentos de respaldo (certificados médicos, etc.). Formatos: PDF, JPG, PNG — máximo 5MB.
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <?php if(isset($attendanceId)): ?>
                <input type="hidden" name="attendance_id" value="<?= (int)$attendanceId ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Motivo de la Ausencia *</label>
                <textarea name="reason" class="form-control" rows="4"
                          placeholder="Describa el motivo de la ausencia..." required></textarea>
            </div>

            <div class="form-group">
                <label>Documento de Respaldo <span style="color:#999;font-weight:400;">(opcional)</span></label>
                <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <div style="display:flex;gap:10px;margin-top:24px;">
                <button type="submit" class="btn btn-success">📤 Enviar Justificación</button>
                <a href="?action=my_attendance" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>

</div>
</body>
</html>
