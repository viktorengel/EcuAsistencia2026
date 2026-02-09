<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Asistencia - EcuAsist</title>
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
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 12px 30px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        #student-list { display: none; }
        select.status-select { width: auto; padding: 5px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Registrar Asistencia</h1>
        <div>
            <a href="?action=dashboard">← Dashboard</a>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Asistencia registrada correctamente</div>
        <?php endif; ?>

        <div class="card">
            <h2>Configuración</h2>
            <form id="config-form">
                <div class="form-group">
                    <label>Curso</label>
                    <select id="course_id" name="course_id" required>
                        <option value="">Seleccionar curso...</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['id'] ?>">
                                <?= $course['name'] ?> - <?= $course['shift_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Asignatura</label>
                    <select id="subject_id" name="subject_id" required>
                        <option value="">Seleccionar asignatura...</option>
                        <?php foreach($subjects as $subject): ?>
                            <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jornada</label>
                    <select id="shift_id" name="shift_id" required>
                        <option value="">Seleccionar jornada...</option>
                        <?php foreach($shifts as $shift): ?>
                            <option value="<?= $shift['id'] ?>"><?= ucfirst($shift['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label>Hora/Período</label>
                    <input type="text" id="hour_period" name="hour_period" placeholder="Ej: 1ra hora, 08:00-09:00" required>
                </div>

                <button type="button" onclick="loadStudents()">Cargar Estudiantes</button>
            </form>
        </div>

        <form method="POST" id="student-list">
            <input type="hidden" name="course_id" id="course_id_hidden">
            <input type="hidden" name="subject_id" id="subject_id_hidden">
            <input type="hidden" name="shift_id" id="shift_id_hidden">
            <input type="hidden" name="date" id="date_hidden">
            <input type="hidden" name="hour_period" id="hour_period_hidden">

            <div class="card">
                <h2>Lista de Estudiantes</h2>
                <table id="students-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Apellidos y Nombres</th>
                            <th>Cédula</th>
                            <th>Estado</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button type="submit" style="margin-top: 20px;">Guardar Asistencia</button>
            </div>
        </form>
    </div>

    <script>
        function loadStudents() {
            const courseId = document.getElementById('course_id').value;
            const subjectId = document.getElementById('subject_id').value;
            const shiftId = document.getElementById('shift_id').value;
            const date = document.getElementById('date').value;
            const hourPeriod = document.getElementById('hour_period').value;

            if (!courseId || !subjectId || !shiftId || !date || !hourPeriod) {
                alert('Complete todos los campos');
                return;
            }

            document.getElementById('course_id_hidden').value = courseId;
            document.getElementById('subject_id_hidden').value = subjectId;
            document.getElementById('shift_id_hidden').value = shiftId;
            document.getElementById('date_hidden').value = date;
            document.getElementById('hour_period_hidden').value = hourPeriod;

            const formData = new FormData();
            formData.append('course_id', courseId);

            fetch('?action=get_students', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(students => {
                if (students.length === 0) {
                    alert('No hay estudiantes matriculados en este curso');
                    return;
                }

                const tbody = document.querySelector('#students-table tbody');
                tbody.innerHTML = '';

                students.forEach((student, index) => {
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${student.last_name} ${student.first_name}</td>
                            <td>${student.dni || '-'}</td>
                            <td>
                                <select name="status_${student.id}" class="status-select">
                                    <option value="presente" selected>Presente</option>
                                    <option value="ausente">Ausente</option>
                                    <option value="tardanza">Tardanza</option>
                                    <option value="justificado">Justificado</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="obs_${student.id}" placeholder="Opcional" style="width: 100%;">
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });

                document.getElementById('student-list').style.display = 'block';
            });
        }
    </script>
</body>
</html>