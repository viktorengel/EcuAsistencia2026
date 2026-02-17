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
        <?php if(isset($_GET['course_updated'])): ?>
            <div class="success">✓ Curso actualizado correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['course_deleted'])): ?>
            <div class="success">✓ Curso eliminado correctamente</div>
        <?php endif; ?>
        
        <?php if(isset($_GET['subject_success'])): ?>
            <div class="success">✓ Asignatura creada correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['subject_updated'])): ?>
            <div class="success">✓ Asignatura actualizada correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['subject_deleted'])): ?>
            <div class="success">✓ Asignatura eliminada correctamente</div>
        <?php endif; ?>
        
        <?php if(isset($_GET['sy_created'])): ?>
            <div class="success">✓ Año lectivo creado correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['sy_updated'])): ?>
            <div class="success">✓ Año lectivo actualizado correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['sy_deleted'])): ?>
            <div class="success">✓ Año lectivo eliminado correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['sy_activated'])): ?>
            <div class="success">✓ Año lectivo activado correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['sy_deactivated'])): ?>
            <div class="success">✓ Año lectivo desactivado correctamente</div>
        <?php endif; ?>

        <?php if(isset($_GET['error']) && $_GET['error'] === 'no_active_year'): ?>
            <div class="error">✗ No hay un año lectivo activo</div>
        <?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error'] === 'course_not_found'): ?>
            <div class="error">✗ Curso no encontrado</div>
        <?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error'] === 'course_has_students'): ?>
            <div class="error">✗ No se puede eliminar el curso porque tiene estudiantes matriculados</div>
        <?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error'] === 'course_has_assignments'): ?>
            <div class="error">✗ No se puede eliminar el curso porque tiene asignaciones docentes</div>
        <?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error'] === 'subject_not_found'): ?>
            <div class="error">✗ Asignatura no encontrada</div>
        <?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error'] === 'subject_has_assignments'): ?>
            <div class="error">✗ No se puede eliminar la asignatura porque tiene asignaciones docentes</div>
        <?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error'] === 'cannot_delete_active'): ?>
            <div class="error">✗ No se puede eliminar el año lectivo activo</div>
        <?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error'] === 'has_courses'): ?>
            <div class="error">✗ No se puede eliminar el año lectivo porque tiene cursos asociados</div>
        <?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error'] === 'year_not_found'): ?>
            <div class="error">✗ Año lectivo no encontrado</div>
        <?php endif; ?>

        <!-- Años Lectivos -->
        <div class="card" style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">📅 Años Lectivos</h2>
                <button onclick="location.href='?action=create_school_year'" class="btn-primary" style="margin: 0;">
                    + Crear Año Lectivo
                </button>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($schoolYears)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; color: #999;">No hay años lectivos registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php $counter = 1; foreach($schoolYears as $year): ?>
                        <tr style="<?= $year['is_active'] ? 'background: #e7f3ff;' : '' ?>">
                            <td><?= $counter++ ?></td>
                            <td><strong><?= htmlspecialchars($year['name']) ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($year['start_date'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($year['end_date'])) ?></td>
                            <td>
                                <?php if($year['is_active']): ?>
                                    <span style="background: #28a745; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                                        ✓ ACTIVO
                                    </span>
                                <?php else: ?>
                                    <span style="background: #6c757d; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px;">
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <button onclick="location.href='?action=edit_school_year&id=<?= $year['id'] ?>'" 
                                        style="padding: 5px 10px; font-size: 12px; background: #ffc107; color: #212529;">
                                    ✏️ Editar
                                </button>
                                
                                <?php if($year['is_active']): ?>
                                    <form method="POST" action="?action=deactivate_school_year" style="display: inline;" 
                                          onsubmit="return confirm('¿Desactivar este año lectivo?')">
                                        <input type="hidden" name="year_id" value="<?= $year['id'] ?>">
                                        <button type="submit" style="padding: 5px 10px; font-size: 12px; background: #6c757d;">
                                            ⊘ Desactivar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="?action=activate_school_year" style="display: inline;" 
                                          onsubmit="return confirm('¿Activar este año lectivo? Se desactivarán los demás.')">
                                        <input type="hidden" name="year_id" value="<?= $year['id'] ?>">
                                        <button type="submit" style="padding: 5px 10px; font-size: 12px; background: #28a745;">
                                            ✓ Activar
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="?action=delete_school_year" style="display: inline;" 
                                          onsubmit="return confirmDelete(event, '<?= htmlspecialchars($year['name']) ?>')">
                                        <input type="hidden" name="year_id" value="<?= $year['id'] ?>">
                                        <button type="submit" style="padding: 5px 10px; font-size: 12px; background: #dc3545;">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

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
                        <th>Acciones</th>
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
                        <td style="white-space: nowrap;">
                            <button onclick="location.href='?action=view_course_students&course_id=<?= $course['id'] ?>'" 
                                    style="padding: 5px 10px; font-size: 12px; background: #007bff;">
                                👥 Estudiantes
                            </button>
                            <button onclick="location.href='?action=edit_course&id=<?= $course['id'] ?>'" 
                                    style="padding: 5px 10px; font-size: 12px; background: #ffc107; color: #000;">
                                ✏️ Editar
                            </button>
                            <form method="POST" action="?action=delete_course" style="display: inline;" 
                                  onsubmit="return confirmDeleteCourse(event, '<?= addslashes($course['name']) ?>')">
                                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                <button type="submit" style="padding: 5px 10px; font-size: 12px; background: #dc3545;">
                                    🗑️ Eliminar
                                </button>
                            </form>
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($subjects as $subject): ?>
                    <tr>
                        <td><?= $subject['id'] ?></td>
                        <td><?= $subject['code'] ?></td>
                        <td><?= $subject['name'] ?></td>
                        <td style="white-space: nowrap;">
                            <button onclick="location.href='?action=edit_subject&id=<?= $subject['id'] ?>'" 
                                    style="padding: 5px 10px; font-size: 12px; background: #ffc107; color: #000;">
                                ✏️ Editar
                            </button>
                            <form method="POST" action="?action=delete_subject" style="display: inline;" 
                                  onsubmit="return confirmDeleteSubject(event, '<?= addslashes($subject['name']) ?>')">
                                <input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
                                <button type="submit" style="padding: 5px 10px; font-size: 12px; background: #dc3545;">
                                    🗑️ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function confirmDeleteCourse(event, courseName) {
        event.preventDefault();
        
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
        
        modalContent.innerHTML = `
            <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Eliminar Curso</h3>
            <p style="margin: 0 0 20px 0; color: #666;">
                ¿Está seguro de eliminar el curso <strong>${courseName}</strong>?
            </p>
            <p style="margin: 0 0 20px 0; color: #666; font-size: 14px;">
                <strong>Nota:</strong> No se puede eliminar si tiene estudiantes o asignaciones docentes.
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
        
        const form = event.target;
        
        document.getElementById('confirmDeleteBtn').onclick = function() {
            document.body.removeChild(modal);
            form.submit();
        };
        
        document.getElementById('cancelDeleteBtn').onclick = function() {
            document.body.removeChild(modal);
        };
        
        return false;
    }

    function confirmDeleteSubject(event, subjectName) {
        event.preventDefault();
        
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
        
        modalContent.innerHTML = `
            <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Eliminar Asignatura</h3>
            <p style="margin: 0 0 20px 0; color: #666;">
                ¿Está seguro de eliminar la asignatura <strong>${subjectName}</strong>?
            </p>
            <p style="margin: 0 0 20px 0; color: #666; font-size: 14px;">
                <strong>Nota:</strong> No se puede eliminar si tiene asignaciones docentes.
            </p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="cancelDeleteSubBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Cancelar
                </button>
                <button type="button" id="confirmDeleteSubBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Sí, Eliminar
                </button>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        const form = event.target;
        
        document.getElementById('confirmDeleteSubBtn').onclick = function() {
            document.body.removeChild(modal);
            form.submit();
        };
        
        document.getElementById('cancelDeleteSubBtn').onclick = function() {
            document.body.removeChild(modal);
        };
        
        return false;
    }

    function confirmDelete(event, yearName) {
        event.preventDefault();
        
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
        
        modalContent.innerHTML = `
            <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Eliminar Año Lectivo</h3>
            <p style="margin: 0 0 20px 0; color: #666;">
                ¿Está seguro de eliminar el año lectivo <strong>${yearName}</strong>?
            </p>
            <p style="margin: 0 0 20px 0; color: #666; font-size: 14px;">
                <strong>Nota:</strong> No se puede eliminar si tiene cursos asociados.
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
        
        const form = event.target;
        
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