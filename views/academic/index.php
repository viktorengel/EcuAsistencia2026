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
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px; padding-bottom:16px; border-bottom:2px solid #f0f0f0;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#1a237e,#283593);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;">🏫</div>
                    <div>
                        <h2 style="margin:0;font-size:17px;color:#1a237e;">Crear Nuevo Curso</h2>
                        <p style="margin:0;font-size:12px;color:#888;">Las asignaturas se cargan automáticamente según el nivel</p>
                    </div>
                </div>

                <form method="POST" action="?action=create_course" id="courseForm">
                <?php
                $cf = $_SESSION['course_form'] ?? [];
                unset($_SESSION['course_form']);
                ?>

                    <!-- Fila 1: Nivel + Grado -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:5px;display:block;">Nivel Educativo *</label>
                            <select name="education_type" id="education_type" required onchange="updateGradeLevels()"
                                style="width:100%;padding:9px 12px;border:1.5px solid #e0e0e0;border-radius:7px;font-size:13px;background:#fafafa;color:#333;outline:none;transition:border-color .2s;"
                                onfocus="this.style.borderColor='#1a237e'" onblur="this.style.borderColor='#e0e0e0'">
                                <option value="">Seleccionar...</option>
                                <option value="inicial" <?= ($cf['education_type']??'')==='inicial'?'selected':'' ?>>🧒 Educación Inicial</option>
                                <option value="egb"     <?= ($cf['education_type']??'')==='egb'    ?'selected':'' ?>>📘 EGB</option>
                                <option value="bgu"     <?= ($cf['education_type']??'')==='bgu'    ?'selected':'' ?>>🎓 BGU</option>
                                <option value="bt"      <?= ($cf['education_type']??'')==='bt'     ?'selected':'' ?>>🛠 Bachillerato Técnico</option>
                            </select>
                        </div>
                        <div class="form-group" id="group_grade" style="margin:0;">
                            <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:5px;display:block;">Grado / Año *</label>
                            <select name="grade_level" id="grade_level" required onchange="onGradeChange()"
                                style="width:100%;padding:9px 12px;border:1.5px solid #e0e0e0;border-radius:7px;font-size:13px;background:#fafafa;color:#333;outline:none;transition:border-color .2s;"
                                onfocus="this.style.borderColor='#1a237e'" onblur="this.style.borderColor='#e0e0e0'">
                                <option value="">Seleccione nivel primero...</option>
                                <?php if(!empty($cf['grade_level'])): ?>
                                <option value="<?= htmlspecialchars($cf['grade_level']) ?>" selected>
                                    <?= htmlspecialchars($cf['grade_level']) ?>
                                </option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- BT: Figura + Carrera (ocultas por defecto) -->
                    <div id="group_specialty" style="display:none;margin-bottom:14px;">
                        <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:5px;display:block;">Figura Profesional</label>
                        <select name="specialty" id="specialty" onchange="updateCarreras()"
                            style="width:100%;padding:9px 12px;border:1.5px solid #e0e0e0;border-radius:7px;font-size:13px;background:#fafafa;color:#333;">
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
                    <div id="group_carrera" style="display:none;margin-bottom:14px;">
                        <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:5px;display:block;">Especialidad / Carrera <span style="color:#aaa;font-weight:400;">(opcional)</span></label>
                        <select name="carrera" id="carrera" onchange="generateCourseName()"
                            style="width:100%;padding:9px 12px;border:1.5px solid #e0e0e0;border-radius:7px;font-size:13px;background:#fafafa;color:#333;">
                            <option value="">Sin especificar</option>
                        </select>
                    </div>

                    <!-- Fila 2: Paralelo + Jornada -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:5px;display:block;">Paralelo *</label>
                            <select name="parallel" id="parallel" required onchange="generateCourseName()"
                                style="width:100%;padding:9px 12px;border:1.5px solid #e0e0e0;border-radius:7px;font-size:13px;background:#fafafa;color:#333;outline:none;transition:border-color .2s;"
                                onfocus="this.style.borderColor='#1a237e'" onblur="this.style.borderColor='#e0e0e0'">
                                <option value="">Seleccionar...</option>
                                <?php foreach(range('A', 'J') as $letter): ?>
                                    <option value="<?= $letter ?>" <?= ($cf['parallel']??'')===$letter?'selected':'' ?>><?= $letter ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:5px;display:block;">Jornada *</label>
                            <select name="shift_id" id="shift_id" required onchange="generateCourseName()"
                                style="width:100%;padding:9px 12px;border:1.5px solid #e0e0e0;border-radius:7px;font-size:13px;background:#fafafa;color:#333;outline:none;transition:border-color .2s;"
                                onfocus="this.style.borderColor='#1a237e'" onblur="this.style.borderColor='#e0e0e0'">
                                <option value="">Seleccionar...</option>
                                <?php foreach($shifts as $shift): ?>
                                    <option value="<?= $shift['id'] ?>" data-shift="<?= $shift['name'] ?>"
                                        <?= ($cf['shift_id']??'')==$shift['id']?'selected':'' ?>>
                                        <?= ucfirst($shift['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Nombre generado -->
                    <div style="margin-bottom:18px;">
                        <label style="font-size:12px;font-weight:600;color:#555;margin-bottom:5px;display:block;">
                            Nombre del Curso
                            <span style="font-weight:400;color:#aaa;margin-left:4px;">← generado automáticamente</span>
                        </label>
                        <div style="position:relative;">
                            <input type="text" name="name" id="course_name" readonly
                                value="<?= htmlspecialchars($cf['name'] ?? '') ?>"
                                style="width:100%;padding:9px 12px 9px 36px;border:1.5px solid #e0e0e0;border-radius:7px;font-size:13px;font-weight:600;color:#1a237e;background:#eef2ff;letter-spacing:0.3px;">
                            <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:15px;">✏️</span>
                        </div>
                    </div>

                    <button type="submit"
                        style="width:100%;padding:11px;background:linear-gradient(135deg,#1a237e,#283593);color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;letter-spacing:0.3px;transition:opacity .2s;"
                        onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                        🏫 Crear Curso
                    </button>
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

            // ── Restaurar form si volvió con error ──────────────────
            (function restoreForm() {
                var savedType  = <?= json_encode($cf['education_type'] ?? '') ?>;
                var savedGrade = <?= json_encode($cf['grade_level']    ?? '') ?>;
                var savedSpec  = <?= json_encode($cf['specialty']      ?? '') ?>;
                if (!savedType) return;
                document.getElementById('education_type').value = savedType;
                updateGradeLevels();
                setTimeout(function() {
                    if (savedGrade) {
                        document.getElementById('grade_level').value = savedGrade;
                        onGradeChange();
                    }
                    if (savedType === 'bt' && savedSpec) {
                        var s = document.getElementById('specialty');
                        if (s) { s.value = savedSpec; updateCarreras(); }
                        setTimeout(function() {
                            var c = document.getElementById('carrera');
                            if (c) c.value = <?= json_encode($cf['carrera'] ?? '') ?>;
                            generateCourseName();
                        }, 60);
                    } else {
                        generateCourseName();
                    }
                }, 60);
            })();
            </script>

        </div>


        <!-- Lista de Cursos -->
        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h2 style="margin:0;">Cursos Registrados</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Jornada</th>
                        <th>Tutor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $n=1; foreach($courses as $course): ?>
                    <tr>
                        <td><?= $n++ ?></td>
                        <td><strong><?= htmlspecialchars($course['name']) ?></strong></td>
                        <td><?= ucfirst($course['shift_name']) ?></td>
                        <td style="white-space:nowrap;">
                            <?php if(!empty($course['tutor_last'])): ?>
                                <span style="color:#1565c0;font-size:13px;">
                                    👤 <?= htmlspecialchars($course['tutor_last'].' '.$course['tutor_first']) ?>
                                </span>
                                <button class="btn-tutor-change"
                                        data-course-id="<?= $course['id'] ?>"
                                        data-course-name="<?= htmlspecialchars($course['name'], ENT_QUOTES) ?>"
                                        data-has-tutor="1"
                                        title="Cambiar tutor"
                                        style="padding:2px 7px;font-size:14px;background:none;border:none;cursor:pointer;color:#f57c00;vertical-align:middle;">
                                    🔄
                                </button>
                                <button class="btn-tutor-remove"
                                        data-course-id="<?= $course['id'] ?>"
                                        data-course-name="<?= htmlspecialchars($course['name'], ENT_QUOTES) ?>"
                                        title="Quitar tutor"
                                        style="padding:2px 7px;font-size:14px;background:none;border:none;cursor:pointer;color:#dc3545;vertical-align:middle;font-weight:bold;">
                                    ✕
                                </button>
                            <?php else: ?>
                                <button class="btn-tutor-assign"
                                        data-course-id="<?= $course['id'] ?>"
                                        data-course-name="<?= htmlspecialchars($course['name'], ENT_QUOTES) ?>"
                                        data-has-tutor="0"
                                        style="padding:3px 10px;font-size:12px;background:#17a2b8;color:white;border:none;border-radius:4px;cursor:pointer;">
                                    👤 Asignar Tutor
                                </button>
                            <?php endif; ?>
                        </td>
                        <td style="white-space:nowrap;">
                            <button onclick="location.href='?action=course_subjects&course_id=<?= $course['id'] ?>'"
                                    style="padding:5px 10px;font-size:12px;background:#6f42c1;color:white;">
                                📚 Asignaturas
                            </button>
                            <button onclick="toggleStudentsRow(<?= $course['id'] ?>)"
                                    id="btnEst_<?= $course['id'] ?>"
                                    style="padding:5px 10px;font-size:12px;background:#007bff;">
                                👥 Estudiantes
                            </button>
                            <button onclick="location.href='?action=edit_course&id=<?= $course['id'] ?>'"
                                    style="padding:5px 10px;font-size:12px;background:#ffc107;color:#000;">
                                ✏️ Editar
                            </button>
                            <form method="POST" action="?action=delete_course" style="display:inline;"
                                  onsubmit="return confirmDeleteCourse(event, '<?= htmlspecialchars(addslashes($course['name'])) ?>')">
                                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                <button type="submit" style="padding:5px 10px;font-size:12px;background:#dc3545;">
                                    🗑️ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <!-- Fila expandible estudiantes -->
                    <tr id="rowEst_<?= $course['id'] ?>" style="display:none;">
                        <td colspan="5" style="padding:0;background:#f8fbff;border-bottom:2px solid #1565c0;">
                            <div style="padding:16px 20px;">
                                <!-- Cabecera del panel -->
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                    <span style="font-weight:700;font-size:14px;color:#1a237e;">
                                        👥 <?= htmlspecialchars($course['name']) ?>
                                        &nbsp;<span style="background:#e3f2fd;color:#1565c0;padding:2px 10px;border-radius:10px;font-size:12px;font-weight:700;">
                                            <?= count($enrollmentsByCourse[$course['id']] ?? []) ?> matriculados
                                        </span>
                                    </span>
                                    <button onclick="openEnrollModal(<?= $course['id'] ?>, '<?= htmlspecialchars(addslashes($course['name']), ENT_QUOTES) ?>')"
                                            style="padding:6px 14px;font-size:12px;background:#28a745;color:#fff;border:none;border-radius:5px;cursor:pointer;font-weight:600;">
                                        ➕ Matricular nuevo
                                    </button>
                                </div>
                                <!-- Buscador -->
                                <?php if(!empty($enrollmentsByCourse[$course['id']])): ?>
                                <input type="text" placeholder="🔍 Buscar estudiante..." oninput="filterInlineEnrolled(this, <?= $course['id'] ?>)"
                                    style="width:100%;max-width:320px;padding:7px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;margin-bottom:10px;">
                                <?php endif; ?>
                                <!-- Tabla matriculados -->
                                <?php if(!empty($enrollmentsByCourse[$course['id']])): ?>
                                <table id="tblEst_<?= $course['id'] ?>" style="width:100%;border-collapse:collapse;font-size:13px;">
                                    <thead>
                                        <tr style="background:#e8eaf6;">
                                            <th style="padding:7px 10px;text-align:left;width:36px;">#</th>
                                            <th style="padding:7px 10px;text-align:left;">Apellidos y Nombres</th>
                                            <th style="padding:7px 10px;text-align:left;">Cédula</th>
                                            <th style="padding:7px 10px;text-align:center;width:90px;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $ni=1; foreach($enrollmentsByCourse[$course['id']] as $est): ?>
                                        <tr data-course="<?= $course['id'] ?>" data-name="<?= strtolower($est['last_name'].' '.$est['first_name']) ?>" style="border-bottom:1px solid #e8eaf6;">
                                            <td style="padding:7px 10px;"><?= $ni++ ?></td>
                                            <td style="padding:7px 10px;"><strong><?= htmlspecialchars($est['last_name'].' '.$est['first_name']) ?></strong></td>
                                            <td style="padding:7px 10px;"><?= $est['dni'] ?? '-' ?></td>
                                            <td style="padding:7px 10px;text-align:center;">
                                                <button onclick="smConfirmUnenroll(<?= $est['id'] ?>, '<?= addslashes($est['last_name'].' '.$est['first_name']) ?>', <?= $course['id'] ?>)"
                                                        style="padding:3px 9px;font-size:12px;background:#dc3545;color:#fff;border:none;border-radius:4px;cursor:pointer;">
                                                    ✕ Retirar
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <div style="text-align:center;padding:20px;color:#aaa;font-size:13px;">📭 Ningún estudiante matriculado aún.</div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div><!-- /container -->
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
            <p style="margin: 0 0 20px 0; font-size: 13px; background:#fff3cd; color:#856404; padding:10px; border-radius:4px;">
                ⚠️ Se desvinculará automáticamente a todos los estudiantes matriculados y docentes asignados.
            </p>
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

<!-- ══ Modal Asignar/Cambiar Tutor ══ -->
<div id="tutorModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:10px;padding:28px;max-width:420px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.2);">
        <h3 style="margin:0 0 6px;color:#1a237e;">👤 <span id="tutorModalTitle">Asignar Tutor</span></h3>
        <p style="margin:0 0 16px;font-size:13px;color:#777;">Curso: <strong id="tutorModalCourse"></strong></p>
        <div id="tutorLoadingMsg" style="color:#999;font-size:13px;margin-bottom:12px;display:none;">⏳ Cargando docentes...</div>
        <div id="tutorNoTeachers" style="display:none;background:#fff3cd;color:#856404;padding:10px;border-radius:6px;font-size:13px;margin-bottom:12px;">
            ⚠️ No hay docentes disponibles. Primero asigna docentes al curso en <strong>Asignaturas</strong>.
        </div>
        <form method="POST" action="?action=set_tutor" id="tutorForm">
            <input type="hidden" name="course_id" id="tutorCourseId">
            <div style="margin-bottom:16px;">
                <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:6px;">Seleccionar Docente</label>
                <select name="teacher_id" id="tutorTeacherSelect"
                        style="width:100%;padding:9px;border:1.5px solid #e0e0e0;border-radius:6px;font-size:13px;">
                    <option value="">Seleccionar...</option>
                </select>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeTutorModal()"
                        style="padding:8px 18px;background:#6c757d;color:white;border:none;border-radius:4px;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit" id="tutorSubmitBtn" disabled
                        style="padding:8px 18px;background:#1976d2;color:white;border:none;border-radius:4px;cursor:pointer;">
                    ✓ Asignar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══ Modal Quitar Tutor ══ -->
<div id="removeTutorModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:10px;padding:28px;max-width:400px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.2);">
        <h3 style="margin:0 0 12px;color:#dc3545;">✕ Quitar Tutor</h3>
        <p style="font-size:14px;color:#555;margin-bottom:20px;">
            ¿Quitar el tutor del curso <strong id="removeTutorCourseName"></strong>?
        </p>
        <form method="POST" action="?action=remove_tutor">
            <input type="hidden" name="course_id" id="removeTutorCourseId">
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeRemoveModal()"
                        style="padding:8px 18px;background:#6c757d;color:white;border:none;border-radius:4px;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                        style="padding:8px 18px;background:#dc3545;color:white;border:none;border-radius:4px;cursor:pointer;">
                    Sí, Quitar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Tutor: event delegation (evita problemas con comillas en nombres) ──
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-tutor-assign, .btn-tutor-change, .btn-tutor-remove');
    if (!btn) return;
    var courseId   = btn.dataset.courseId;
    var courseName = btn.dataset.courseName;
    if (btn.classList.contains('btn-tutor-remove')) {
        openRemoveModal(courseId, courseName);
    } else {
        openTutorModal(courseId, courseName, btn.dataset.hasTutor === '1');
    }
});

function openTutorModal(courseId, courseName, hasTutor) {
    document.getElementById('tutorCourseId').value = courseId;
    document.getElementById('tutorModalCourse').textContent = courseName;
    document.getElementById('tutorModalTitle').textContent  = hasTutor ? 'Cambiar Tutor' : 'Asignar Tutor';
    document.getElementById('tutorTeacherSelect').innerHTML = '<option value="">Seleccionar...</option>';
    document.getElementById('tutorLoadingMsg').style.display = 'block';
    document.getElementById('tutorNoTeachers').style.display = 'none';
    document.getElementById('tutorSubmitBtn').disabled = true;
    document.getElementById('tutorModal').style.display = 'flex';

    fetch('?action=get_course_teachers&course_id=' + courseId)
        .then(function(r){ return r.json(); })
        .then(function(teachers) {
            document.getElementById('tutorLoadingMsg').style.display = 'none';
            if (!teachers || !teachers.length) {
                document.getElementById('tutorNoTeachers').style.display = 'block';
                return;
            }
            var sel = document.getElementById('tutorTeacherSelect');
            teachers.forEach(function(t) {
                var opt = document.createElement('option');
                opt.value = t.teacher_id;
                opt.textContent = t.teacher_name;
                sel.appendChild(opt);
            });
            document.getElementById('tutorSubmitBtn').disabled = false;
        })
        .catch(function() {
            document.getElementById('tutorLoadingMsg').textContent = '✗ Error al cargar docentes.';
            document.getElementById('tutorLoadingMsg').style.color = '#dc3545';
        });
}
function closeTutorModal() { document.getElementById('tutorModal').style.display = 'none'; }

function openRemoveModal(courseId, courseName) {
    document.getElementById('removeTutorCourseId').value = courseId;
    document.getElementById('removeTutorCourseName').textContent = courseName;
    document.getElementById('removeTutorModal').style.display = 'flex';
}
function closeRemoveModal() { document.getElementById('removeTutorModal').style.display = 'none'; }

document.getElementById('tutorModal').addEventListener('click', function(e){ if(e.target===this) closeTutorModal(); });
document.getElementById('removeTutorModal').addEventListener('click', function(e){ if(e.target===this) closeRemoveModal(); });
</script>


<!-- ══ Modal Matricular ══ -->
<div id="enrollModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:10000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:10px;width:90%;max-width:460px;max-height:88vh;display:flex;flex-direction:column;box-shadow:0 8px 40px rgba(0,0,0,.25);">
        <div style="padding:20px 24px 14px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;">
            <div>
                <h3 style="margin:0;font-size:16px;color:#1a237e;">➕ Matricular Estudiantes</h3>
                <p id="emCourseLabel" style="margin:4px 0 0;font-size:13px;color:#777;"></p>
            </div>
            <button onclick="closeEnrollModal()" style="background:none;border:none;font-size:22px;cursor:pointer;color:#aaa;">✕</button>
        </div>
        <div style="padding:16px 24px;overflow-y:auto;flex:1;">
            <form method="POST" action="?action=enroll_students" id="emForm">
                <input type="hidden" name="course_id" id="emCourseId">
                <input type="hidden" name="redirect_to" value="academic">
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;">
                    <input type="text" id="emSearch" placeholder="🔍 Buscar..." oninput="filterEmAvail()"
                        style="flex:1;padding:8px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;">
                    <button type="button" onclick="emSelectAll()"
                        style="padding:7px 13px;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;border-radius:6px;font-size:12px;cursor:pointer;white-space:nowrap;">
                        Seleccionar todos
                    </button>
                </div>
                <div style="margin-bottom:8px;">
                    <span id="emCounter" style="font-size:12px;background:#f0f0f0;color:#555;padding:2px 10px;border-radius:10px;">0 seleccionados</span>
                </div>
                <div id="emList" style="max-height:260px;overflow-y:auto;padding-right:2px;"></div>
                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;padding-top:12px;border-top:1px solid #f0f0f0;">
                    <button type="button" onclick="closeEnrollModal()"
                        style="padding:8px 18px;background:#f5f5f5;color:#555;border:1px solid #ddd;border-radius:6px;cursor:pointer;">Cancelar</button>
                    <button type="submit" id="emSubmit" disabled
                        style="padding:8px 18px;background:#28a745;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;">
                        ✓ Matricular seleccionados
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ Modal Confirmar Retirar ══ -->
<div id="smUnenrollModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:10001;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:10px;padding:28px;max-width:420px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.2);">
        <h3 style="margin:0 0 12px;color:#dc3545;">⚠️ Retirar Estudiante</h3>
        <p id="smUnenrollMsg" style="color:#555;font-size:14px;margin-bottom:6px;"></p>
        <p style="color:#999;font-size:12px;">Los registros de asistencia se conservarán.</p>
        <form method="POST" action="?action=unenroll_student" id="smUnenrollForm">
            <input type="hidden" name="student_id" id="smUnenrollStudentId">
            <input type="hidden" name="course_id" id="smUnenrollCourseId">
            <input type="hidden" name="redirect_to" value="academic">
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
                <button type="button" onclick="closeSmUnenroll()"
                    style="padding:8px 18px;background:#f5f5f5;color:#555;border:1px solid #ddd;border-radius:6px;cursor:pointer;">Cancelar</button>
                <button type="submit"
                    style="padding:8px 18px;background:#dc3545;color:#fff;border:none;border-radius:6px;cursor:pointer;">Sí, Retirar</button>
            </div>
        </form>
    </div>
</div>

<script>
var smAvailableStudents = <?= json_encode(array_values($availableStudents)) ?>;

function toggleStudentsRow(courseId) {
    var row = document.getElementById('rowEst_' + courseId);
    var btn = document.getElementById('btnEst_' + courseId);
    if (!row) return;
    var isOpen = row.style.display !== 'none';
    document.querySelectorAll('[id^="rowEst_"]').forEach(function(r) { r.style.display = 'none'; });
    document.querySelectorAll('[id^="btnEst_"]').forEach(function(b) { b.style.background = '#007bff'; });
    if (!isOpen) {
        row.style.display = '';
        btn.style.background = '#0056b3';
    }
}

function filterInlineEnrolled(input, courseId) {
    var q = input.value.toLowerCase();
    document.querySelectorAll('#tblEst_' + courseId + ' tbody tr').forEach(function(tr) {
        tr.style.display = (tr.dataset.name || '').includes(q) ? '' : 'none';
    });
}

function emToggleCheck(div) {
    var cb = div.querySelector('input[type="checkbox"]');
    if (cb) { cb.checked = !cb.checked; emUpdateCounter(); }
}

function openEnrollModal(courseId, courseName) {
    document.getElementById('emCourseId').value = courseId;
    document.getElementById('emCourseLabel').textContent = courseName;
    document.getElementById('emSearch').value = '';
    document.getElementById('emCounter').textContent = '0 seleccionados';
    document.getElementById('emSubmit').disabled = true;

    var html = '';
    smAvailableStudents.forEach(function(s) {
        var name = (s.last_name || '') + ' ' + (s.first_name || '');
        var dni  = s.dni ? ' <span style="color:#aaa;font-size:11px;">· ' + s.dni + '</span>' : '';
        html += '<div class="emItem" data-name="' + name.toLowerCase() + '" '
            + 'style="display:flex;align-items:center;gap:10px;padding:8px 10px;border:1px solid #e8e8e8;border-radius:6px;margin-bottom:5px;cursor:pointer;" '
            + 'onclick="emToggleCheck(this)" >'
            + '<input type="checkbox" name="student_ids[]" value="' + s.id + '" '
            + 'onclick="event.stopPropagation();" onchange="emUpdateCounter()" '
            + 'style="width:15px;height:15px;cursor:pointer;flex-shrink:0;">'
            + '<span style="font-size:13px;"><strong>' + name + '</strong>' + dni + '</span></div>';
    });
    if (!html) html = '<p style="text-align:center;color:#aaa;padding:20px;font-size:13px;">No hay estudiantes disponibles para matricular.</p>';
    document.getElementById('emList').innerHTML = html;
    document.getElementById('enrollModal').style.display = 'flex';
}

function closeEnrollModal() {
    document.getElementById('enrollModal').style.display = 'none';
}

document.getElementById('enrollModal').addEventListener('click', function(e) {
    if (e.target === this) closeEnrollModal();
});

function filterEmAvail() {
    var q = document.getElementById('emSearch').value.toLowerCase();
    document.querySelectorAll('#emList .emItem').forEach(function(el) {
        el.style.display = (el.dataset.name || '').includes(q) ? '' : 'none';
    });
}

function emSelectAll() {
    document.querySelectorAll('#emList .emItem').forEach(function(el) {
        if (el.style.display !== 'none') el.querySelector('input[type="checkbox"]').checked = true;
    });
    emUpdateCounter();
}

function emUpdateCounter() {
    var n = document.querySelectorAll('#emList input[type="checkbox"]:checked').length;
    document.getElementById('emCounter').textContent = n + ' seleccionado' + (n !== 1 ? 's' : '');
    document.getElementById('emSubmit').disabled = n === 0;
}

function smConfirmUnenroll(studentId, name, courseId) {
    document.getElementById('smUnenrollStudentId').value = studentId;
    document.getElementById('smUnenrollCourseId').value  = courseId;
    document.getElementById('smUnenrollMsg').innerHTML   = '¿Retirar a <strong>' + name + '</strong> del curso?';
    document.getElementById('smUnenrollModal').style.display = 'flex';
}

function closeSmUnenroll() {
    document.getElementById('smUnenrollModal').style.display = 'none';
}

document.getElementById('smUnenrollModal').addEventListener('click', function(e) {
    if (e.target === this) closeSmUnenroll();
});
</script>

</body>
</html>