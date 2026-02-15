<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Docente Tutor - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #f8f9fa; font-weight: bold; }
        h2 { margin-bottom: 20px; color: #333; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>
        <div class="container">
            <?php if(isset($_GET['tutor_success'])): ?>
                <div class="success">✓ Tutor asignado correctamente</div>
            <?php endif; ?>
            <?php if(isset($_GET['tutor_error'])): ?>
                <div class="error">✗ <?= htmlspecialchars($_GET['tutor_error']) ?></div>
            <?php endif; ?>
            <?php if(isset($_GET['tutor_removed'])): ?>
                <div class="success">✓ Tutor eliminado correctamente</div>
            <?php endif; ?>

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

                    <button type="submit">Asignar Tutor</button>
                </form>

                <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
                
            </div>

            <div class="card">
                <h2>Gestión de Tutores por Curso</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Docente Tutor</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($courses as $course): ?>
                        <?php
                            // Buscar si el curso tiene tutor
                            $courseTutor = null;
                            foreach($assignments as $assignment) {
                                if ($assignment['course_name'] == $course['name'] && $assignment['is_tutor']) {
                                    $courseTutor = $assignment;
                                    break;
                                }
                            }
                        ?>
                        <tr>
                            <td><?= $course['name'] ?></td>
                            <td>
                                <?php if($courseTutor): ?>
                                    <?= $courseTutor['teacher_name'] ?>
                                <?php else: ?>
                                    <em style="color: #999;">Sin tutor asignado</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($courseTutor): ?>
                                    <form method="POST" action="?action=remove_tutor" style="display: inline;" onsubmit="return confirmRemoveTutor(event, '<?= $courseTutor['teacher_name'] ?>', '<?= $course['name'] ?>')">
                                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                        <button type="submit" class="btn-danger" style="padding: 5px 10px; font-size: 12px;">
                                            × Quitar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
    let currentTutor = null;

    document.getElementById('course_tutor').addEventListener('change', function() {
        const courseId = this.value;
        const teacherSelect = document.getElementById('teacher_tutor');
        currentTutor = null;
        
        teacherSelect.innerHTML = '<option value="">Cargando...</option>';
        
        if (!courseId) {
            teacherSelect.innerHTML = '<option value="">Primero seleccione un curso...</option>';
            return;
        }
        
        fetch('?action=check_course_tutor&course_id=' + courseId)
            .then(response => response.json())
            .then(tutor => {
                if (tutor && tutor.tutor_name) {
                    currentTutor = tutor.tutor_name;
                }
            });
        
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

    document.getElementById('tutorForm').addEventListener('submit', function(e) {
        if (currentTutor) {
            e.preventDefault();
            
            const modal = document.createElement('div');
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
            
            const modalContent = document.createElement('div');
            modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
            
            modalContent.innerHTML = `
                <h3 style="margin: 0 0 15px 0; color: #333;">⚠️ Confirmar Cambio de Tutor</h3>
                <p style="margin: 0 0 20px 0; color: #666;">
                    El curso ya tiene como tutor a: <strong>${currentTutor}</strong>.<br>
                    ¿Desea reemplazarlo por el nuevo docente?
                </p>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" id="cancelBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="button" id="confirmBtn" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Sí, Reemplazar
                    </button>
                </div>
            `;
            
            modal.appendChild(modalContent);
            document.body.appendChild(modal);
            
            const form = this;
            
            document.getElementById('confirmBtn').onclick = function() {
                document.body.removeChild(modal);
                currentTutor = null;
                form.submit();
            };
            
            document.getElementById('cancelBtn').onclick = function() {
                document.body.removeChild(modal);
            };
        }
    });

    function confirmRemoveTutor(event, tutorName, courseName) {
        event.preventDefault();
        
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
        
        modalContent.innerHTML = `
            <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Quitar Tutor</h3>
            <p style="margin: 0 0 20px 0; color: #666;">
                ¿Está seguro de quitar a <strong>${tutorName}</strong> como tutor del curso <strong>${courseName}</strong>?
            </p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="cancelRemoveBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Cancelar
                </button>
                <button type="button" id="confirmRemoveBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Sí, Quitar
                </button>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        const form = event.target;
        
        document.getElementById('confirmRemoveBtn').onclick = function() {
            document.body.removeChild(modal);
            form.submit();
        };
        
        document.getElementById('cancelRemoveBtn').onclick = function() {
            document.body.removeChild(modal);
        };
        
        return false;
    }
    </script>
</body>
</html>