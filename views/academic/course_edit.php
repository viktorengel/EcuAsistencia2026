<?php
require_once BASE_PATH . '/helpers/Security.php';
Security::requireLogin();
if (!Security::hasRole('autoridad')) { die('Acceso denegado'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Curso - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 700px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card-header { background: #007bff; color: white; padding: 15px 20px; border-radius: 8px 8px 0 0; margin: -30px -30px 25px -30px; }
        .card-header h4 { margin: 0; font-size: 18px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .d-flex { display: flex; justify-content: space-between; margin-top: 20px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>✏️ Editar Curso: <?= htmlspecialchars($course['name']) ?></h4>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">

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

                <div class="form-group">
                    <label>Grado / Año</label>
                    <select name="grade_level" id="grade_level" required onchange="onGradeChange()">
                        <option value="">Seleccione nivel primero...</option>
                    </select>
                </div>

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

                <div class="form-group" id="group_carrera" style="display:none;">
                    <label>Especialidad / Carrera <span style="color:#999; font-weight:normal;">(opcional)</span></label>
                    <select name="carrera" id="carrera" onchange="generateCourseName()">
                        <option value="">Sin especificar</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Paralelo</label>
                    <select name="parallel" id="parallel" required onchange="generateCourseName()">
                        <option value="">Seleccionar...</option>
                        <?php foreach(range('A', 'J') as $letter): ?>
                            <option value="<?= $letter ?>" <?= $course['parallel'] == $letter ? 'selected' : '' ?>>
                                <?= $letter ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jornada</label>
                    <select name="shift_id" id="shift_id" required onchange="generateCourseName()">
                        <option value="">Seleccionar...</option>
                        <?php foreach($shifts as $shift): ?>
                            <option value="<?= $shift['id'] ?>" 
                                    data-shift="<?= $shift['name'] ?>"
                                    <?= $course['shift_id'] == $shift['id'] ? 'selected' : '' ?>>
                                <?= ucfirst($shift['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nombre del Curso <span style="color:#999; font-weight:normal;">(generado automáticamente)</span></label>
                    <input type="text" name="name" id="course_name" readonly 
                           style="background: #f0f0f0; font-weight:bold;"
                           value="<?= htmlspecialchars($course['name']) ?>">
                </div>

                <div class="d-flex">
                    <a href="?action=academic" class="btn btn-secondary">← Cancelar</a>
                    <button type="submit" class="btn btn-success">✓ Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Datos del curso actual para precargar
    const currentGradeLevel = <?= json_encode($course['grade_level']) ?>;
    const currentShiftId    = <?= json_encode((string)$course['shift_id']) ?>;

    const gradeLevels = {
        inicial: ['Inicial 1 (0-3 años)', 'Inicial 2 (3-5 años)'],
        egb: ['1.º EGB - Preparatoria','2.º EGB','3.º EGB','4.º EGB','5.º EGB','6.º EGB','7.º EGB','8.º EGB','9.º EGB','10.º EGB'],
        bgu: ['1.º BGU','2.º BGU','3.º BGU'],
        bt:  ['1.º BT','2.º BT','3.º BT']
    };

    const egbNocturnaAllowed = ['8.º EGB','9.º EGB','10.º EGB'];

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

    // Detectar tipo educativo desde el grade_level actual
    function detectType(grade) {
        if (!grade) return '';
        if (grade.includes('Inicial')) return 'inicial';
        if (grade.includes('EGB'))    return 'egb';
        if (grade.includes('BGU'))    return 'bgu';
        if (grade.includes('BT'))     return 'bt';
        return '';
    }

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
        let visible = (type === 'bgu' || type === 'bt' || (type === 'egb' && egbNocturnaAllowed.includes(grade)));
        nocturna.style.display = visible ? '' : 'none';
        if (!visible && nocturna.selected) {
            document.getElementById('shift_id').value = '';
        }
    }

    function updateGradeLevels(preselectGrade) {
        const type = document.getElementById('education_type').value;
        const gradeSelect = document.getElementById('grade_level');
        gradeSelect.innerHTML = '<option value="">Seleccionar grado...</option>';

        if (!type) return;

        (gradeLevels[type] || []).forEach(function(g) {
            const opt = document.createElement('option');
            opt.value = g;
            opt.textContent = g;
            if (preselectGrade && g === preselectGrade) opt.selected = true;
            gradeSelect.appendChild(opt);
        });

        const isBT = (type === 'bt');
        document.getElementById('group_specialty').style.display = isBT ? 'block' : 'none';
        document.getElementById('group_carrera').style.display   = isBT ? 'block' : 'none';
        document.getElementById('specialty').required = isBT;

        if (!isBT) {
            document.getElementById('specialty').value = '';
            document.getElementById('carrera').innerHTML = '<option value="">Sin especificar</option>';
        }

        updateNocturnaVisibility(type, gradeSelect.value);
    }

    function onGradeChange() {
        const type  = document.getElementById('education_type').value;
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
                opt.value = c; opt.textContent = c;
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
        const type      = document.getElementById('education_type').value;
        const specialty = document.getElementById('specialty').value;
        const carrera   = document.getElementById('carrera').value;

        if (!grade || !parallel || !shiftName) return;

        let name = grade + ' "' + parallel + '"';
        if (type === 'bt' && specialty) {
            name += ' - ' + specialty;
            if (carrera) name += ' (' + carrera + ')';
        }
        name += ' - ' + shiftName.charAt(0).toUpperCase() + shiftName.slice(1);
        document.getElementById('course_name').value = name;
    }

    // Extraer figura profesional y carrera desde el nombre del curso
    // Formato: "1.º BT "A" - Informática (Soporte Técnico) - Matutina"
    function parseSpecialtyFromName(courseName) {
        const result = { specialty: '', carrera: '' };
        // Buscar patrón: - Figura (Carrera) - o - Figura -
        const match = courseName.match(/BT "[A-Z]" - ([^(]+?)(?:\s*\(([^)]+)\))?\s*-\s*\w+$/);
        if (match) {
            result.specialty = match[1].trim();
            result.carrera   = match[2] ? match[2].trim() : '';
        }
        return result;
    }

    // PRECARGAR datos del curso al cargar la página
    window.addEventListener('DOMContentLoaded', function() {
        const detectedType = detectType(currentGradeLevel);
        if (detectedType) {
            document.getElementById('education_type').value = detectedType;
            updateGradeLevels(currentGradeLevel);
        }

        // Preseleccionar jornada
        const shiftSelect = document.getElementById('shift_id');
        for (let i = 0; i < shiftSelect.options.length; i++) {
            if (shiftSelect.options[i].value === currentShiftId) {
                shiftSelect.selectedIndex = i;
                break;
            }
        }

        // Precargar figura profesional y carrera (solo BT)
        if (detectedType === 'bt') {
            const parsed = parseSpecialtyFromName(currentGradeLevel + ' - ' + <?= json_encode($course['name']) ?>);

            // Intentar parsear directo del nombre completo del curso
            const fullName = <?= json_encode($course['name']) ?>;
            const matchFull = fullName.match(/- ([^(]+?)(?:\s*\(([^)]+)\))?\s*-\s*\w+$/);
            if (matchFull) {
                const fig = matchFull[1].trim();
                const car = matchFull[2] ? matchFull[2].trim() : '';

                // Seleccionar figura profesional
                const specialtySelect = document.getElementById('specialty');
                for (let i = 0; i < specialtySelect.options.length; i++) {
                    if (specialtySelect.options[i].value === fig) {
                        specialtySelect.selectedIndex = i;
                        break;
                    }
                }

                // Cargar carreras de esa figura y preseleccionar
                if (fig && carreras[fig]) {
                    const carreraSelect = document.getElementById('carrera');
                    carreraSelect.innerHTML = '<option value="">Sin especificar</option>';
                    carreras[fig].forEach(function(c) {
                        const opt = document.createElement('option');
                        opt.value = c; opt.textContent = c;
                        if (c === car) opt.selected = true;
                        carreraSelect.appendChild(opt);
                    });
                }
            }
        }

        updateNocturnaVisibility(detectedType, currentGradeLevel);
    });
    </script>
</body>
</html>