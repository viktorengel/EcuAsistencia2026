<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Asignatura - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 500px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card h2 { margin-bottom: 24px; color: #333; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; color: #555; font-size: 14px; }
        input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        input:focus { border-color: #007bff; outline: none; box-shadow: 0 0 0 2px rgba(0,123,255,.2); }
        .btn-row { display: flex; gap: 10px; margin-top: 24px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn-save { background: #28a745; color: white; }
        .btn-back { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.88; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    🏠 Inicio &rsaquo;
    <a href="?action=academic">Configuración Académica</a> &rsaquo;
    <a href="?action=course_subjects&course_id=<?= $courseId ?>">Asignaturas del Curso</a> &rsaquo;
    Editar Asignatura
</div>

<div class="container">
    <div class="card">
        <h2>✏️ Editar Asignatura</h2>

        <form method="POST" action="?action=edit_course_subject&subject_id=<?= $subjectId ?>&course_id=<?= $courseId ?>">
            <div class="form-group">
                <label>Nombre de la asignatura *</label>
                <input type="text" name="name"
                       value="<?= htmlspecialchars($subject['name']) ?>"
                       required autofocus>
            </div>
            <div class="form-group">
                <label>Código <span style="font-weight:normal;color:#999;">(opcional)</span></label>
                <input type="text" name="code"
                       value="<?= htmlspecialchars($subject['code'] ?? '') ?>"
                       placeholder="Ej: MAT">
            </div>
            <div class="btn-row">
                <button type="submit" class="btn btn-save">💾 Guardar cambios</button>
                <a href="?action=course_subjects&course_id=<?= $courseId ?>" class="btn btn-back">← Cancelar</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>