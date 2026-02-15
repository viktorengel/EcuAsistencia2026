<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración Académica - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        button:hover { background: #218838; }
        .btn-primary { background: #007bff; }
        .btn-primary:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #f8f9fa; font-weight: bold; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        h2 { margin-bottom: 20px; color: #333; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php if(isset($_GET['course_success'])): ?>
            <div class="success">✓ Curso creado correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['subject_success'])): ?>
            <div class="success">✓ Asignatura creada correctamente</div>
        <?php endif; ?>

        <?php if(isset($_GET['error']) && $_GET['error'] === 'no_active_year'): ?>
            <div class="error">✗ No hay un año lectivo activo</div>
        <?php endif; ?>

        <div class="grid">
            <!-- Crear Curso -->
            <div class="card">
                <h2>Crear Curso</h2>
                <form method="POST" action="?action=create_course" id="courseForm">
                    <div class="form-group">
                        <label>Nivel</label>
                        <select name="grade_level" id="grade_level" required>
                            <option value="">Seleccionar...</option>
                            <option value="8vo EGB">8vo EGB</option>
                            <option value="9no EGB">9no EGB</option>
                            <option value="10mo EGB">10mo EGB</option>
                            <option value="1ro BGU">1ro BGU</option>
                            <option value="2do BGU">2do BGU</option>
                            <option value="3ro BGU">3ro BGU</option>
                            <option value="1ro Técnico">1ro Técnico</option>
                            <option value="2do Técnico">2do Técnico</option>
                            <option value="3ro Técnico">3ro Técnico</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Paralelo</label>
                        <select name="parallel" id="parallel" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach(range('A', 'Z') as $letter): ?>
                                <option value="<?= $letter ?>"><?= $letter ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jornada</label>
                        <select name="shift_id" id="shift_id" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach($shifts as $shift): ?>
                                <option value="<?= $shift['id'] ?>" data-shift="<?= $shift['name'] ?>">
                                    <?= ucfirst($shift['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nombre del Curso (generado automáticamente)</label>
                        <input type="text" name="name" id="course_name" readonly style="background: #f0f0f0;">
                    </div>
                    <button type="submit">Crear Curso</button>
                </form>
            </div>

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

            <!-- Crear Asignatura -->
            <div class="card">
                <h2>Crear Asignatura</h2>
                <form method="POST" action="?action=create_subject">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="name" placeholder="Ej: Matemáticas" required>
                    </div>
                    <div class="form-group">
                        <label>Código</label>
                        <input type="text" name="code" placeholder="Ej: MAT" required>
                    </div>
                    <button type="submit">Crear Asignatura</button>
                </form>
            </div>
        </div>

        <!-- Lista de Cursos -->
        <div class="card">
            <h2>Cursos Registrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Nivel</th>
                        <th>Paralelo</th>
                        <th>Jornada</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($courses as $course): ?>
                    <tr>
                        <td><?= $course['id'] ?></td>
                        <td><?= $course['name'] ?></td>
                        <td><?= $course['grade_level'] ?></td>
                        <td><?= $course['parallel'] ?></td>
                        <td><?= ucfirst($course['shift_name']) ?></td>
                        <td>
                            <button onclick="location.href='?action=view_course_students&course_id=<?= $course['id'] ?>'" 
                                    style="padding: 5px 10px; font-size: 12px;">
                                Ver Estudiantes
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="location.href='?action=enroll_students'" class="btn-primary" style="margin-top: 15px;">
                Matricular Estudiantes
            </button>
        </div>

        <!-- Lista de Asignaturas -->
        <div class="card">
            <h2>Asignaturas Registradas</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($subjects as $subject): ?>
                    <tr>
                        <td><?= $subject['id'] ?></td>
                        <td><?= $subject['code'] ?></td>
                        <td><?= $subject['name'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>