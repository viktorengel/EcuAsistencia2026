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
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        #student-list { display: none; }
        select.status-select { width: auto; padding: 5px; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Asistencia registrada correctamente</div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <?php if($_GET['error'] == 'future'): ?>
                <div class="error">✗ No se puede registrar asistencia de fechas futuras</div>
            <?php elseif($_GET['error'] == 'toolate'): ?>
                <div class="error">✗ Solo se puede registrar asistencia de hasta 48 horas atrás</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card">
            <h2>Configuración</h2>
            <div class="warning">
                ⚠ <strong>Importante:</strong> Solo puede registrar asistencia del día actual o hasta 48 horas atrás. No se permiten fechas futuras.
            </div>
            
            <form id="config-form">
                <div class="form-group">
                    <label>Curso</label>
                    <select id="course_id" name="course_id" required>
                        <option value="">Seleccionar curso...</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['id'] ?>" 
                                    data-shift="<?= $course['shift_id'] ?>"
                                    data-shift-name="<?= $course['shift_name'] ?>"
                                    data-level="<?= $course['grade_level'] ?>">
                                <?= $course['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Asignatura</label>
                    <select id="subject_id" name="subject_id" required>
                        <option value="">Primero seleccione un curso...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jornada</label>
                    <input type="text" id="shift_name" readonly style="background: #f0f0f0;">
                    <input type="hidden" id="shift_id" name="shift_id">
                </div>

                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" 
                        max="<?= date('Y-m-d') ?>" 
                        min="<?= $minDate ?>" 
                        required>
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Permitido: Desde el <?= date('d/m/Y', strtotime($minDate)) ?> hasta hoy
                    </small>
                </div>

                <div class="form-group">
                    <label>Hora/Período</label>
                    <select id="hour_period" name="hour_period" required>
                        <option value="">Primero seleccione un curso...</option>
                    </select>
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
        const minDateAllowed = '<?= $minDate ?>';

        // Al cambiar curso: cargar jornada, asignaturas y horas
        document.getElementById('course_id').addEventListener('change', function() {
            const courseId = this.value;
            const selectedOption = this.options[this.selectedIndex];
            const shiftId = selectedOption.getAttribute('data-shift');
            const shiftName = selectedOption.getAttribute('data-shift-name');
            const gradeLevel = selectedOption.getAttribute('data-level');
            
            // Limpiar selects
            document.getElementById('subject_id').innerHTML = '<option value="">Cargando...</option>';
            document.getElementById('hour_period').innerHTML = '<option value="">Seleccionar...</option>';
            
            if (!courseId) {
                document.getElementById('shift_name').value = '';
                document.getElementById('shift_id').value = '';
                document.getElementById('subject_id').innerHTML = '<option value="">Primero seleccione un curso...</option>';
                document.getElementById('hour_period').innerHTML = '<option value="">Primero seleccione un curso...</option>';
                return;
            }
            
            // Establecer jornada
            document.getElementById('shift_name').value = shiftName.charAt(0).toUpperCase() + shiftName.slice(1);
            document.getElementById('shift_id').value = shiftId;
            
            // Cargar asignaturas del curso
            fetch('?action=get_course_subjects&course_id=' + courseId)
                .then(response => response.json())
                .then(subjects => {
                    const subjectSelect = document.getElementById('subject_id');
                    if (subjects.length === 0) {
                        subjectSelect.innerHTML = '<option value="">No hay asignaturas asignadas</option>';
                    } else {
                        subjectSelect.innerHTML = '<option value="">Seleccionar...</option>';
                        subjects.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.subject_id;
                            option.textContent = subject.subject_name;
                            subjectSelect.appendChild(option);
                        });
                    }
                });
            
            // Determinar número de horas según nivel
            let totalHours = 7; // Por defecto EGB y BGU
            if (gradeLevel.includes('Técnico')) {
                totalHours = 8;
            }
            
            // Llenar select de horas
            const hourSelect = document.getElementById('hour_period');
            hourSelect.innerHTML = '<option value="">Seleccionar...</option>';
            for (let i = 1; i <= totalHours; i++) {
                const option = document.createElement('option');
                option.value = i + 'ra hora';
                option.textContent = i + 'ra hora';
                hourSelect.appendChild(option);
            }
        });

        function getBusinessHoursBetween(dateStr1, dateStr2) {
            let businessHours = 0;
            let current = new Date(dateStr1 + 'T00:00:00');
            let end = new Date(dateStr2 + 'T00:00:00');
            
            while (current < end) {
                current.setDate(current.getDate() + 1);
                const dayOfWeek = current.getDay();
                
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    businessHours += 24;
                }
            }
            
            return businessHours;
        }

        document.getElementById('date').addEventListener('change', function() {
            const selectedDate = this.value;
            const today = new Date().toISOString().split('T')[0];

            if (selectedDate > today) {
                alert('No se puede seleccionar una fecha futura');
                this.value = today;
                return;
            }

            if (selectedDate < minDateAllowed) {
                alert('Solo puede registrar asistencia de hasta 48 horas hábiles atrás (excluyendo fines de semana)');
                this.value = today;
                return;
            }

            const businessHours = getBusinessHoursBetween(selectedDate, today);

            if (businessHours > 48) {
                alert('La fecha seleccionada excede las 48 horas hábiles permitidas');
                this.value = today;
            }
        });

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

            const today = new Date().toISOString().split('T')[0];

            if (date > today) {
                alert('No se puede registrar asistencia de fechas futuras');
                return;
            }

            if (date < minDateAllowed) {
                alert('Solo puede registrar asistencia de hasta 48 horas hábiles atrás');
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