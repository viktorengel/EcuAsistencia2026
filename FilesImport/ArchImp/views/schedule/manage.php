<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario de <?= $course['name'] ?> - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select, input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .btn-danger { background: #dc3545; padding: 5px 10px; font-size: 12px; }
        .btn-danger:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #f8f9fa; font-weight: bold; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        h2 { margin-bottom: 20px; color: #333; }
        .course-info { background: #e7f3ff; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Clase agregada al horario</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="success">✓ Clase eliminada del horario</div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="error">✗ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="course-info">
            <h2><?= $course['name'] ?></h2>
            <p><strong>Nivel:</strong> <?= $course['grade_level'] ?> | <strong>Paralelo:</strong> <?= $course['parallel'] ?> | <strong>Jornada:</strong> <?= ucfirst($course['shift_name']) ?></p>
        </div>

        <div class="grid">
            <div class="card">
                <h2>Agregar Clase al Horario</h2>
                <form method="POST" id="scheduleForm">
                    <div class="form-group">
                        <label>Día de la Semana</label>
                        <select name="day_of_week" id="day_of_week" required>
                            <option value="">Seleccionar...</option>
                            <option value="lunes">Lunes</option>
                            <option value="martes">Martes</option>
                            <option value="miercoles">Miércoles</option>
                            <option value="jueves">Jueves</option>
                            <option value="viernes">Viernes</option>
                            <option value="sabado">Sábado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número de Hora</label>
                        <select name="period_number" id="period_number" required>
                            <option value="">Seleccionar...</option>
                            <?php 
                            $maxHours = strpos($course['grade_level'], 'Técnico') !== false ? 8 : 7;
                            for($i = 1; $i <= $maxHours; $i++): 
                            ?>
                                <option value="<?= $i ?>"><?= $i ?>ra hora</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Asignatura</label>
                        <select name="subject_id" id="subject_id" required>
                            <option value="">Seleccionar...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Docente Asignado</label>
                        <input type="text" id="teacher_name" readonly style="background: #f0f0f0;" placeholder="Se asignará automáticamente">
                        <input type="hidden" name="teacher_id" id="teacher_id">
                    </div>

                    <button type="submit" id="submitBtn" disabled style="background: #6c757d;">Agregar Clase</button>
                </form>

                <script>
                const courseId = <?= $course['id'] ?>;
                
                // Cargar asignaturas del curso al cargar la página
                fetch('?action=get_course_subjects_schedule&course_id=' + courseId)
                    .then(response => response.json())
                    .then(data => {
                        const subjectSelect = document.getElementById('subject_id');
                        if (data.length === 0) {
                            subjectSelect.innerHTML = '<option value="">No hay asignaturas asignadas a este curso</option>';
                        } else {
                            subjectSelect.innerHTML = '<option value="">Seleccionar...</option>';
                            data.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.subject_id;
                                option.textContent = item.subject_name;
                                option.setAttribute('data-teacher-id', item.teacher_id);
                                option.setAttribute('data-teacher-name', item.teacher_name);
                                subjectSelect.appendChild(option);
                            });
                        }
                    });
                
                // Al seleccionar asignatura, mostrar docente automáticamente
                document.getElementById('subject_id').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const teacherId = selectedOption.getAttribute('data-teacher-id');
                    const teacherName = selectedOption.getAttribute('data-teacher-name');
                    
                    if (teacherId && teacherName) {
                        document.getElementById('teacher_id').value = teacherId;
                        document.getElementById('teacher_name').value = teacherName;
                        checkFormComplete();
                        
                    } else {
                        document.getElementById('teacher_id').value = '';
                        document.getElementById('teacher_name').value = '';
                        document.getElementById('submitBtn').disabled = true;
                        document.getElementById('submitBtn').style.background = '#6c757d';
                    }
                });
                
                document.getElementById('day_of_week').addEventListener('change', checkFormComplete);
                document.getElementById('period_number').addEventListener('change', checkFormComplete);
                
                function checkFormComplete() {
                    const day = document.getElementById('day_of_week').value;
                    const period = document.getElementById('period_number').value;
                    const subject = document.getElementById('subject_id').value;
                    const teacher = document.getElementById('teacher_id').value;
                    
                    const btn = document.getElementById('submitBtn');
                    if (day && period && subject && teacher) {
                        btn.disabled = false;
                        btn.style.background = '#28a745';
                    } else {
                        btn.disabled = true;
                        btn.style.background = '#6c757d';
                    }
                }
                document.getElementById('day_of_week').addEventListener('change', checkExistingSchedule);
                document.getElementById('period_number').addEventListener('change', checkExistingSchedule);
                
                function checkExistingSchedule() {
                    const day = document.getElementById('day_of_week').value;
                    const period = document.getElementById('period_number').value;
                    
                    if (!day || !period) return;
                    
                    fetch('?action=check_schedule_conflict&course_id=' + courseId + '&day=' + day + '&period=' + period)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                showWarning(`⚠️ Esta hora ya está ocupada con ${data.subject_name} (${data.teacher_name}). Debe eliminarla antes de agregar otra.`);
                                document.getElementById('submitBtn').disabled = true;
                                document.getElementById('submitBtn').style.background = '#6c757d';
                            }
                        });
                }
                
                function showWarning(message) {
                    // Remover advertencia anterior si existe
                    const existingWarning = document.getElementById('scheduleWarning');
                    if (existingWarning) {
                        existingWarning.remove();
                    }
                    
                    const warning = document.createElement('div');
                    warning.id = 'scheduleWarning';
                    warning.style.cssText = 'background: #fff3cd; color: #856404; padding: 12px; border-radius: 4px; margin-top: 15px; border-left: 4px solid #ffc107;';
                    warning.innerHTML = message;
                    
                    document.getElementById('scheduleForm').appendChild(warning);
                    
                    setTimeout(() => {
                        warning.style.transition = 'opacity 0.5s';
                        warning.style.opacity = '0';
                        setTimeout(() => warning.remove(), 500);
                    }, 4000);
                }
                </script>
            </div>

            <div class="card">
                <h2>Horario Actual</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Hora</th>
                            <th>Asignatura</th>
                            <th>Docente</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($schedule)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">No hay clases en el horario</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($schedule as $class): ?>
                            <tr>
                                <td><?= ucfirst($class['day_of_week']) ?></td>
                                <td><?= $class['period_number'] ?>ra hora</td>
                                <td><?= $class['subject_name'] ?></td>
                                <td><?= $class['teacher_name'] ?></td>
                                <td>
                                    <form method="POST" action="?action=delete_schedule_class" style="display: inline;">
                                        <input type="hidden" name="schedule_id" value="<?= $class['id'] ?>">
                                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                        <button type="submit" class="btn-danger" onclick="return confirm('¿Eliminar esta clase del horario?')">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>