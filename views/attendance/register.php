<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Asistencia - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
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
        .class-card { border: 1px solid #ddd; padding: 15px; border-radius: 4px; margin-bottom: 10px; cursor: pointer; transition: all 0.3s; }
        .class-card:hover { background: #f8f9fa; border-color: #007bff; }
        .class-card.selected { background: #e7f3ff; border-color: #007bff; }
        .class-info { display: flex; justify-content: space-between; align-items: center; }
        .class-details h3 { color: #007bff; margin-bottom: 5px; }
        .class-details p { color: #666; font-size: 14px; }
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
            <h2>Mis Clases de Hoy</h2>
            <div class="warning">
                ⚠ <strong>Importante:</strong> La asistencia registrada hoy podrá corregirse hasta 48 horas hábiles después (<?= date('d/m/Y', strtotime($maxEditDate)) ?>).
            </div>

            <?php if(empty($todayClasses)): ?>
                <p style="color: #666; text-align: center; padding: 30px;">
                    No tienes clases programadas para hoy según tu horario.
                </p>
            <?php else: ?>
                <div id="classes-list">
                    <?php foreach($todayClasses as $class): ?>
                    <?php
                        $jsonCourse  = json_encode($class['course_name'],  JSON_HEX_TAG | JSON_HEX_APOS);
                        $jsonSubject = json_encode($class['subject_name'],  JSON_HEX_TAG | JSON_HEX_APOS);
                    ?>
                    <div class="class-card" onclick='selectClass(event, <?= $class["id"] ?>, <?= $jsonCourse ?>, <?= $jsonSubject ?>, <?= $class["period_number"] ?>)'>
                        <div class="class-info">
                            <div class="class-details">
                                <h3><?= $class['period_number'] ?>ra hora - <?= $class['subject_name'] ?></h3>
                                <p>Curso: <?= $class['course_name'] ?></p>
                            </div>
                            <div>
                                <button type="button" style="padding: 8px 15px; font-size: 14px;">
                                    Tomar Asistencia
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card" id="date-selector" style="display: none;">
            <h2>Seleccionar Fecha</h2>
            <form id="config-form">
                <input type="hidden" id="schedule_id" name="schedule_id">
                <div class="form-group">
                    <label>Clase Seleccionada</label>
                    <input type="text" id="selected_class_info" readonly style="background: #f0f0f0;">
                </div>
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" 
                        max="<?= date('Y-m-d') ?>" 
                        min="<?= $minDate ?>" 
                        required>
                    <small style="color: #666; display: block; margin-top: 5px;">
                        📅 La asistencia de hoy podrá editarse hasta el <?= date('d/m/Y', strtotime($maxEditDate)) ?>
                    </small>
                </div>
                <button type="button" onclick="loadStudents()">Cargar Estudiantes</button>
            </form>
        </div>

        <form method="POST" id="student-list">
            <input type="hidden" name="schedule_id" id="schedule_id_hidden">
            <input type="hidden" name="date" id="date_hidden">

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
        let selectedScheduleId = null;

        function selectClass(event, scheduleId, courseName, subjectName, periodNumber) {
            selectedScheduleId = scheduleId;
            
            // Remover selección anterior
            document.querySelectorAll('.class-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Seleccionar actual
            event.currentTarget.classList.add('selected');
            
            // Mostrar selector de fecha
            document.getElementById('date-selector').style.display = 'block';
            document.getElementById('schedule_id').value = scheduleId;
            document.getElementById('selected_class_info').value = periodNumber + 'ra hora - ' + subjectName + ' (' + courseName + ')';
            
            // Ocultar lista de estudiantes
            document.getElementById('student-list').style.display = 'none';
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
                alert('Solo puede registrar asistencia de hasta 48 horas hábiles atrás');
                this.value = today;
            }
        });

        function loadStudents() {
            const scheduleId = document.getElementById('schedule_id').value;
            const date = document.getElementById('date').value;

            if (!scheduleId || !date) {
                alert('Complete todos los campos');
                return;
            }

            document.getElementById('schedule_id_hidden').value = scheduleId;
            document.getElementById('date_hidden').value = date;

            let courseId = null;

            // Obtener course_id del horario
            fetch('?action=get_schedule_info&schedule_id=' + scheduleId)
                .then(response => response.json())
                .then(scheduleData => {
                    courseId = scheduleData.course_id;
                    const formData = new FormData();
                    formData.append('course_id', courseId);
                    return fetch('?action=get_students', {
                        method: 'POST',
                        body: formData
                    });
                })
                .then(response => response.json())
                .then(students => {
                    if (students.length === 0) {
                        alert('No hay estudiantes matriculados en este curso');
                        return;
                    }

                    // Consultar asistencia ya registrada para esta clase y fecha
                    return fetch('?action=get_existing_attendance&schedule_id=' + scheduleId + '&date=' + date)
                        .then(response => response.json())
                        .then(existing => {
                            // Convertir a mapa: student_id => {status, observation}
                            const existingMap = {};
                            existing.forEach(a => {
                                existingMap[a.student_id] = { status: a.status, observation: a.observation || '' };
                            });

                            const tbody = document.querySelector('#students-table tbody');
                            tbody.innerHTML = '';

                            const statuses = ['presente', 'ausente', 'tardanza', 'justificado'];

                            students.forEach((student, index) => {
                                const current = existingMap[student.id] || { status: 'presente', observation: '' };
                                const statusOptions = statuses.map(s =>
                                    `<option value="${s}" ${current.status === s ? 'selected' : ''}>${s.charAt(0).toUpperCase() + s.slice(1)}</option>`
                                ).join('');

                                const row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${student.last_name} ${student.first_name}</td>
                                        <td>${student.dni || '-'}</td>
                                        <td>
                                            <select name="status_${student.id}" class="status-select">
                                                ${statusOptions}
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="obs_${student.id}" 
                                                   value="${current.observation}" 
                                                   placeholder="Opcional" style="width: 100%;">
                                        </td>
                                    </tr>
                                `;
                                tbody.innerHTML += row;
                            });

                            document.getElementById('student-list').style.display = 'block';
                            document.getElementById('student-list').scrollIntoView({ behavior: 'smooth' });
                        });
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Diagnóstico
                    fetch('?action=get_existing_attendance&schedule_id=' + scheduleId + '&date=' + date)
                        .then(r => r.text())
                        .then(t => { 
                            console.log('Respuesta get_existing_attendance:', t);
                        });
                });
        }
    </script>
</body>
</html>