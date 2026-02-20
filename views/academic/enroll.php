<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Matricular Estudiantes - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 24px auto; padding: 0 16px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .student-list { margin-top: 20px; }
        .student-item { padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px; display: flex; align-items: center; }
        .student-item input[type="checkbox"] { margin-right: 10px; }
        button { padding: 12px 30px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 20px; }
        button:hover { background: #218838; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; background: #007bff; color: white; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=academic">Configuración Académica</a> &rsaquo;
    Matricular Estudiantes
</div>

<div class="container">

    <div class="page-header" style="background:linear-gradient(135deg,#2e7d32,#388e3c);">
        <div class="ph-icon">🎓</div>
        <div>
            <h1>Matricular Estudiantes</h1>
            <p>Asigna estudiantes a sus cursos del año lectivo activo</p>
        </div>
    </div>
        <?php if(isset($_GET['enrolled'])): ?>
            <div class="success">✓ <?= $_GET['enrolled'] ?> estudiante(s) matriculado(s) correctamente</div>
        <?php endif; ?>
        
        <?php if(isset($_GET['errors']) && $_GET['errors'] > 0): ?>
            <div class="warning">⚠ <?= $_GET['errors'] ?> estudiante(s) no pudieron ser matriculados (ya están en otro curso)</div>
        <?php endif; ?>

        <?php if(isset($_GET['unenrolled'])): ?>
            <div class="success">✓ Estudiante retirado del curso correctamente</div>
        <?php endif; ?>

        <?php if(isset($_GET['error']) && $_GET['error'] === 'not_enrolled'): ?>
            <div class="warning">⚠ El estudiante no está matriculado en ningún curso</div>
        <?php endif; ?>

        <?php if(isset($_GET['error']) && $_GET['error'] === 'unenroll_failed'): ?>
            <div class="warning">✗ Error al retirar el estudiante del curso</div>
        <?php endif; ?>

        <!-- Resumen de estudiantes -->
        <div class="card">
            <h2>Resumen</h2>
            <div class="info">
                <strong>Total estudiantes registrados:</strong> <?= count($allStudents) ?><br>
                <strong>Ya matriculados:</strong> <?= count($allStudents) - count($availableStudents) ?><br>
                <strong>Disponibles para matricular:</strong> <?= count($availableStudents) ?>
            </div>
        </div>

        <!-- Formulario de matrícula -->
        <?php if(count($availableStudents) > 0): ?>
        <div class="card">
            <h2>Matricular Estudiantes Disponibles</h2>
            <p style="color: #666; margin-bottom: 20px;">
                Solo se muestran estudiantes que aún NO están matriculados en ningún curso del año lectivo actual.
            </p>
            
            <form method="POST" action="?action=enroll_students">
                <div class="form-group">
                    <label>Curso</label>
                    <select name="course_id" required>
                        <option value="">Seleccionar curso...</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['id'] ?>">
                                <?= $course['name'] ?> - <?= $course['shift_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="student-list">
                    <label>Seleccionar Estudiantes</label>
                    <?php foreach($availableStudents as $student): ?>
                    <div class="student-item">
                        <input type="checkbox" name="student_ids[]" value="<?= $student['id'] ?>" id="student_<?= $student['id'] ?>">
                        <label for="student_<?= $student['id'] ?>" style="margin: 0; font-weight: normal;">
                            <?= $student['last_name'] . ' ' . $student['first_name'] ?> 
                            (<?= $student['dni'] ?? 'Sin cédula' ?>)
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit">Matricular Seleccionados</button>
            </form>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="warning">
                ⚠ No hay estudiantes disponibles para matricular. Todos los estudiantes ya están asignados a cursos.
            </div>
        </div>
        <?php endif; ?>

        <!-- Lista de todos los estudiantes con su estado -->
        <div class="card">
            <h2>Estado de Todos los Estudiantes</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Apellidos y Nombres</th>
                        <th>Cédula</th>
                        <th>Curso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 1;
                    foreach($allStudents as $student): 
                        $course = $this->userModel->getStudentCourse($student['id'], $activeYear['id']);
                    ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= $student['last_name'] . ' ' . $student['first_name'] ?></td>
                        <td><?= $student['dni'] ?? '-' ?></td>
                        <td>
                            <?php if($course): ?>
                                <strong><?= $course['name'] ?></strong>
                                <br>
                                <small style="color: #666;"><?= $course['shift_name'] ?></small>
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($course): ?>
                                <span class="badge" style="background: #28a745;">
                                    ✓ Matriculado
                                </span>
                            <?php else: ?>
                                <span class="badge" style="background: #6c757d;">Sin matricular</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($course): ?>
                                <form method="POST" action="?action=unenroll_student" style="display: inline;" 
                                      onsubmit="return confirmUnenroll(event, '<?= addslashes($student['last_name'] . ' ' . $student['first_name']) ?>', '<?= addslashes($course['name']) ?>')">
                                    <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                                    <button type="submit" 
                                            style="padding: 6px 12px; background: #dc3545; font-size: 13px;">
                                        ✗ Retirar
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function confirmUnenroll(event, studentName, courseName) {
        event.preventDefault();
        
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
        
        modalContent.innerHTML = `
            <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Retirar Estudiante del Curso</h3>
            <p style="margin: 0 0 20px 0; color: #666;">
                ¿Está seguro de retirar a <strong>${studentName}</strong> del curso <strong>${courseName}</strong>?
            </p>
            <p style="margin: 0 0 20px 0; color: #666; font-size: 14px; background: #fff3cd; padding: 10px; border-radius: 4px;">
                <strong>⚠️ Advertencia:</strong> Esta acción quitará al estudiante del curso. 
                Los registros de asistencia se conservarán pero el estudiante no aparecerá como matriculado.
            </p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="cancelUnenrollBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Cancelar
                </button>
                <button type="button" id="confirmUnenrollBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Sí, Retirar
                </button>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        const form = event.target;
        
        document.getElementById('confirmUnenrollBtn').onclick = function() {
            document.body.removeChild(modal);
            form.submit();
        };
        
        document.getElementById('cancelUnenrollBtn').onclick = function() {
            document.body.removeChild(modal);
        };
        
        return false;
    }
    </script>
</body>
</html>