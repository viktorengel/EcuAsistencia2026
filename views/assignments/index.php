<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignaciones Docentes - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .btn-primary { background: #007bff; }
        .btn-primary:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; padding: 5px 10px; font-size: 12px; }
        .btn-danger:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #f8f9fa; font-weight: bold; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .badge { padding: 4px 8px; border-radius: 3px; font-size: 11px; background: #ffc107; color: #333; }
        h2 { margin-bottom: 20px; color: #333; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Asignación creada correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['tutor_success'])): ?>
            <div class="success">✓ Tutor asignado correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['tutor_error'])): ?>
            <div class="error">✗ <?= htmlspecialchars($_GET['tutor_error']) ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['removed'])): ?>
            <div class="success">✓ Asignación eliminada</div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="error">✗ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <?php if(isset($_GET['tutor_removed'])): ?>
            <div class="success">✓ Tutor eliminado correctamente</div>
        <?php endif; ?>

        <div class="grid">
            <!-- Asignar Docente -->
            <div class="card">
                <h2>Asignar Docente a Materia</h2>
                <form method="POST" action="?action=create_assignment">
                    <div class="form-group">
                        <label>Docente</label>
                        <select name="teacher_id" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>">
                                    <?= $teacher['last_name'] . ' ' . $teacher['first_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Curso</label>
                        <select name="course_id" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($courses as $course): ?>
                                <option value="<?= $course['id'] ?>">
                                    <?= $course['name'] ?> - <?= $course['shift_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Asignatura</label>
                        <select name="subject_id" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit">Asignar</button>
                </form>
            </div>

            <!-- Asignar Tutor -->
            <div class="card">
                <h2>Asignar Docente Tutor</h2>
                <form method="POST" action="?action=set_tutor" id="tutorForm">
                    <div class="form-group">
                        <label>Curso</label>
                        <select name="course_id" id="course_tutor" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($courses as $course): ?>
                                <option value="<?= $course['id'] ?>">
                                    <?= $course['name'] ?> - <?= $course['shift_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Docente Tutor</label>
                        <select name="teacher_id" id="teacher_tutor" required>
                            <option value="">Primero seleccione un curso...</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary">Asignar Tutor</button>
                </form>
            </div>

            <script>
            document.getElementById('course_tutor').addEventListener('change', function() {
                const courseId = this.value;
                const teacherSelect = document.getElementById('teacher_tutor');
                
                teacherSelect.innerHTML = '<option value="">Cargando...</option>';
                
                if (!courseId) {
                    teacherSelect.innerHTML = '<option value="">Primero seleccione un curso...</option>';
                    return;
                }
                
                fetch('?action=get_course_teachers&course_id=' + courseId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            teacherSelect.innerHTML = '<option value="">No hay docentes asignados a este curso</option>';
                        } else {
                            teacherSelect.innerHTML = '<option value="">Seleccionar...</option>';
                            data.forEach(teacher => {
                                const option = document.createElement('option');
                                option.value = teacher.teacher_id;
                                option.textContent = teacher.teacher_name;
                                teacherSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        teacherSelect.innerHTML = '<option value="">Error al cargar docentes</option>';
                        console.error('Error:', error);
                    });
            });
            </script>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
                    <h3 style="margin-bottom: 15px;">Quitar Tutor</h3>
                    <form method="POST" action="?action=remove_tutor">
                        <div class="form-group">
                            <label>Curso</label>
                            <select name="course_id" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach($courses as $course): ?>
                                    <option value="<?= $course['id'] ?>">
                                        <?= $course['name'] ?> - <?= $course['shift_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-danger" onclick="return confirm('¿Quitar tutor de este curso?')">Quitar Tutor</button>
                    </form>

        </div>

        <!-- Lista de Asignaciones -->
        <div class="card">
            <h2>Todas las Asignaciones</h2>
            <table>
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Curso</th>
                        <th>Asignatura</th>
                        <th>Año Lectivo</th>
                        <th>Tutor</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($assignments as $assignment): ?>
                    <tr>
                        <td><?= $assignment['teacher_name'] ?></td>
                        <td><?= $assignment['course_name'] ?></td>
                        <td><?= $assignment['subject_name'] ?></td>
                        <td><?= $assignment['year_name'] ?></td>
                        <td>
                            <?php if($assignment['is_tutor']): ?>
                                <span class="badge">Tutor</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="?action=remove_assignment" style="display: inline;">
                                <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                <button type="submit" class="btn-danger" onclick="return confirm('¿Eliminar asignación?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Ver por curso -->
        <div class="card">
            <h2>Ver Asignaciones por Curso</h2>
            <form method="GET" action="">
                <input type="hidden" name="action" value="view_course_assignments">
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label>Seleccionar Curso</label>
                        <select name="course_id" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($courses as $course): ?>
                                <option value="<?= $course['id'] ?>">
                                    <?= $course['name'] ?> - <?= $course['shift_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Ver Detalle</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>