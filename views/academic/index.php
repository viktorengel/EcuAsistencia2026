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
<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Configuración Académica
</div>

<div class="container">

    <div class="page-header" style="background:linear-gradient(135deg,#1a237e,#283593);">
        <div class="ph-icon">🎓</div>
        <div>
            <h1>Configuración Académica</h1>
            <p>Años lectivos, cursos y asignaturas</p>
        </div>
    </div>

        <?php if(isset($_GET['course_success'])): ?>
            <div class="success">✓ Curso creado correctamente.
                <?php if(isset($_GET['subjects_loaded']) && (int)$_GET['subjects_loaded'] > 0): ?>
                    Se pre-cargaron <strong><?= (int)$_GET['subjects_loaded'] ?> asignaturas</strong> según la malla curricular.
                <?php elseif(isset($_GET['subjects_loaded'])): ?>
                    Las asignaturas de este nivel ya estaban registradas.
                <?php endif; ?>
            </div>
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
        <?php if(isset($_GET['error']) && $_GET['error'] === 'course_duplicate'): ?>
            <div class="error">✗ Ya existe un curso con ese nivel, paralelo y jornada en el año lectivo activo. Elige un paralelo diferente.</div>
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
                                          onsubmit="return confirmSY(event,'desactivar','<?= htmlspecialchars(addslashes($year['name'])) ?>')">
                                        <input type="hidden" name="year_id" value="<?= $year['id'] ?>">
                                        <button type="submit" style="padding: 5px 10px; font-size: 12px; background: #6c757d; color:white;">
                                            ⊘ Desactivar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="?action=activate_school_year" style="display: inline;"
                                          onsubmit="return confirmSY(event,'activar','<?= htmlspecialchars(addslashes($year['name'])) ?>')">
                                        <input type="hidden" name="year_id" value="<?= $year['id'] ?>">
                                        <button type="submit" style="padding: 5px 10px; font-size: 12px; background: #28a745; color:white;">
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

                    <!-- NIVEL EDUCATIVO -->
                    <div class="form-group">
                        <label>Nivel Educativo</label>
                        <select name="education_type" id="education_type" required onchange="updateGradeLevels()">
                            <option value="">Seleccionar tipo...</option>
                            <option value="inicial">🧒 Educación Inicial</option>
                            <option value="egb">📘 Educación General Básica (EGB)</option>
                            <option value="bgu">🎓 Bachillerato General Unificado (BGU)</option>
                            <option value="bt">🛠 Bachillerato Técnico (BT)</option>
                        </select>
                    </div>

                    <!-- GRADO -->
                    <div class="form-group" id="group_grade">
                        <label>Grado / Año</label>
                        <select name="grade_level" id="grade_level" required onchange="onGradeChange()">
                            <option value="">Seleccione nivel primero...</option>
                        </select>
                    </div>

                    <!-- FIGURA PROFESIONAL (solo BT) -->
                    <div class="form-group" id="group_specialty" style="display:none;">
                        <label>Figura Profesional</label>
                        <select name="specialty" id="specialty" onchange="updateCarreras()">
                            <option value="">Seleccionar figura...</option>
                            <option value="Informática">Informática</option>
                            <option value="Administración">Administración</option>
                            <option value="Contabilidad">Contabilidad</option>
                            <option value="Comercialización y Ventas">Comercialización y Ventas</option>
                            <option value="Servicios Hoteleros">Servicios Hoteleros</option>
                            <option value="Turismo">Turismo</option>
                            <option value="Electromecánica Automotriz">Electromecánica Automotriz</option>
                            <option value="Instalaciones Eléctricas">Instalaciones Eléctricas</option>
                            <option value="Electrónica de Consumo">Electrónica de Consumo</option>
                            <option value="Mecanizado y Construcciones Metálicas">Mecanizado y Construcciones Metálicas</option>
                            <option value="Producción Agropecuaria">Producción Agropecuaria</option>
                            <option value="Acuicultura">Acuicultura</option>
                            <option value="Industrialización de Alimentos">Industrialización de Alimentos</option>
                            <option value="Confección Textil">Confección Textil</option>
                            <option value="Música">Música</option>
                            <option value="Artes Plásticas">Artes Plásticas</option>
                            <option value="Diseño Gráfico">Diseño Gráfico</option>
                            <option value="Servicios de Belleza">Servicios de Belleza</option>
                            <option value="Atención Integral en Salud">Atención Integral en Salud</option>
                            <option value="Mecatrónica">Mecatrónica</option>
                            <option value="Refrigeración y Climatización">Refrigeración y Climatización</option>
                            <option value="Construcción">Construcción</option>
                            <option value="Redes y Telecomunicaciones">Redes y Telecomunicaciones</option>
                            <option value="Seguridad Industrial">Seguridad Industrial</option>
                            <option value="Logística y Transporte">Logística y Transporte</option>
                        </select>
                    </div>

                    <!-- ESPECIALIDAD / CARRERA (solo BT) -->
                    <div class="form-group" id="group_carrera" style="display:none;">
                        <label>Especialidad / Carrera <span style="color:#999; font-weight:normal;">(opcional)</span></label>
                        <select name="carrera" id="carrera" onchange="generateCourseName()">
                            <option value="">Sin especificar</option>
                        </select>
                    </div>

                    <!-- PARALELO -->
                    <div class="form-group">
                        <label>Paralelo</label>
                        <select name="parallel" id="parallel" required onchange="generateCourseName()">
                            <option value="">Seleccionar...</option>
                            <?php foreach(range('A', 'J') as $letter): ?>
                                <option value="<?= $letter ?>"><?= $letter ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- JORNADA -->
                    <div class="form-group">
                        <label>Jornada</label>
                        <select name="shift_id" id="shift_id" required onchange="generateCourseName()">
                            <option value="">Seleccionar...</option>
                            <?php foreach($shifts as $shift): ?>
                                <option value="<?= $shift['id'] ?>" data-shift="<?= $shift['name'] ?>">
                                    <?= ucfirst($shift['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- NOMBRE GENERADO -->
                    <div class="form-group">
                        <label>Nombre del Curso <span style="color:#999; font-weight:normal;">(generado automáticamente)</span></label>
                        <input type="text" name="name" id="course_name" readonly 
                               style="background: #f0f0f0; font-weight: bold; color: #333;">
                    </div>

                    <button type="submit">Crear Curso</button>
                </form>
            </div>

            <script>
            const gradeLevels = {
                inicial: [
                    'Inicial 1 (0-3 años)',
                    'Inicial 2 (3-5 años)'
                ],
                egb: [
                    '1.º EGB - Preparatoria',
                    '2.º EGB', '3.º EGB', '4.º EGB',
                    '5.º EGB', '6.º EGB', '7.º EGB',
                    '8.º EGB', '9.º EGB', '10.º EGB'
                ],
                bgu: ['1.º BGU', '2.º BGU', '3.º BGU'],
                bt:  ['1.º BT', '2.º BT', '3.º BT']
            };

            // Grados EGB donde se permite jornada nocturna
            const egbNocturnaAllowed = ['8.º EGB', '9.º EGB', '10.º EGB'];

            const carreras = {
                'Informática': ['Aplicaciones Informáticas','Programación de Software','Soporte Técnico','Sistemas Microinformáticos y Redes'],
                'Administración': ['Asistencia Administrativa','Gestión Empresarial'],
                'Contabilidad': ['Contabilidad','Ventas e Información Comercial'],
                'Comercialización y Ventas': ['Ventas e Información Comercial','Marketing'],
                'Servicios Hoteleros': ['Hotelería','Hospitalidad'],
                'Turismo': ['Turismo','Guía Turístico'],
                'Electromecánica Automotriz': ['Electromecánica Automotriz','Mecánica Automotriz'],
                'Instalaciones Eléctricas': ['Electricidad','Instalaciones Eléctricas'],
                'Electrónica de Consumo': ['Electrónica','Mantenimiento Electrónico'],
                'Atención Integral en Salud': ['Atención en Enfermería','Auxiliar de Salud'],
                'Producción Agropecuaria': ['Producción Agropecuaria','Agroindustria'],
                'Redes y Telecomunicaciones': ['Redes','Telecomunicaciones'],
                'Diseño Gráfico': ['Diseño Gráfico','Multimedia'],
                'Servicios de Belleza': ['Peluquería','Cosmetología']
            };

            function getNocturnaOption() {
                const opts = document.getElementById('shift_id').options;
                for (let i = 0; i < opts.length; i++) {
                    if ((opts[i].getAttribute('data-shift') || '').toLowerCase() === 'nocturna') return opts[i];
                }
                return null;
            }

            function updateNocturnaVisibility(type, grade) {
                const nocturna = getNocturnaOption();
                if (!nocturna) return;

                let visible = false;
                if (type === 'bgu' || type === 'bt') {
                    visible = true;
                } else if (type === 'egb' && egbNocturnaAllowed.includes(grade)) {
                    visible = true;
                }

                nocturna.style.display = visible ? '' : 'none';

                // Si estaba seleccionada y ya no aplica, limpiar
                if (!visible && nocturna.selected) {
                    document.getElementById('shift_id').value = '';
                    generateCourseName();
                }
            }

            function updateGradeLevels() {
                const type = document.getElementById('education_type').value;
                const gradeSelect = document.getElementById('grade_level');

                gradeSelect.innerHTML = '<option value="">Seleccionar grado...</option>';
                document.getElementById('course_name').value = '';

                if (!type) return;

                (gradeLevels[type] || []).forEach(function(g) {
                    const opt = document.createElement('option');
                    opt.value = g;
                    opt.textContent = g;
                    gradeSelect.appendChild(opt);
                });

                // Mostrar/ocultar campos BT
                const isBT = (type === 'bt');
                document.getElementById('group_specialty').style.display = isBT ? 'block' : 'none';
                document.getElementById('group_carrera').style.display = isBT ? 'block' : 'none';
                document.getElementById('specialty').required = isBT;

                if (!isBT) {
                    document.getElementById('specialty').value = '';
                    document.getElementById('carrera').innerHTML = '<option value="">Sin especificar</option>';
                }

                updateNocturnaVisibility(type, '');
                generateCourseName();
            }

            function onGradeChange() {
                const type = document.getElementById('education_type').value;
                const grade = document.getElementById('grade_level').value;
                updateNocturnaVisibility(type, grade);
                generateCourseName();
            }

            function updateCarreras() {
                const fig = document.getElementById('specialty').value;
                const carreraSelect = document.getElementById('carrera');
                carreraSelect.innerHTML = '<option value="">Sin especificar</option>';

                if (carreras[fig]) {
                    carreras[fig].forEach(function(c) {
                        const opt = document.createElement('option');
                        opt.value = c;
                        opt.textContent = c;
                        carreraSelect.appendChild(opt);
                    });
                }
                generateCourseName();
            }

            function generateCourseName() {
                const grade    = document.getElementById('grade_level').value;
                const parallel = document.getElementById('parallel').value;
                const shiftSelect = document.getElementById('shift_id');
                const shiftOpt = shiftSelect.options[shiftSelect.selectedIndex];
                const shiftName = shiftOpt ? (shiftOpt.getAttribute('data-shift') || '') : '';
                const type     = document.getElementById('education_type').value;
                const specialty = document.getElementById('specialty').value;
                const carrera  = document.getElementById('carrera').value;

                if (!grade || !parallel || !shiftName) {
                    document.getElementById('course_name').value = '';
                    return;
                }

                let name = grade + ' "' + parallel + '"';

                if (type === 'bt' && specialty) {
                    name += ' - ' + specialty;
                    if (carrera) name += ' (' + carrera + ')';
                }

                name += ' - ' + shiftName.charAt(0).toUpperCase() + shiftName.slice(1);
                document.getElementById('course_name').value = name;
            }
            </script>

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
                        <td><?= htmlspecialchars($course['name']) ?></td>
                        <td><?= htmlspecialchars($course['grade_level']) ?></td>
                        <td><?= htmlspecialchars($course['parallel']) ?></td>
                        <td><?= ucfirst($course['shift_name']) ?></td>
                        <td style="white-space: nowrap;">
                            <button onclick="location.href='?action=course_subjects&course_id=<?= $course['id'] ?>'" 
                                    style="padding: 5px 10px; font-size: 12px; background: #6f42c1; color:white;">
                                📚 Asignaturas
                            </button>
                            <button onclick="location.href='?action=view_course_students&course_id=<?= $course['id'] ?>'" 
                                    style="padding: 5px 10px; font-size: 12px; background: #007bff;">
                                👥 Estudiantes
                            </button>
                            <button onclick="location.href='?action=edit_course&id=<?= $course['id'] ?>'" 
                                    style="padding: 5px 10px; font-size: 12px; background: #ffc107; color: #000;">
                                ✏️ Editar
                            </button>
                            <form method="POST" action="?action=delete_course" style="display: inline;" 
                                  onsubmit="return confirmDeleteCourse(event, '<?= htmlspecialchars(addslashes($course['name'])) ?>')">
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
            <p style="margin: 0 0 20px 0; color: #666;">¿Está seguro de eliminar el curso <strong>${courseName}</strong>?</p>
            <p style="margin: 0 0 20px 0; color: #666; font-size: 14px;"><strong>Nota:</strong> No se puede eliminar si tiene estudiantes o asignaciones docentes.</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="cancelCourseBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
                <button type="button" id="confirmCourseBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">Sí, Eliminar</button>
            </div>`;
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        const form = event.target;
        document.getElementById('confirmCourseBtn').onclick = function() { document.body.removeChild(modal); form.submit(); };
        document.getElementById('cancelCourseBtn').onclick = function() { document.body.removeChild(modal); };
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
            <p style="margin: 0 0 20px 0; color: #666;">¿Está seguro de eliminar el año lectivo <strong>${yearName}</strong>?</p>
            <p style="margin: 0 0 20px 0; color: #666; font-size: 14px;"><strong>Nota:</strong> No se puede eliminar si tiene cursos asociados.</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="cancelYearBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
                <button type="button" id="confirmYearBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">Sí, Eliminar</button>
            </div>`;
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        const form = event.target;
        document.getElementById('confirmYearBtn').onclick = function() { document.body.removeChild(modal); form.submit(); };
        document.getElementById('cancelYearBtn').onclick = function() { document.body.removeChild(modal); };
        return false;
    }
    
    // Modal para activar/desactivar año lectivo
    function confirmSY(event, accion, nombre) {
        event.preventDefault();
        var form = event.target;
        var esActivar = accion === 'activar';
        var modal = document.createElement('div');
        modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:9999;';
        var color = esActivar ? '#28a745' : '#6c757d';
        var icono = esActivar ? '✓' : '⊘';
        var btnTxt = esActivar ? 'Sí, Activar' : 'Sí, Desactivar';
        var desc = esActivar
            ? 'Se desactivará el año lectivo actual y se activará <strong>' + nombre + '</strong>.'
            : '¿Seguro que deseas desactivar <strong>' + nombre + '</strong>?';
        modal.innerHTML = '<div style="background:white;padding:30px;border-radius:8px;max-width:460px;width:90%;box-shadow:0 4px 20px rgba(0,0,0,.3);">'
            + '<h3 style="margin:0 0 12px;color:' + color + ';">' + icono + ' ' + (esActivar ? 'Activar' : 'Desactivar') + ' Año Lectivo</h3>'
            + '<p style="color:#555;margin-bottom:20px;">' + desc + '</p>'
            + '<div style="display:flex;gap:10px;justify-content:flex-end;">'
            + '<button id="btnCancelSY" style="padding:9px 18px;background:#6c757d;color:white;border:none;border-radius:4px;cursor:pointer;">Cancelar</button>'
            + '<button id="btnConfirmSY" style="padding:9px 18px;background:' + color + ';color:white;border:none;border-radius:4px;cursor:pointer;">' + btnTxt + '</button>'
            + '</div></div>';
        document.body.appendChild(modal);
        document.getElementById('btnConfirmSY').onclick = function(){ document.body.removeChild(modal); form.submit(); };
        document.getElementById('btnCancelSY').onclick  = function(){ document.body.removeChild(modal); };
        return false;
    }
</script>
</body>
</html>