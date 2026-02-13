<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Matricular Estudiantes - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
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
    <div class="navbar">
        <h1>Matricular Estudiantes</h1>
        <div>
            <a href="?action=academic">← Configuración Académica</a>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <?php if(isset($_GET['enrolled'])): ?>
            <div class="success">✓ <?= $_GET['enrolled'] ?> estudiante(s) matriculado(s) correctamente</div>
        <?php endif; ?>
        
        <?php if(isset($_GET['errors']) && $_GET['errors'] > 0): ?>
            <div class="warning">⚠ <?= $_GET['errors'] ?> estudiante(s) no pudieron ser matriculados (ya están en otro curso)</div>
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
            
            <form method="POST">
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
                        <th>Apellidos y Nombres</th>
                        <th>Cédula</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($allStudents as $student): 
                        $course = $this->userModel->getStudentCourse($student['id'], $activeYear['id']);
                    ?>
                    <tr>
                        <td><?= $student['last_name'] . ' ' . $student['first_name'] ?></td>
                        <td><?= $student['dni'] ?? '-' ?></td>
                        <td>
                            <?php if($course): ?>
                                <span class="badge" style="background: #28a745;">
                                    Matriculado en: <?= $course['name'] ?> (<?= $course['shift_name'] ?>)
                                </span>
                            <?php else: ?>
                                <span class="badge" style="background: #6c757d;">Sin matricular</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>