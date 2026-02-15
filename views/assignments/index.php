<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignaciones Docentes - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
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
        <?php if(isset($_GET['error'])): ?>
            <div class="error">✗ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['removed'])): ?>
            <div class="success">✓ Asignación eliminada</div>
        <?php endif; ?>

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

        <div class="card">
            <h2>Asignaciones Docente-Materia</h2>
            
            <!-- Filtros -->
            <form method="GET" action="" style="margin-bottom: 20px;">
                <input type="hidden" name="action" value="assignments">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr) auto; gap: 15px; align-items: flex-end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Filtrar por Curso</label>
                        <select name="filter_course" onchange="this.form.submit()">
                            <option value="">Todos los cursos</option>
                            <?php foreach($courses as $course): ?>
                                <option value="<?= $course['id'] ?>" <?= (isset($_GET['filter_course']) && $_GET['filter_course'] == $course['id']) ? 'selected' : '' ?>>
                                    <?= $course['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Filtrar por Asignatura</label>
                        <select name="filter_subject" onchange="this.form.submit()">
                            <option value="">Todas las asignaturas</option>
                            <?php foreach($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>" <?= (isset($_GET['filter_subject']) && $_GET['filter_subject'] == $subject['id']) ? 'selected' : '' ?>>
                                    <?= $subject['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Filtrar por Docente</label>
                        <select name="filter_teacher" onchange="this.form.submit()">
                            <option value="">Todos los docentes</option>
                            <?php foreach($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>" <?= (isset($_GET['filter_teacher']) && $_GET['filter_teacher'] == $teacher['id']) ? 'selected' : '' ?>>
                                    <?= $teacher['last_name'] . ' ' . $teacher['first_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if(isset($_GET['filter_course']) || isset($_GET['filter_subject']) || isset($_GET['filter_teacher'])): ?>
                    <div>
                        <a href="?action=assignments" style="padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">
                            Limpiar
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
            
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Aplicar filtros
                    $filteredAssignments = $assignments;
                    
                    if(isset($_GET['filter_course']) && $_GET['filter_course'] != '') {
                        $filterCourseId = (int)$_GET['filter_course'];
                        $courseName = '';
                        foreach($courses as $c) {
                            if($c['id'] == $filterCourseId) {
                                $courseName = $c['name'];
                                break;
                            }
                        }
                        $filteredAssignments = array_filter($filteredAssignments, function($a) use ($courseName) {
                            return $a['course_name'] == $courseName;
                        });
                    }
                    
                    if(isset($_GET['filter_subject']) && $_GET['filter_subject'] != '') {
                        $filterSubjectId = (int)$_GET['filter_subject'];
                        $subjectName = '';
                        foreach($subjects as $s) {
                            if($s['id'] == $filterSubjectId) {
                                $subjectName = $s['name'];
                                break;
                            }
                        }
                        $filteredAssignments = array_filter($filteredAssignments, function($a) use ($subjectName) {
                            return $a['subject_name'] == $subjectName;
                        });
                    }
                    
                    if(isset($_GET['filter_teacher']) && $_GET['filter_teacher'] != '') {
                        $filterTeacherId = (int)$_GET['filter_teacher'];
                        $teacherName = '';
                        foreach($teachers as $t) {
                            if($t['id'] == $filterTeacherId) {
                                $teacherName = $t['last_name'] . ' ' . $t['first_name'];
                                break;
                            }
                        }
                        $filteredAssignments = array_filter($filteredAssignments, function($a) use ($teacherName) {
                            return $a['teacher_name'] == $teacherName;
                        });
                    }
                    ?>
                    
                    <?php if(empty($filteredAssignments)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">No hay asignaciones que coincidan con los filtros</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($filteredAssignments as $assignment): ?>
                        <tr>
                            <td><?= $assignment['course_name'] ?></td>
                            <td><?= $assignment['subject_name'] ?></td>
                            <td><?= $assignment['teacher_name'] ?></td>
                            <td>
                                <form method="POST" action="?action=remove_assignment" style="display: inline;">
                                    <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                    <button type="submit" class="btn-danger" onclick="confirmDeleteAssignment(event, '<?= $assignment['teacher_name'] ?>', '<?= $assignment['course_name'] ?>', '<?= $assignment['subject_name'] ?>')">
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

    <script>
    function confirmDeleteAssignment(event, teacherName, courseName, subjectName) {
        event.preventDefault();
        
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
        
        modalContent.innerHTML = `
            <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Eliminar Asignación</h3>
            <p style="margin: 0 0 20px 0; color: #666;">
                ¿Está seguro de eliminar esta asignación?<br><br>
                <strong>Docente:</strong> ${teacherName}<br>
                <strong>Curso:</strong> ${courseName}<br>
                <strong>Asignatura:</strong> ${subjectName}
            </p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="cancelDeleteBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Cancelar
                </button>
                <button type="button" id="confirmDeleteBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Sí, Eliminar
                </button>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        const form = event.target.closest('form');
        
        document.getElementById('confirmDeleteBtn').onclick = function() {
            document.body.removeChild(modal);
            form.submit();
        };
        
        document.getElementById('cancelDeleteBtn').onclick = function() {
            document.body.removeChild(modal);
        };
        
        return false;
    }
    </script>
</body>
</html>