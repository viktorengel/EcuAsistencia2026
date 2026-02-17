<?php
require_once BASE_PATH . '/helpers/Security.php';
Security::requireLogin();
if (!Security::hasRole('autoridad')) {
    die('Acceso denegado');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso - EcuAsist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 70px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">✏️ Editar Curso: <?= htmlspecialchars($course['name']) ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="courseForm">
                            <div class="mb-3">
                                <label class="form-label">Nivel *</label>
                                <select name="grade_level" id="grade_level" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <?php 
                                    $levels = ['8vo EGB', '9no EGB', '10mo EGB', '1ro BGU', '2do BGU', '3ro BGU', '1ro Técnico', '2do Técnico', '3ro Técnico'];
                                    foreach($levels as $level): 
                                    ?>
                                        <option value="<?= $level ?>" <?= $course['grade_level'] == $level ? 'selected' : '' ?>><?= $level ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Paralelo *</label>
                                <select name="parallel" id="parallel" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach(range('A', 'Z') as $letter): ?>
                                        <option value="<?= $letter ?>" <?= $course['parallel'] == $letter ? 'selected' : '' ?>><?= $letter ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jornada *</label>
                                <select name="shift_id" id="shift_id" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach($shifts as $shift): ?>
                                        <option value="<?= $shift['id'] ?>" 
                                                data-shift="<?= $shift['name'] ?>"
                                                <?= $course['shift_id'] == $shift['id'] ? 'selected' : '' ?>>
                                            <?= ucfirst($shift['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nombre del Curso (generado automáticamente)</label>
                                <input type="text" name="name" id="course_name" class="form-control" readonly style="background: #f0f0f0;" value="<?= htmlspecialchars($course['name']) ?>">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="?action=academic" class="btn btn-secondary">← Cancelar</a>
                                <button type="submit" class="btn btn-success">✓ Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function generateCourseName() {
            const level = document.getElementById('grade_level').value;
            const parallel = document.getElementById('parallel').value;
            const shiftSelect = document.getElementById('shift_id');
            const shiftOption = shiftSelect.options[shiftSelect.selectedIndex];
            const shiftName = shiftOption ? shiftOption.getAttribute('data-shift') : '';
            
            if (level && parallel && shiftName) {
                const courseName = `${level} "${parallel}" - ${shiftName.charAt(0).toUpperCase() + shiftName.slice(1)}`;
                document.getElementById('course_name').value = courseName;
            }
        }
        
        document.getElementById('grade_level').addEventListener('change', generateCourseName);
        document.getElementById('parallel').addEventListener('change', generateCourseName);
        document.getElementById('shift_id').addEventListener('change', generateCourseName);
    </script>
</body>
</html>