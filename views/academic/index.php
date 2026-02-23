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

        /* ── Modal base ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.55); z-index: 9999;
            align-items: center; justify-content: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: #fff; border-radius: 12px;
            width: 90%; max-width: 540px; max-height: 90vh;
            display: flex; flex-direction: column;
            box-shadow: 0 10px 40px rgba(0,0,0,.25);
            animation: modalIn .18s ease;
        }
        @keyframes modalIn { from { transform: translateY(-18px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid #f0f0f0;
            display: flex; justify-content: space-between; align-items: center;
            flex-shrink: 0;
        }
        .modal-header h3 { margin: 0; font-size: 17px; color: #1a237e; }
        .modal-header p  { margin: 4px 0 0; font-size: 12px; color: #888; }
        .modal-close { background: none; border: none; font-size: 22px; cursor: pointer; color: #aaa; margin: 0; padding: 0; line-height: 1; }
        .modal-close:hover { color: #333; background: none; }
        .modal-body { padding: 20px 24px; overflow-y: auto; flex: 1; }
        .modal-footer {
            padding: 14px 24px; border-top: 1px solid #f0f0f0;
            display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0;
        }
        .modal-footer .btn-cancel {
            padding: 9px 20px; background: #f5f5f5; color: #555;
            border: 1px solid #ddd; border-radius: 6px; cursor: pointer; margin: 0; font-size: 14px;
        }
        .modal-footer .btn-cancel:hover { background: #e8e8e8; }
        .modal-footer .btn-submit {
            padding: 9px 22px; background: linear-gradient(135deg,#1a237e,#283593);
            color: white; border: none; border-radius: 6px; cursor: pointer;
            margin: 0; font-size: 14px; font-weight: 600;
        }
        .modal-footer .btn-submit:hover { opacity: .88; }

        /* ── Form fields inside modal ── */
        .mf-label { font-size: 12px; font-weight: 600; color: #555; margin-bottom: 5px; display: block; }
        .mf-input {
            width: 100%; padding: 9px 12px;
            border: 1.5px solid #e0e0e0; border-radius: 7px;
            font-size: 13px; background: #fafafa; color: #333;
            outline: none; transition: border-color .2s;
        }
        .mf-input:focus { border-color: #1a237e; }
        .mf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
        .mf-group { margin-bottom: 14px; }

        /* ── Tabla Cursos Registrados (mismo estilo que Años Lectivos) ── */
        .section-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .section-header h2 { margin: 0; }
        .btn-add {
            padding: 9px 18px; background: linear-gradient(135deg,#1a237e,#283593);
            color: white; border: none; border-radius: 6px; cursor: pointer;
            font-size: 13px; font-weight: 600; margin: 0; transition: opacity .2s;
        }
        .btn-add:hover { opacity: .88; background: linear-gradient(135deg,#1a237e,#283593); }

        .badge-active   { background: #28a745; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-inactive { background: #6c757d; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; }
        .badge-shift    { background: #e3f2fd; color: #1565c0; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }

        .action-btn {
            padding: 5px 10px; font-size: 12px;
            border: none; border-radius: 4px; cursor: pointer; margin: 0;
        }
        .action-btn:hover { opacity: .85; }

        /* expandible estudiantes */
        .students-panel { background: #f8fbff; border-bottom: 2px solid #1565c0; }
        .students-panel td { padding: 0; }
        .sp-inner { padding: 16px 20px; }
        .sp-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
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

    <!-- ── Mensajes ── -->
    <?php if(isset($_GET['course_success'])): ?>
        <div class="success">✓ Curso creado correctamente.
            <?php if(isset($_GET['subjects_loaded']) && (int)$_GET['subjects_loaded'] > 0): ?>
                Se pre-cargaron <strong><?= (int)$_GET['subjects_loaded'] ?> asignaturas</strong> según la malla curricular.
            <?php elseif(isset($_GET['subjects_loaded'])): ?>
                Las asignaturas de este nivel ya estaban registradas.
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_GET['course_updated'])): ?><div class="success">✓ Curso actualizado correctamente</div><?php endif; ?>
    <?php if(isset($_GET['course_deleted'])): ?><div class="success">✓ Curso eliminado correctamente</div><?php endif; ?>
    <?php if(isset($_GET['subject_success'])): ?><div class="success">✓ Asignatura creada correctamente</div><?php endif; ?>
    <?php if(isset($_GET['subject_updated'])): ?><div class="success">✓ Asignatura actualizada correctamente</div><?php endif; ?>
    <?php if(isset($_GET['subject_deleted'])): ?><div class="success">✓ Asignatura eliminada correctamente</div><?php endif; ?>
    <?php if(isset($_GET['sy_created'])): ?><div class="success">✓ Año lectivo creado correctamente</div><?php endif; ?>
    <?php if(isset($_GET['sy_updated'])): ?><div class="success">✓ Año lectivo actualizado correctamente</div><?php endif; ?>
    <?php if(isset($_GET['sy_deleted'])): ?><div class="success">✓ Año lectivo eliminado correctamente</div><?php endif; ?>
    <?php if(isset($_GET['sy_activated'])): ?><div class="success">✓ Año lectivo activado correctamente</div><?php endif; ?>
    <?php if(isset($_GET['sy_deactivated'])): ?><div class="success">✓ Año lectivo desactivado correctamente</div><?php endif; ?>

    <?php if(isset($_GET['rep_assigned'])): ?><div class="success">&#10003; Representante asignado correctamente</div><?php endif; ?>
    <?php if(isset($_GET['rep_removed'])): ?><div class="success">&#10003; Representante eliminado correctamente</div><?php endif; ?>
    <?php if(isset($_GET['rep_error'])): ?><div class="error">&#10007; <?= htmlspecialchars($_GET['rep_error']) ?></div><?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <?php $errMap = [
            'no_active_year'       => 'No hay un año lectivo activo',
            'course_not_found'     => 'Curso no encontrado',
            'course_has_students'  => 'No se puede eliminar el curso porque tiene estudiantes matriculados',
            'course_has_assignments'=> 'No se puede eliminar el curso porque tiene asignaciones docentes',
            'course_duplicate'     => 'Ya existe un curso con ese nivel, paralelo y jornada en el año lectivo activo. Elige un paralelo diferente.',
            'subject_not_found'    => 'Asignatura no encontrada',
            'subject_has_assignments'=> 'No se puede eliminar la asignatura porque tiene asignaciones docentes',
            'cannot_delete_active' => 'No se puede eliminar el año lectivo activo',
            'has_courses'          => 'No se puede eliminar el año lectivo porque tiene cursos asociados',
            'year_not_found'       => 'Año lectivo no encontrado',
        ]; ?>
        <?php if(isset($errMap[$_GET['error']])): ?>
            <div class="error">✗ <?= $errMap[$_GET['error']] ?></div>
        <?php endif; ?>
    <?php endif; ?>


    <!-- ══════════════════════════════════════
         AÑOS LECTIVOS
    ══════════════════════════════════════ -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="section-header">
            <h2>📅 Años Lectivos</h2>
            <button class="btn-add" onclick="openSYModal()">+ Crear Año Lectivo</button>
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
                    <tr><td colspan="6" style="text-align:center; color: #999;">No hay años lectivos registrados</td></tr>
                <?php else: ?>
                    <?php $counter = 1; foreach($schoolYears as $year): ?>
                    <tr style="<?= $year['is_active'] ? 'background: #e7f3ff;' : '' ?>">
                        <td><?= $counter++ ?></td>
                        <td><strong><?= htmlspecialchars($year['name']) ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($year['start_date'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($year['end_date'])) ?></td>
                        <td>
                            <?php if($year['is_active']): ?>
                                <span class="badge-active">✓ ACTIVO</span>
                            <?php else: ?>
                                <span class="badge-inactive">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td style="white-space: nowrap;">
                            <button class="action-btn" style="background:#ffc107;color:#212529;"
                                    onclick="openEditSYModal(<?= $year['id'] ?>, '<?= htmlspecialchars(addslashes($year['name'])) ?>', '<?= $year['start_date'] ?>', '<?= $year['end_date'] ?>', <?= $year['is_active'] ? 'true' : 'false' ?>)">
                                ✏️ Editar
                            </button>

                            <?php if($year['is_active']): ?>
                                <form method="POST" action="?action=deactivate_school_year" style="display:inline;"
                                      onsubmit="return confirmSY(event,'desactivar','<?= htmlspecialchars(addslashes($year['name'])) ?>')">
                                    <input type="hidden" name="year_id" value="<?= $year['id'] ?>">
                                    <button type="submit" class="action-btn" style="background:#6c757d;color:white;">⊘ Desactivar</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="?action=activate_school_year" style="display:inline;"
                                      onsubmit="return confirmSY(event,'activar','<?= htmlspecialchars(addslashes($year['name'])) ?>')">
                                    <input type="hidden" name="year_id" value="<?= $year['id'] ?>">
                                    <button type="submit" class="action-btn" style="background:#28a745;color:white;">✓ Activar</button>
                                </form>
                                <form method="POST" action="?action=delete_school_year" style="display:inline;"
                                      onsubmit="return confirmDelete(event, '<?= htmlspecialchars($year['name']) ?>')">
                                    <input type="hidden" name="year_id" value="<?= $year['id'] ?>">
                                    <button type="submit" class="action-btn" style="background:#dc3545;color:white;">🗑️ Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <!-- ══════════════════════════════════════
         CURSOS REGISTRADOS
    ══════════════════════════════════════ -->
    <div class="card">
        <div class="section-header">
            <h2>🏫 Cursos Registrados</h2>
            <button class="btn-add" onclick="openCourseModal()">+ Crear Nuevo Curso</button>
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
                    <td><span class="badge-shift"><?= ucfirst($course['shift_name']) ?></span></td>
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
                                    style="padding:2px 7px;font-size:14px;background:none;border:none;cursor:pointer;color:#f57c00;vertical-align:middle;margin:0;">
                                🔄
                            </button>
                            <button class="btn-tutor-remove"
                                    data-course-id="<?= $course['id'] ?>"
                                    data-course-name="<?= htmlspecialchars($course['name'], ENT_QUOTES) ?>"
                                    title="Quitar tutor"
                                    style="padding:2px 7px;font-size:14px;background:none;border:none;cursor:pointer;color:#dc3545;vertical-align:middle;font-weight:bold;margin:0;">
                                ✕
                            </button>
                        <?php else: ?>
                            <button class="btn-tutor-assign"
                                    data-course-id="<?= $course['id'] ?>"
                                    data-course-name="<?= htmlspecialchars($course['name'], ENT_QUOTES) ?>"
                                    data-has-tutor="0"
                                    style="padding:3px 10px;font-size:12px;background:#17a2b8;color:white;border:none;border-radius:4px;cursor:pointer;margin:0;">
                                👤 Asignar Tutor
                            </button>
                        <?php endif; ?>
                    </td>
                    <td style="white-space:nowrap;">
                        <button class="action-btn" style="background:#6f42c1;color:white;"
                                id="btnSub_<?= $course['id'] ?>"
                                onclick="toggleSubjectsRow(<?= $course['id'] ?>)">
                            📚 Asignaturas
                        </button>
                        <button class="action-btn" style="background:#007bff;color:white;"
                                id="btnEst_<?= $course['id'] ?>"
                                onclick="toggleStudentsRow(<?= $course['id'] ?>)">
                            👥 Estudiantes
                        </button>
                        <button class="action-btn" style="background:#ffc107;color:#000;"
                                onclick="openEditCourseModal(<?= $course['id'] ?>, '<?= htmlspecialchars(addslashes($course['name'])) ?>', '<?= htmlspecialchars(addslashes($course['grade_level'])) ?>', '<?= $course['parallel'] ?>', <?= $course['shift_id'] ?>)">
                            ✏️ Editar
                        </button>
                        <form method="POST" action="?action=delete_course" style="display:inline;"
                              onsubmit="return confirmDeleteCourse(event, '<?= htmlspecialchars(addslashes($course['name'])) ?>')">
                            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                            <button type="submit" class="action-btn" style="background:#dc3545;color:white;">🗑️ Eliminar</button>
                        </form>
                    </td>
                </tr>
                <!-- Fila expandible estudiantes -->
                <tr id="rowEst_<?= $course['id'] ?>" class="students-panel" style="display:none;">
                    <td colspan="5">
                        <div class="sp-inner">
                            <div class="sp-header">
                                <span style="font-weight:700;font-size:14px;color:#1a237e;">
                                    👥 <?= htmlspecialchars($course['name']) ?>
                                    &nbsp;<span style="background:#e3f2fd;color:#1565c0;padding:2px 10px;border-radius:10px;font-size:12px;font-weight:700;">
                                        <?= count($enrollmentsByCourse[$course['id']] ?? []) ?> matriculados
                                    </span>
                                </span>
                                <button onclick="openEnrollModal(<?= $course['id'] ?>, '<?= htmlspecialchars(addslashes($course['name']), ENT_QUOTES) ?>')"
                                        style="padding:6px 14px;font-size:12px;background:#28a745;color:#fff;border:none;border-radius:5px;cursor:pointer;font-weight:600;margin:0;">
                                    ➕ Matricular nuevo
                                </button>
                            </div>
                            <?php if(!empty($enrollmentsByCourse[$course['id']])): ?>
                            <input type="text" placeholder="🔍 Buscar estudiante..." oninput="filterInlineEnrolled(this, <?= $course['id'] ?>)"
                                style="width:100%;max-width:320px;padding:7px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;margin-bottom:10px;">
                            <table id="tblEst_<?= $course['id'] ?>" style="width:100%;border-collapse:collapse;font-size:13px;table-layout:fixed;">
                                <colgroup>
                                    <col style="width:32px;">
                                    <col style="width:110px;">
                                    <col style="width:auto;">
                                    <col style="width:220px;">
                                </colgroup>
                                <thead>
                                    <tr style="background:#e8eaf6;">
                                        <th style="padding:7px 10px;text-align:left;">#</th>
                                        <th style="padding:7px 10px;text-align:left;">Cédula</th>
                                        <th style="padding:7px 10px;text-align:left;">Apellidos y Nombres</th>
                                        <th style="padding:7px 10px;text-align:center;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $ni=1; foreach($enrollmentsByCourse[$course['id']] as $est): ?>
                                    <tr data-course="<?= $course['id'] ?>" data-name="<?= strtolower($est['last_name'].' '.$est['first_name']) ?>" style="border-bottom:1px solid #e8eaf6;">
                                        <td style="padding:7px 10px;"><?= $ni++ ?></td>
                                        <td style="padding:7px 10px;"><?= $est['dni'] ?? '-' ?></td>
                                        <td style="padding:7px 10px;"><strong><?= htmlspecialchars($est['last_name'].' '.$est['first_name']) ?></strong></td>
                                        <td style="padding:7px 10px;text-align:center;white-space:nowrap;">
                                            <button onclick="openRepModal(<?= $est['id'] ?>, '<?= htmlspecialchars($est['last_name'].' '.$est['first_name'], ENT_QUOTES) ?>', <?= $course['id'] ?>)"
                                                    style="padding:3px 9px;font-size:12px;background:#17a2b8;color:#fff;border:none;border-radius:4px;cursor:pointer;margin:0;">
                                                👪 Representantes
                                            </button>
                                            <button onclick="smConfirmUnenroll(<?= $est['id'] ?>, '<?= addslashes($est['last_name'].' '.$est['first_name']) ?>', <?= $course['id'] ?>)"
                                                    style="padding:3px 9px;font-size:12px;background:#dc3545;color:#fff;border:none;border-radius:4px;cursor:pointer;margin:0;">
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
                    <!-- Fila expandible ASIGNATURAS -->
                    <tr id="rowSub_<?= $course['id'] ?>" style="display:none;background:#fffde7;">
                        <td colspan="5" style="padding:0;border-bottom:2px solid #6f42c1;">
                            <div class="sp-inner">
                                <div class="sp-header">
                                    <span style="font-weight:700;font-size:14px;color:#4a148c;">
                                        📚 <?= htmlspecialchars($course['name']) ?>
                                        &nbsp;<span style="background:#ede7f6;color:#4a148c;padding:2px 10px;border-radius:10px;font-size:12px;font-weight:700;">
                                            <?= count($subjectsByCourse[$course['id']] ?? []) ?> asignatura(s)
                                        </span>
                                    </span>
                                    <button onclick="openAddSubjectModal(<?= $course['id'] ?>, '<?= htmlspecialchars(addslashes($course['name']), ENT_QUOTES) ?>')"
                                            style="padding:6px 14px;font-size:12px;background:#6f42c1;color:#fff;border:none;border-radius:5px;cursor:pointer;font-weight:600;margin:0;">
                                        ➕ Agregar asignatura
                                    </button>
                                </div>
                                <?php if(!empty($subjectsByCourse[$course['id']])): ?>
                                <input type="text" placeholder="🔍 Buscar asignatura..."
                                    oninput="filterSubjects(this, <?= $course['id'] ?>)"
                                    style="width:100%;max-width:320px;padding:7px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;margin-bottom:10px;">
                                <table id="tblSub_<?= $course['id'] ?>" style="width:100%;border-collapse:collapse;font-size:13px;">
                                    <thead>
                                        <tr style="background:#ede7f6;">
                                            <th style="padding:7px 10px;text-align:left;width:36px;">#</th>
                                            <th style="padding:7px 10px;text-align:left;">Asignatura</th>
                                            <th style="padding:7px 10px;text-align:left;">Código</th>
                                            <th style="padding:7px 10px;text-align:center;width:80px;">Hrs/sem</th>
                                            <th style="padding:7px 10px;text-align:left;">Docente</th>
                                            <th style="padding:7px 10px;text-align:center;width:130px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $si=1; foreach($subjectsByCourse[$course['id']] as $sub): ?>
                                    <tr data-subname="<?= strtolower($sub['name']) ?>" style="border-bottom:1px solid #ede7f6;">
                                        <td style="padding:7px 10px;"><?= $si++ ?></td>
                                        <td style="padding:7px 10px;"><strong><?= htmlspecialchars($sub['name']) ?></strong></td>
                                        <td style="padding:7px 10px;"><?= $sub['code'] ? htmlspecialchars($sub['code']) : '<span style="color:#bbb;">&#x2014;</span>' ?></td>
                                        <td style="padding:7px 10px;text-align:center;">
                                            <form method="POST" action="?action=set_subject_hours&course_id=<?= $course['id'] ?>" style="display:inline-flex;align-items:center;gap:4px;">
                                                <input type="hidden" name="subject_id" value="<?= $sub['id'] ?>">
                                                <input type="number" name="hours_per_week" value="<?= (int)($sub['hours_per_week'] ?? 1) ?>"
                                                       min="1" max="20" style="width:50px;padding:3px 5px;border:1px solid #ddd;border-radius:4px;font-size:12px;text-align:center;">
                                                <button type="submit" style="padding:3px 7px;background:#007bff;color:white;border:none;border-radius:4px;font-size:11px;cursor:pointer;margin:0;">&#10003;</button>
                                            </form>
                                        </td>
                                        <td style="padding:7px 10px;">
                                            <?php if(!empty($sub['teacher_name'])): ?>
                                                <span style="color:#2e7d32;font-size:13px;">&#128100; <?= htmlspecialchars($sub['teacher_name']) ?></span>
                                            <?php else: ?>
                                                <span style="color:#bbb;font-style:italic;font-size:12px;">Sin docente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:7px 10px;text-align:center;white-space:nowrap;">
                                            <button onclick="abrirModalDocente(<?= $sub['id'] ?>, '<?= htmlspecialchars(addslashes($sub['name'])) ?>', <?= $sub['assignment_id'] ?? 'null' ?>, <?= $sub['teacher_id'] ?? 'null' ?>, <?= $course['id'] ?>)"
                                                    style="padding:3px 8px;font-size:12px;background:#e65100;color:white;border:none;border-radius:4px;cursor:pointer;margin:0;"
                                                    title="<?= empty($sub['teacher_name']) ? 'Asignar docente' : 'Cambiar docente' ?>">
                                                <?= empty($sub['teacher_name']) ? '&#128100;' : '&#128260;&#128100;' ?>
                                            </button>
                                            <?php if(!empty($sub['teacher_name'])): ?>
                                            <form method="POST" action="?action=unassign_subject_teacher" style="display:inline;">
                                                <input type="hidden" name="assignment_id" value="<?= $sub['assignment_id'] ?>">
                                                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                                <button type="submit"
                                                        onclick="return confirm('Quitar docente de esta asignatura?')"
                                                        style="padding:3px 8px;font-size:12px;background:#6c757d;color:white;border:none;border-radius:4px;cursor:pointer;margin:0;">&#10006;</button>
                                            </form>
                                            <?php endif; ?>
                                            <form method="POST" action="?action=remove_course_subject" style="display:inline;"
                                                  onsubmit="return confirmRemoveSubject(event, '<?= htmlspecialchars(addslashes($sub['name'])) ?>')">
                                                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                                <input type="hidden" name="subject_id" value="<?= $sub['id'] ?>">
                                                <button type="submit" style="padding:3px 8px;font-size:12px;background:#dc3545;color:white;border:none;border-radius:4px;cursor:pointer;margin:0;">&#128465;</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <div style="text-align:center;padding:20px;color:#aaa;font-size:13px;">&#128235; Este curso aun no tiene asignaturas asignadas.</div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div><!-- /container -->


<!-- ══════════════════════════════════════════════════════
     MODAL: CREAR AÑO LECTIVO
══════════════════════════════════════════════════════ -->
<div id="modalSY" class="modal-overlay" onclick="if(event.target===this)closeSYModal()">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3>📅 Crear Año Lectivo</h3>
                <p>Define el período académico</p>
            </div>
            <button class="modal-close" onclick="closeSYModal()">✕</button>
        </div>
        <form method="POST" action="?action=create_school_year" id="formSY">
            <div class="modal-body">
                <div class="mf-group">
                    <label class="mf-label">Nombre del Año Lectivo *</label>
                    <input type="text" name="name" class="mf-input" required placeholder="Ej: 2025-2026">
                    <small style="color:#aaa;font-size:11px;">Formato recomendado: AAAA-AAAA</small>
                </div>
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Fecha de Inicio *</label>
                        <input type="date" name="start_date" class="mf-input" required>
                    </div>
                    <div>
                        <label class="mf-label">Fecha de Fin *</label>
                        <input type="date" name="end_date" class="mf-input" required>
                    </div>
                </div>
                <div style="background:#f8f9fa;border-radius:8px;padding:12px 14px;">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:normal;margin:0;">
                        <input type="checkbox" name="is_active" value="1"
                               style="width:17px;height:17px;accent-color:#28a745;flex-shrink:0;">
                        <span>
                            <strong style="font-size:13px;">Marcar como año lectivo activo</strong><br>
                            <small style="color:#888;font-size:11px;">Solo puede haber un año lectivo activo a la vez</small>
                        </span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeSYModal()">Cancelar</button>
                <button type="submit" class="btn-submit">💾 Crear Año Lectivo</button>
            </div>
        </form>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════
     MODAL: CREAR NUEVO CURSO
══════════════════════════════════════════════════════ -->
<div id="modalCourse" class="modal-overlay" onclick="if(event.target===this)closeCourseModal()">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <div>
                <h3>🏫 Crear Nuevo Curso</h3>
                <p>Las asignaturas se cargan automáticamente según el nivel</p>
            </div>
            <button class="modal-close" onclick="closeCourseModal()">✕</button>
        </div>
        <form method="POST" action="?action=create_course" id="courseForm">
            <div class="modal-body">
                <?php
                $cf = $_SESSION['course_form'] ?? [];
                unset($_SESSION['course_form']);
                ?>
                <script type="application/json" id="_dataCF"><?= json_encode(["education_type"=>$cf["education_type"]??"","grade_level"=>$cf["grade_level"]??"","specialty"=>$cf["specialty"]??"","carrera"=>$cf["carrera"]??""], JSON_HEX_TAG|JSON_HEX_AMP) ?></script>

                <!-- Nivel + Grado -->
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Nivel Educativo *</label>
                        <select name="education_type" id="education_type" required onchange="updateGradeLevels()" class="mf-input">
                            <option value="">Seleccionar...</option>
                            <option value="inicial" <?= ($cf['education_type']??'')==='inicial'?'selected':'' ?>>🧒 Educación Inicial</option>
                            <option value="egb"     <?= ($cf['education_type']??'')==='egb'    ?'selected':'' ?>>📘 EGB</option>
                            <option value="bgu"     <?= ($cf['education_type']??'')==='bgu'    ?'selected':'' ?>>🎓 BGU</option>
                            <option value="bt"      <?= ($cf['education_type']??'')==='bt'     ?'selected':'' ?>>🛠 Bachillerato Técnico</option>
                        </select>
                    </div>
                    <div id="group_grade">
                        <label class="mf-label">Grado / Año *</label>
                        <select name="grade_level" id="grade_level" required onchange="onGradeChange()" class="mf-input">
                            <option value="">Seleccione nivel primero...</option>
                            <?php if(!empty($cf['grade_level'])): ?>
                            <option value="<?= htmlspecialchars($cf['grade_level']) ?>" selected>
                                <?= htmlspecialchars($cf['grade_level']) ?>
                            </option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- BT: Figura + Carrera -->
                <div id="group_specialty" style="display:none;" class="mf-group">
                    <label class="mf-label">Figura Profesional</label>
                    <select name="specialty" id="specialty" onchange="updateCarreras()" class="mf-input">
                        <option value="">Seleccionar figura...</option>
                        <?php foreach(['Informática','Administración','Contabilidad','Comercialización y Ventas','Servicios Hoteleros','Turismo','Electromecánica Automotriz','Instalaciones Eléctricas','Electrónica de Consumo','Mecanizado y Construcciones Metálicas','Producción Agropecuaria','Acuicultura','Industrialización de Alimentos','Confección Textil','Música','Artes Plásticas','Diseño Gráfico','Servicios de Belleza','Atención Integral en Salud','Mecatrónica','Refrigeración y Climatización','Construcción','Redes y Telecomunicaciones','Seguridad Industrial','Logística y Transporte'] as $fig): ?>
                            <option value="<?= $fig ?>"><?= $fig ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="group_carrera" style="display:none;" class="mf-group">
                    <label class="mf-label">Especialidad / Carrera <span style="color:#aaa;font-weight:400;">(opcional)</span></label>
                    <select name="carrera" id="carrera" onchange="generateCourseName()" class="mf-input">
                        <option value="">Sin especificar</option>
                    </select>
                </div>

                <!-- Paralelo + Jornada -->
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Paralelo *</label>
                        <select name="parallel" id="parallel" required onchange="generateCourseName()" class="mf-input">
                            <option value="">Seleccionar...</option>
                            <?php foreach(range('A', 'J') as $letter): ?>
                                <option value="<?= $letter ?>" <?= ($cf['parallel']??'')===$letter?'selected':'' ?>><?= $letter ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mf-label">Jornada *</label>
                        <select name="shift_id" id="shift_id" required onchange="generateCourseName()" class="mf-input">
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
                <div class="mf-group" style="margin-bottom:0;">
                    <label class="mf-label">
                        Nombre del Curso
                        <span style="font-weight:400;color:#aaa;margin-left:4px;">← generado automáticamente</span>
                    </label>
                    <div style="position:relative;">
                        <input type="text" name="name" id="course_name" readonly
                            value="<?= htmlspecialchars($cf['name'] ?? '') ?>"
                            class="mf-input"
                            style="padding-left:34px;font-weight:600;color:#1a237e;background:#eef2ff;">
                        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:14px;">✏️</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeCourseModal()">Cancelar</button>
                <button type="submit" class="btn-submit">🏫 Crear Curso</button>
            </div>
        </form>
    </div>
</div>


<!-- ══ Modal Asignar/Cambiar Tutor ══ -->
<div id="tutorModal" class="modal-overlay">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div>
                <h3>👤 <span id="tutorModalTitle">Asignar Tutor</span></h3>
                <p>Curso: <strong id="tutorModalCourse"></strong></p>
            </div>
            <button class="modal-close" onclick="closeTutorModal()">✕</button>
        </div>
        <form method="POST" action="?action=set_tutor" id="tutorForm">
            <div class="modal-body">
                <div id="tutorLoadingMsg" style="color:#999;font-size:13px;margin-bottom:12px;display:none;">⏳ Cargando docentes...</div>
                <div id="tutorNoTeachers" style="display:none;background:#fff3cd;color:#856404;padding:10px;border-radius:6px;font-size:13px;margin-bottom:12px;">
                    ⚠️ No hay docentes disponibles. Primero asigna docentes al curso en <strong>Asignaturas</strong>.
                </div>
                <input type="hidden" name="course_id" id="tutorCourseId">
                <div class="mf-group">
                    <label class="mf-label">Seleccionar Docente</label>
                    <select name="teacher_id" id="tutorTeacherSelect" class="mf-input">
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeTutorModal()">Cancelar</button>
                <button type="submit" id="tutorSubmitBtn" disabled class="btn-submit">✓ Asignar</button>
            </div>
        </form>
    </div>
</div>

<!-- ══ Modal Quitar Tutor ══ -->
<div id="removeTutorModal" class="modal-overlay">
    <div class="modal-box" style="max-width:400px;">
        <div class="modal-header">
            <h3 style="color:#dc3545;">✕ Quitar Tutor</h3>
            <button class="modal-close" onclick="closeRemoveModal()">✕</button>
        </div>
        <div class="modal-body">
            <p style="font-size:14px;color:#555;">¿Quitar el tutor del curso <strong id="removeTutorCourseName"></strong>?</p>
        </div>
        <form method="POST" action="?action=remove_tutor">
            <input type="hidden" name="course_id" id="removeTutorCourseId">
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeRemoveModal()">Cancelar</button>
                <button type="submit" style="padding:9px 22px;background:#dc3545;color:white;border:none;border-radius:6px;cursor:pointer;margin:0;font-size:14px;font-weight:600;">Sí, Quitar</button>
            </div>
        </form>
    </div>
</div>

<!-- ══ Modal Matricular ══ -->
<div id="enrollModal" class="modal-overlay" onclick="if(event.target===this)closeEnrollModal()">
    <div class="modal-box" style="max-width:460px;">
        <div class="modal-header">
            <div>
                <h3>➕ Matricular Estudiantes</h3>
                <p id="emCourseLabel"></p>
            </div>
            <button class="modal-close" onclick="closeEnrollModal()">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="?action=enroll_students" id="emForm">
                <input type="hidden" name="course_id" id="emCourseId">
                <input type="hidden" name="redirect_to" value="academic">
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;">
                    <input type="text" id="emSearch" placeholder="🔍 Buscar..." oninput="filterEmAvail()"
                        style="flex:1;padding:8px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;">
                    <button type="button" onclick="emSelectAll()"
                        style="padding:7px 13px;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;border-radius:6px;font-size:12px;cursor:pointer;white-space:nowrap;margin:0;">
                        Seleccionar todos
                    </button>
                </div>
                <div style="margin-bottom:8px;">
                    <span id="emCounter" style="font-size:12px;background:#f0f0f0;color:#555;padding:2px 10px;border-radius:10px;">0 seleccionados</span>
                </div>
                <div id="emList" style="max-height:260px;overflow-y:auto;padding-right:2px;"></div>
                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px;padding-top:12px;border-top:1px solid #f0f0f0;">
                    <button type="button" onclick="closeEnrollModal()"
                        style="padding:8px 18px;background:#f5f5f5;color:#555;border:1px solid #ddd;border-radius:6px;cursor:pointer;margin:0;">Cancelar</button>
                    <button type="submit" id="emSubmit" disabled
                        style="padding:8px 18px;background:#28a745;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;margin:0;">
                        ✓ Matricular seleccionados
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ Modal Confirmar Retirar ══ -->
<div id="smUnenrollModal" class="modal-overlay" onclick="if(event.target===this)closeSmUnenroll()">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <h3 style="color:#dc3545;">⚠️ Retirar Estudiante</h3>
            <button class="modal-close" onclick="closeSmUnenroll()">✕</button>
        </div>
        <div class="modal-body">
            <p id="smUnenrollMsg" style="color:#555;font-size:14px;margin-bottom:6px;"></p>
            <p style="color:#999;font-size:12px;">Los registros de asistencia se conservarán.</p>
        </div>
        <form method="POST" action="?action=unenroll_student" id="smUnenrollForm">
            <input type="hidden" name="student_id" id="smUnenrollStudentId">
            <input type="hidden" name="course_id"  id="smUnenrollCourseId">
            <input type="hidden" name="redirect_to" value="academic">
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeSmUnenroll()">Cancelar</button>
                <button type="submit" style="padding:9px 22px;background:#dc3545;color:white;border:none;border-radius:6px;cursor:pointer;margin:0;font-size:14px;font-weight:600;">Sí, Retirar</button>
            </div>
        </form>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════════════ -->

<script type="application/json" id="_dataAvailable"><?= json_encode(array_values($availableStudents), JSON_HEX_TAG|JSON_HEX_AMP) ?></script>
<script type="application/json" id="_dataReps"><?= json_encode(array_values($representatives ?? []), JSON_HEX_TAG|JSON_HEX_AMP) ?></script>
<script type="application/json" id="_dataRepsByStudent"><?= json_encode($repsByStudent ?? [], JSON_HEX_TAG|JSON_HEX_AMP) ?></script>
<script>
/* \u2500\u2500 Modales SY y Curso \u2500\u2500 */
function openSYModal()     { document.getElementById('modalSY').classList.add('active'); }
function closeSYModal()    { document.getElementById('modalSY').classList.remove('active'); document.getElementById('formSY').reset(); }
function openCourseModal() { document.getElementById('modalCourse').classList.add('active'); }
function closeCourseModal(){ document.getElementById('modalCourse').classList.remove('active'); }

/* Si hubo error de duplicado, reabrir modal de curso autom\u00e1ticamente */
<?php if(isset($_GET['error']) && in_array($_GET['error'], ['course_duplicate','no_active_year']) && !empty($cf)): ?>
document.addEventListener('DOMContentLoaded', function(){ openCourseModal(); });
<?php endif; ?>

/* \u2500\u2500 Grade levels (curso) \u2500\u2500 */
const gradeLevels = {
    inicial: ['Inicial 1 (0-3 a\u00f1os)','Inicial 2 (3-5 a\u00f1os)'],
    egb: ['1.\u00ba EGB - Preparatoria','2.\u00ba EGB','3.\u00ba EGB','4.\u00ba EGB','5.\u00ba EGB','6.\u00ba EGB','7.\u00ba EGB','8.\u00ba EGB','9.\u00ba EGB','10.\u00ba EGB'],
    bgu: ['1.\u00ba BGU','2.\u00ba BGU','3.\u00ba BGU'],
    bt:  ['1.\u00ba BT','2.\u00ba BT','3.\u00ba BT']
};
const egbNocturnaAllowed = ['8.\u00ba EGB','9.\u00ba EGB','10.\u00ba EGB'];
const carreras = {
    'Inform\u00e1tica': ['Aplicaciones Inform\u00e1ticas','Programaci\u00f3n de Software','Soporte T\u00e9cnico','Sistemas Microinform\u00e1ticos y Redes'],
    'Administraci\u00f3n': ['Asistencia Administrativa','Gesti\u00f3n Empresarial'],
    'Contabilidad': ['Contabilidad','Ventas e Informaci\u00f3n Comercial'],
    'Comercializaci\u00f3n y Ventas': ['Ventas e Informaci\u00f3n Comercial','Marketing'],
    'Servicios Hoteleros': ['Hoteler\u00eda','Hospitalidad'],
    'Turismo': ['Turismo','Gu\u00eda Tur\u00edstico'],
    'Electromec\u00e1nica Automotriz': ['Electromec\u00e1nica Automotriz','Mec\u00e1nica Automotriz'],
    'Instalaciones El\u00e9ctricas': ['Electricidad','Instalaciones El\u00e9ctricas'],
    'Electr\u00f3nica de Consumo': ['Electr\u00f3nica','Mantenimiento Electr\u00f3nico'],
    'Atenci\u00f3n Integral en Salud': ['Atenci\u00f3n en Enfermer\u00eda','Auxiliar de Salud'],
    'Producci\u00f3n Agropecuaria': ['Producci\u00f3n Agropecuaria','Agroindustria'],
    'Redes y Telecomunicaciones': ['Redes','Telecomunicaciones'],
    'Dise\u00f1o Gr\u00e1fico': ['Dise\u00f1o Gr\u00e1fico','Multimedia'],
    'Servicios de Belleza': ['Peluquer\u00eda','Cosmetolog\u00eda']
};

function getNocturnaOption() {
    const opts = document.getElementById('shift_id').options;
    for (let i = 0; i < opts.length; i++) {
        if ((opts[i].getAttribute('data-shift')||'').toLowerCase()==='nocturna') return opts[i];
    }
    return null;
}
function updateNocturnaVisibility(type, grade) {
    const n = getNocturnaOption(); if (!n) return;
    let visible = (type==='bgu'||type==='bt') || (type==='egb'&&egbNocturnaAllowed.includes(grade));
    n.style.display = visible ? '' : 'none';
    if (!visible && n.selected) { document.getElementById('shift_id').value=''; generateCourseName(); }
}
function updateGradeLevels() {
    const type = document.getElementById('education_type').value;
    const sel  = document.getElementById('grade_level');
    sel.innerHTML = '<option value="">Seleccionar grado...</option>';
    document.getElementById('course_name').value = '';
    if (!type) return;
    (gradeLevels[type]||[]).forEach(function(g){ const o=document.createElement('option'); o.value=o.textContent=g; sel.appendChild(o); });
    const isBT = type==='bt';
    document.getElementById('group_specialty').style.display = isBT ? 'block' : 'none';
    document.getElementById('group_carrera').style.display   = isBT ? 'block' : 'none';
    document.getElementById('specialty').required = isBT;
    if (!isBT) { document.getElementById('specialty').value=''; document.getElementById('carrera').innerHTML='<option value="">Sin especificar</option>'; }
    updateNocturnaVisibility(type,'');
    generateCourseName();
}
function onGradeChange() {
    updateNocturnaVisibility(document.getElementById('education_type').value, document.getElementById('grade_level').value);
    generateCourseName();
}
function updateCarreras() {
    const fig = document.getElementById('specialty').value;
    const sel = document.getElementById('carrera');
    sel.innerHTML = '<option value="">Sin especificar</option>';
    (carreras[fig]||[]).forEach(function(c){ const o=document.createElement('option'); o.value=o.textContent=c; sel.appendChild(o); });
    generateCourseName();
}
function generateCourseName() {
    const grade    = document.getElementById('grade_level').value;
    const parallel = document.getElementById('parallel').value;
    const shiftSel = document.getElementById('shift_id');
    const shiftOpt = shiftSel.options[shiftSel.selectedIndex];
    const shiftName = shiftOpt ? (shiftOpt.getAttribute('data-shift')||'') : '';
    const type     = document.getElementById('education_type').value;
    const specialty= document.getElementById('specialty').value;
    const carrera  = document.getElementById('carrera').value;
    if (!grade||!parallel||!shiftName) { document.getElementById('course_name').value=''; return; }
    let name = grade + ' "' + parallel + '"';
    if (type==='bt'&&specialty) { name += ' - '+specialty; if(carrera) name += ' ('+carrera+')'; }
    name += ' - '+shiftName.charAt(0).toUpperCase()+shiftName.slice(1);
    document.getElementById('course_name').value = name;
}

/* Restaurar form si volvi\u00f3 con error */
(function restoreForm() {
    var _cfData   = JSON.parse(document.getElementById('_dataCF').textContent);
    var savedType  = _cfData.education_type || '';
    var savedGrade = _cfData.grade_level    || '';
    var savedSpec  = _cfData.specialty      || '';
    if (!savedType) return;
    document.getElementById('education_type').value = savedType;
    updateGradeLevels();
    setTimeout(function() {
        if (savedGrade) { document.getElementById('grade_level').value=savedGrade; onGradeChange(); }
        if (savedType==='bt'&&savedSpec) {
            var s=document.getElementById('specialty'); if(s){s.value=savedSpec;updateCarreras();}
            setTimeout(function(){ var c=document.getElementById('carrera'); if(c) c.value=_cfData.carrera||''; generateCourseName(); },60);
        } else { generateCourseName(); }
    },60);
})();

/* \u2500\u2500 Confirmaciones \u2500\u2500 */
function confirmDeleteCourse(event, courseName) {
    event.preventDefault();
    showConfirmModal('\u26a0\ufe0f Eliminar Curso','#dc3545',
        '\u00bfEst\u00e1 seguro de eliminar el curso <strong>'+courseName+'</strong>?',
        '<p style="font-size:13px;background:#fff3cd;color:#856404;padding:10px;border-radius:4px;margin-top:8px;">\u26a0\ufe0f Se desvincular\u00e1 autom\u00e1ticamente a todos los estudiantes matriculados y docentes asignados.</p>',
        'S\u00ed, Eliminar', event.target);
    return false;
}
function confirmDelete(event, yearName) {
    event.preventDefault();
    showConfirmModal('\u26a0\ufe0f Eliminar A\u00f1o Lectivo','#dc3545',
        '\u00bfEst\u00e1 seguro de eliminar el a\u00f1o lectivo <strong>'+yearName+'</strong>?',
        '<p style="font-size:13px;color:#666;margin-top:8px;"><strong>Nota:</strong> No se puede eliminar si tiene cursos asociados.</p>',
        'S\u00ed, Eliminar', event.target);
    return false;
}
function confirmSY(event, accion, nombre) {
    event.preventDefault();
    var esActivar = accion==='activar';
    var color = esActivar ? '#28a745' : '#6c757d';
    var desc  = esActivar
        ? 'Se desactivar\u00e1 el a\u00f1o lectivo actual y se activar\u00e1 <strong>'+nombre+'</strong>.'
        : '\u00bfSeguro que deseas desactivar <strong>'+nombre+'</strong>?';
    showConfirmModal((esActivar?'\u2713 Activar':'\u2298 Desactivar')+' A\u00f1o Lectivo', color, desc, '', esActivar?'S\u00ed, Activar':'S\u00ed, Desactivar', event.target);
    return false;
}
function showConfirmModal(title, color, msg, extra, btnTxt, form) {
    var m = document.createElement('div');
    m.style.cssText='position:fixed;inset:0;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;z-index:99999;';
    m.innerHTML='<div style="background:#fff;border-radius:12px;padding:28px;max-width:480px;width:90%;box-shadow:0 10px 40px rgba(0,0,0,.25);">'
        +'<h3 style="margin:0 0 12px;color:'+color+';">'+title+'</h3>'
        +'<p style="color:#555;font-size:14px;">'+msg+'</p>'+extra
        +'<div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">'
        +'<button id="__cfCancel" style="padding:9px 20px;background:#f5f5f5;color:#555;border:1px solid #ddd;border-radius:6px;cursor:pointer;">Cancelar</button>'
        +'<button id="__cfOk" style="padding:9px 20px;background:'+color+';color:white;border:none;border-radius:6px;cursor:pointer;font-weight:600;">'+btnTxt+'</button>'
        +'</div></div>';
    document.body.appendChild(m);
    m.querySelector('#__cfOk').onclick     = function(){ document.body.removeChild(m); form.submit(); };
    m.querySelector('#__cfCancel').onclick = function(){ document.body.removeChild(m); };
    m.onclick = function(e){ if(e.target===m) document.body.removeChild(m); };
}

/* \u2500\u2500 Tutor \u2500\u2500 */
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-tutor-assign,.btn-tutor-change,.btn-tutor-remove');
    if (!btn) return;
    var courseId=btn.dataset.courseId, courseName=btn.dataset.courseName;
    if (btn.classList.contains('btn-tutor-remove')) { openRemoveModal(courseId,courseName); }
    else { openTutorModal(courseId,courseName,btn.dataset.hasTutor==='1'); }
});
function openTutorModal(courseId,courseName,hasTutor){
    document.getElementById('tutorCourseId').value=courseId;
    document.getElementById('tutorModalCourse').textContent=courseName;
    document.getElementById('tutorModalTitle').textContent=hasTutor?'Cambiar Tutor':'Asignar Tutor';
    document.getElementById('tutorTeacherSelect').innerHTML='<option value="">Seleccionar...</option>';
    document.getElementById('tutorLoadingMsg').style.display='block';
    document.getElementById('tutorNoTeachers').style.display='none';
    document.getElementById('tutorSubmitBtn').disabled=true;
    document.getElementById('tutorModal').classList.add('active');
    fetch('?action=get_course_teachers&course_id='+courseId)
        .then(function(r){return r.json();})
        .then(function(teachers){
            document.getElementById('tutorLoadingMsg').style.display='none';
            if(!teachers||!teachers.length){document.getElementById('tutorNoTeachers').style.display='block';return;}
            var sel=document.getElementById('tutorTeacherSelect');
            teachers.forEach(function(t){var o=document.createElement('option');o.value=t.teacher_id;o.textContent=t.teacher_name;sel.appendChild(o);});
            document.getElementById('tutorSubmitBtn').disabled=false;
        })
        .catch(function(){
            var msg=document.getElementById('tutorLoadingMsg');
            msg.textContent='\u2717 Error al cargar docentes.'; msg.style.color='#dc3545';
        });
}
function closeTutorModal(){ document.getElementById('tutorModal').classList.remove('active'); }
function openRemoveModal(courseId,courseName){
    document.getElementById('removeTutorCourseId').value=courseId;
    document.getElementById('removeTutorCourseName').textContent=courseName;
    document.getElementById('removeTutorModal').classList.add('active');
}
function closeRemoveModal(){ document.getElementById('removeTutorModal').classList.remove('active'); }
document.getElementById('tutorModal').addEventListener('click',function(e){if(e.target===this)closeTutorModal();});
document.getElementById('removeTutorModal').addEventListener('click',function(e){if(e.target===this)closeRemoveModal();});

/* \u2500\u2500 Estudiantes expandible \u2500\u2500 */
function toggleStudentsRow(courseId) {
    var row=document.getElementById('rowEst_'+courseId);
    var btn=document.getElementById('btnEst_'+courseId);
    if (!row) return;
    var isOpen = row.style.display!=='none';
    // Cerrar todos (estudiantes Y asignaturas)
    document.querySelectorAll('[id^="rowEst_"],[id^="rowSub_"]').forEach(function(r){r.style.display='none';});
    document.querySelectorAll('[id^="btnEst_"]').forEach(function(b){b.style.background='#007bff';});
    document.querySelectorAll('[id^="btnSub_"]').forEach(function(b){b.style.background='#6f42c1';});
    if (!isOpen){ row.style.display=''; btn.style.background='#0056b3'; }
}
function normalize(str){ return str.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,''); }
function filterInlineEnrolled(input,courseId){
    var q=normalize(input.value);
    document.querySelectorAll('#tblEst_'+courseId+' tbody tr').forEach(function(tr){
        tr.style.display=normalize(tr.dataset.name||'').includes(q)?'':'none';
    });
}

/* \u2500\u2500 Matricular \u2500\u2500 */
var smAvailableStudents = JSON.parse(document.getElementById("_dataAvailable").textContent);
var smRepresentatives   = JSON.parse(document.getElementById("_dataReps").textContent);
var smRepsByStudent     = JSON.parse(document.getElementById("_dataRepsByStudent").textContent);
function openEnrollModal(courseId,courseName){
    document.getElementById('emCourseId').value=courseId;
    document.getElementById('emCourseLabel').textContent=courseName;
    document.getElementById('emSearch').value='';
    document.getElementById('emCounter').textContent='0 seleccionados';
    document.getElementById('emSubmit').disabled=true;
    var html='';
    smAvailableStudents.forEach(function(s){
        var name=(s.last_name||'')+' '+(s.first_name||'');
        var dni=s.dni?'<span style="color:#aaa;font-size:11px;">\u00b7 '+s.dni+'</span>':'';
        html+='<div class="emItem" data-name="'+normalize(name)+'" '
            +'style="display:flex;align-items:center;gap:10px;padding:8px 10px;border:1px solid #e8e8e8;border-radius:6px;margin-bottom:5px;cursor:pointer;" '
            +'onclick="emToggleCheck(this)">'
            +'<input type="checkbox" name="student_ids[]" value="'+s.id+'" '
            +'onclick="event.stopPropagation();" onchange="emUpdateCounter()" '
            +'style="width:15px;height:15px;cursor:pointer;flex-shrink:0;">'
            +'<span style="font-size:13px;"><strong>'+name+'</strong>'+dni+'</span></div>';
    });
    if (!html) html='<p style="text-align:center;color:#aaa;padding:20px;font-size:13px;">No hay estudiantes disponibles para matricular.</p>';
    document.getElementById('emList').innerHTML=html;
    document.getElementById('enrollModal').classList.add('active');
}
function closeEnrollModal(){ document.getElementById('enrollModal').classList.remove('active'); }
function filterEmAvail(){
    var q=normalize(document.getElementById('emSearch').value);
    document.querySelectorAll('#emList .emItem').forEach(function(el){el.style.display=normalize(el.dataset.name||'').includes(q)?'':'none';});
}
function emSelectAll(){
    document.querySelectorAll('#emList .emItem').forEach(function(el){if(el.style.display!=='none')el.querySelector('input[type="checkbox"]').checked=true;});
    emUpdateCounter();
}
function emToggleCheck(div){ var cb=div.querySelector('input[type="checkbox"]'); if(cb){cb.checked=!cb.checked;emUpdateCounter();} }
function emUpdateCounter(){
    var n=document.querySelectorAll('#emList input[type="checkbox"]:checked').length;
    document.getElementById('emCounter').textContent=n+' seleccionado'+(n!==1?'s':'');
    document.getElementById('emSubmit').disabled=(n===0);
}

/* \u2500\u2500 Retirar estudiante \u2500\u2500 */
function smConfirmUnenroll(studentId,name,courseId){
    document.getElementById('smUnenrollStudentId').value=studentId;
    document.getElementById('smUnenrollCourseId').value=courseId;
    document.getElementById('smUnenrollMsg').innerHTML='\u00bfRetirar a <strong>'+name+'</strong> del curso?';
    document.getElementById('smUnenrollModal').classList.add('active');
}
function closeSmUnenroll(){ document.getElementById('smUnenrollModal').classList.remove('active'); }

/* ESC cierra modales */
document.addEventListener('keydown',function(e){
    if(e.key==='Escape'){
        closeSYModal(); closeCourseModal(); closeTutorModal();
        closeRemoveModal(); closeEnrollModal(); closeSmUnenroll();
    }
});
</script>



<!-- ══════════════════════════════════════════════════════
     MODAL: EDITAR AÑO LECTIVO
══════════════════════════════════════════════════════ -->
<div id="modalEditSY" class="modal-overlay" onclick="if(event.target===this)closeEditSYModal()">
    <div class="modal-box" style="max-width:540px;">
        <div class="modal-header">
            <div>
                <h3>✏️ Editar Año Lectivo</h3>
                <p id="editSYSubtitle"></p>
            </div>
            <button class="modal-close" onclick="closeEditSYModal()">✕</button>
        </div>
        <form method="POST" id="formEditSY">
            <div class="modal-body">
                <div class="mf-group">
                    <label class="mf-label">Nombre del Año Lectivo *</label>
                    <input type="text" name="name" id="editSYName" class="mf-input" required placeholder="Ej: 2025-2026">
                    <small style="color:#aaa;font-size:11px;">Formato recomendado: AAAA-AAAA</small>
                </div>
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Fecha de Inicio *</label>
                        <input type="date" name="start_date" id="editSYStart" class="mf-input" required>
                    </div>
                    <div>
                        <label class="mf-label">Fecha de Fin *</label>
                        <input type="date" name="end_date" id="editSYEnd" class="mf-input" required>
                    </div>
                </div>
                <div id="editSYActiveNote" style="display:none;background:#e8f5e9;color:#2e7d32;border-radius:7px;padding:10px 14px;font-size:13px;margin-top:4px;">
                    ✅ Este es el <strong>año lectivo activo</strong> actualmente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditSYModal()">Cancelar</button>
                <button type="submit" class="btn-submit">💾 Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════
     MODAL: EDITAR CURSO
══════════════════════════════════════════════════════ -->
<div id="modalEditCourse" class="modal-overlay" onclick="if(event.target===this)closeEditCourseModal()">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <div>
                <h3>✏️ Editar Curso</h3>
                <p id="editCourseSubtitle" style="color:#888;font-size:12px;"></p>
            </div>
            <button class="modal-close" onclick="closeEditCourseModal()">✕</button>
        </div>
        <form method="POST" id="formEditCourse">
            <div class="modal-body">
                <!-- Nivel + Grado -->
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Nivel Educativo *</label>
                        <select name="education_type" id="editEduType" required onchange="editUpdateGradeLevels()" class="mf-input">
                            <option value="">Seleccionar...</option>
                            <option value="inicial">🧒 Educación Inicial</option>
                            <option value="egb">📘 EGB</option>
                            <option value="bgu">🎓 BGU</option>
                            <option value="bt">🛠 Bachillerato Técnico</option>
                        </select>
                    </div>
                    <div>
                        <label class="mf-label">Grado / Año *</label>
                        <select name="grade_level" id="editGradeLevel" required onchange="editOnGradeChange()" class="mf-input">
                            <option value="">Seleccione nivel primero...</option>
                        </select>
                    </div>
                </div>
                <!-- BT Especialidad -->
                <div id="editGroupSpecialty" style="display:none;" class="mf-group">
                    <label class="mf-label">Figura Profesional</label>
                    <select name="specialty" id="editSpecialty" onchange="editUpdateCarreras()" class="mf-input">
                        <option value="">Seleccionar figura...</option>
                        <?php foreach(['Informática','Administración','Contabilidad','Comercialización y Ventas','Servicios Hoteleros','Turismo','Electromecánica Automotriz','Instalaciones Eléctricas','Electrónica de Consumo','Mecanizado y Construcciones Metálicas','Producción Agropecuaria','Acuicultura','Industrialización de Alimentos','Confección Textil','Música','Artes Plásticas','Diseño Gráfico','Servicios de Belleza','Atención Integral en Salud','Mecatrónica','Refrigeración y Climatización','Construcción','Redes y Telecomunicaciones','Seguridad Industrial','Logística y Transporte'] as $fig): ?>
                            <option value="<?= $fig ?>"><?= $fig ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="editGroupCarrera" style="display:none;" class="mf-group">
                    <label class="mf-label">Especialidad / Carrera <span style="color:#aaa;font-weight:400;">(opcional)</span></label>
                    <select name="carrera" id="editCarrera" onchange="editGenerateName()" class="mf-input">
                        <option value="">Sin especificar</option>
                    </select>
                </div>
                <!-- Paralelo + Jornada -->
                <div class="mf-row">
                    <div>
                        <label class="mf-label">Paralelo *</label>
                        <select name="parallel" id="editParallel" required onchange="editGenerateName()" class="mf-input">
                            <option value="">Seleccionar...</option>
                            <?php foreach(range('A','J') as $l): ?>
                                <option value="<?= $l ?>"><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mf-label">Jornada *</label>
                        <select name="shift_id" id="editShiftId" required onchange="editGenerateName()" class="mf-input">
                            <option value="">Seleccionar...</option>
                            <?php foreach($shifts as $shift): ?>
                                <option value="<?= $shift['id'] ?>" data-shift="<?= $shift['name'] ?>"><?= ucfirst($shift['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <!-- Nombre generado -->
                <div class="mf-group" style="margin-bottom:0;">
                    <label class="mf-label">Nombre del Curso <span style="font-weight:400;color:#aaa;">← generado automáticamente</span></label>
                    <div style="position:relative;">
                        <input type="text" name="name" id="editCourseName" readonly class="mf-input"
                               style="padding-left:34px;font-weight:600;color:#1a237e;background:#eef2ff;">
                        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:14px;">✏️</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditCourseModal()">Cancelar</button>
                <button type="submit" class="btn-submit">💾 Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
/* \u2550\u2550 Editar A\u00f1o Lectivo \u2550\u2550 */
function openEditSYModal(id, name, startDate, endDate, isActive) {
    document.getElementById('formEditSY').action = '?action=edit_school_year&id=' + id;
    document.getElementById('editSYName').value  = name;
    document.getElementById('editSYStart').value = startDate;
    document.getElementById('editSYEnd').value   = endDate;
    document.getElementById('editSYSubtitle').textContent = name;
    document.getElementById('editSYActiveNote').style.display = isActive ? 'block' : 'none';
    document.getElementById('modalEditSY').classList.add('active');
}
function closeEditSYModal() { document.getElementById('modalEditSY').classList.remove('active'); }
document.getElementById('modalEditSY').addEventListener('click', function(e){ if(e.target===this) closeEditSYModal(); });

/* \u2550\u2550 Editar Curso \u2550\u2550 */
const editGradeLevels = {
    inicial: ['Inicial 1 (0-3 a\u00f1os)','Inicial 2 (3-5 a\u00f1os)'],
    egb: ['1.\u00ba EGB - Preparatoria','2.\u00ba EGB','3.\u00ba EGB','4.\u00ba EGB','5.\u00ba EGB','6.\u00ba EGB','7.\u00ba EGB','8.\u00ba EGB','9.\u00ba EGB','10.\u00ba EGB'],
    bgu: ['1.\u00ba BGU','2.\u00ba BGU','3.\u00ba BGU'],
    bt:  ['1.\u00ba BT','2.\u00ba BT','3.\u00ba BT']
};
const editEgbNocturna = ['8.\u00ba EGB','9.\u00ba EGB','10.\u00ba EGB'];
const editCarrerasMap = {
    'Inform\u00e1tica': ['Aplicaciones Inform\u00e1ticas','Programaci\u00f3n de Software','Soporte T\u00e9cnico','Sistemas Microinform\u00e1ticos y Redes'],
    'Administraci\u00f3n': ['Asistencia Administrativa','Gesti\u00f3n Empresarial'],
    'Contabilidad': ['Contabilidad','Ventas e Informaci\u00f3n Comercial'],
    'Comercializaci\u00f3n y Ventas': ['Ventas e Informaci\u00f3n Comercial','Marketing'],
    'Servicios Hoteleros': ['Hoteler\u00eda','Hospitalidad'],
    'Turismo': ['Turismo','Gu\u00eda Tur\u00edstico'],
    'Electromec\u00e1nica Automotriz': ['Electromec\u00e1nica Automotriz','Mec\u00e1nica Automotriz'],
    'Instalaciones El\u00e9ctricas': ['Electricidad','Instalaciones El\u00e9ctricas'],
    'Electr\u00f3nica de Consumo': ['Electr\u00f3nica','Mantenimiento Electr\u00f3nico'],
    'Atenci\u00f3n Integral en Salud': ['Atenci\u00f3n en Enfermer\u00eda','Auxiliar de Salud'],
    'Producci\u00f3n Agropecuaria': ['Producci\u00f3n Agropecuaria','Agroindustria'],
    'Redes y Telecomunicaciones': ['Redes','Telecomunicaciones'],
    'Dise\u00f1o Gr\u00e1fico': ['Dise\u00f1o Gr\u00e1fico','Multimedia'],
    'Servicios de Belleza': ['Peluquer\u00eda','Cosmetolog\u00eda']
};

function editDetectType(grade) {
    if (!grade) return '';
    if (grade.includes('Inicial')) return 'inicial';
    if (grade.includes('EGB'))     return 'egb';
    if (grade.includes('BGU'))     return 'bgu';
    if (grade.includes('BT'))      return 'bt';
    return '';
}
function editGetNocturna() {
    var opts = document.getElementById('editShiftId').options;
    for (var i=0;i<opts.length;i++) if ((opts[i].getAttribute('data-shift')||'').toLowerCase()==='nocturna') return opts[i];
    return null;
}
function editUpdateNocturna(type, grade) {
    var n = editGetNocturna(); if (!n) return;
    var v = (type==='bgu'||type==='bt')||(type==='egb'&&editEgbNocturna.includes(grade));
    n.style.display = v ? '' : 'none';
    if (!v && n.selected) { document.getElementById('editShiftId').value=''; editGenerateName(); }
}
function editUpdateGradeLevels(preselect) {
    var type = document.getElementById('editEduType').value;
    var sel  = document.getElementById('editGradeLevel');
    sel.innerHTML = '<option value="">Seleccionar grado...</option>';
    if (!type) return;
    (editGradeLevels[type]||[]).forEach(function(g){
        var o=document.createElement('option'); o.value=o.textContent=g;
        if (preselect && g===preselect) o.selected=true;
        sel.appendChild(o);
    });
    var isBT = type==='bt';
    document.getElementById('editGroupSpecialty').style.display = isBT?'block':'none';
    document.getElementById('editGroupCarrera').style.display   = isBT?'block':'none';
    document.getElementById('editSpecialty').required = isBT;
    if (!isBT) { document.getElementById('editSpecialty').value=''; document.getElementById('editCarrera').innerHTML='<option value="">Sin especificar</option>'; }
    editUpdateNocturna(type, sel.value);
    editGenerateName();
}
function editOnGradeChange() {
    editUpdateNocturna(document.getElementById('editEduType').value, document.getElementById('editGradeLevel').value);
    editGenerateName();
}
function editUpdateCarreras(preselect) {
    var fig = document.getElementById('editSpecialty').value;
    var sel = document.getElementById('editCarrera');
    sel.innerHTML = '<option value="">Sin especificar</option>';
    (editCarrerasMap[fig]||[]).forEach(function(c){
        var o=document.createElement('option'); o.value=o.textContent=c;
        if (preselect && c===preselect) o.selected=true;
        sel.appendChild(o);
    });
    editGenerateName();
}
function editGenerateName() {
    var grade   = document.getElementById('editGradeLevel').value;
    var parallel= document.getElementById('editParallel').value;
    var shiftSel= document.getElementById('editShiftId');
    var shiftOpt= shiftSel.options[shiftSel.selectedIndex];
    var shiftName = shiftOpt ? (shiftOpt.getAttribute('data-shift')||'') : '';
    var type    = document.getElementById('editEduType').value;
    var spec    = document.getElementById('editSpecialty').value;
    var car     = document.getElementById('editCarrera').value;
    if (!grade||!parallel||!shiftName) { document.getElementById('editCourseName').value=''; return; }
    var name = grade + ' "' + parallel + '"';
    if (type==='bt'&&spec) { name += ' - '+spec; if(car) name += ' ('+car+')'; }
    name += ' - '+shiftName.charAt(0).toUpperCase()+shiftName.slice(1);
    document.getElementById('editCourseName').value = name;
}

function openEditCourseModal(id, courseName, gradeLevel, parallel, shiftId) {
    document.getElementById('formEditCourse').action = '?action=edit_course&id=' + id;
    document.getElementById('editCourseSubtitle').textContent = courseName;

    // Detectar tipo y cargar grados
    var type = editDetectType(gradeLevel);
    document.getElementById('editEduType').value = type;
    editUpdateGradeLevels(gradeLevel);

    // Preseleccionar paralelo
    document.getElementById('editParallel').value = parallel;

    // Preseleccionar jornada
    var shiftSel = document.getElementById('editShiftId');
    for (var i=0;i<shiftSel.options.length;i++) {
        if (parseInt(shiftSel.options[i].value) === shiftId) { shiftSel.selectedIndex=i; break; }
    }

    // Si es BT, parsear figura y carrera del nombre
    if (type==='bt') {
        var match = courseName.match(/- ([^(]+?)(?:\s*\(([^)]+)\))?\s*-\s*\w+$/);
        if (match) {
            var fig = match[1].trim(), car = match[2] ? match[2].trim() : '';
            document.getElementById('editSpecialty').value = fig;
            editUpdateCarreras(car);
        }
    }

    editUpdateNocturna(type, gradeLevel);
    editGenerateName();
    document.getElementById('modalEditCourse').classList.add('active');
}
function closeEditCourseModal() { document.getElementById('modalEditCourse').classList.remove('active'); }
document.getElementById('modalEditCourse').addEventListener('click', function(e){ if(e.target===this) closeEditCourseModal(); });

/* Agregar ESC para nuevos modales */
document.addEventListener('keydown', function(e){
    if(e.key==='Escape'){ closeEditSYModal(); closeEditCourseModal(); }
});
</script>



<!-- ══════════════════════════════════════════════════════
     MODAL: AGREGAR ASIGNATURA
══════════════════════════════════════════════════════ -->
<div id="modalAddSubject" class="modal-overlay" onclick="if(event.target===this)closeAddSubjectModal()">
    <div class="modal-box" style="max-width:480px;">
        <div class="modal-header">
            <div>
                <h3>📚 Agregar Asignatura</h3>
                <p id="addSubjectCourseName" style="color:#888;font-size:12px;"></p>
            </div>
            <button class="modal-close" onclick="closeAddSubjectModal()">&#x2715;</button>
        </div>
        <form method="POST" id="formAddSubject">
            <div class="modal-body">
                <div class="mf-group">
                    <label class="mf-label">Nombre de la Asignatura *</label>
                    <input type="text" name="new_subject_name" id="addSubjectName" class="mf-input"
                           required placeholder="Ej: Matematicas, Lenguaje...">
                </div>
                <div class="mf-group" style="margin-bottom:0;">
                    <label class="mf-label">Codigo <span style="color:#aaa;font-weight:400;">(opcional)</span></label>
                    <input type="text" name="new_subject_code" id="addSubjectCode" class="mf-input"
                           placeholder="Ej: MAT, LEN...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeAddSubjectModal()">Cancelar</button>
                <button type="submit" class="btn-submit" style="background:linear-gradient(135deg,#6f42c1,#5a32a3);">&#10133; Agregar</button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     MODAL: ASIGNAR DOCENTE A ASIGNATURA
══════════════════════════════════════════════════════ -->
<div id="modalDocente" class="modal-overlay" onclick="if(event.target===this)cerrarModalDocente()">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div>
                <h3 style="color:#e65100;">&#128100; Asignar Docente</h3>
                <p id="modalDocenteSubject" style="color:#888;font-size:12px;"></p>
            </div>
            <button class="modal-close" onclick="cerrarModalDocente()">&#x2715;</button>
        </div>
        <form method="POST" action="?action=assign_subject_teacher" id="formDocente">
            <div class="modal-body">
                <input type="hidden" name="course_id"     id="modalDocenteCourseId">
                <input type="hidden" name="subject_id"    id="modalDocenteSubjectId">
                <input type="hidden" name="assignment_id" id="modalDocenteAssignId">
                <div class="mf-group" style="margin-bottom:0;">
                    <label class="mf-label">Seleccionar Docente *</label>
                    <select name="teacher_id" id="modalDocenteSelect" required class="mf-input">
                        <option value="">Seleccionar docente...</option>
                        <?php foreach($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>">
                                <?= htmlspecialchars($t['last_name'].' '.$t['first_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="cerrarModalDocente()">Cancelar</button>
                <button type="submit" class="btn-submit" style="background:#e65100;">&#10003; Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
/* \u2500\u2500 Asignaturas expandible \u2500\u2500 */
function toggleSubjectsRow(courseId) {
    var row = document.getElementById('rowSub_' + courseId);
    var btn = document.getElementById('btnSub_' + courseId);
    if (!row) return;
    var isOpen = row.style.display !== 'none';
    // Cerrar todos (asignaturas Y estudiantes)
    document.querySelectorAll('[id^="rowSub_"],[id^="rowEst_"]').forEach(function(r){ r.style.display='none'; });
    document.querySelectorAll('[id^="btnSub_"]').forEach(function(b){ b.style.background='#6f42c1'; });
    document.querySelectorAll('[id^="btnEst_"]').forEach(function(b){ b.style.background='#007bff'; });
    if (!isOpen) {
        row.style.display = '';
        btn.style.background = '#4a0e8f';
    }
}
function filterSubjects(input, courseId) {
    var q = normalize(input.value);
    document.querySelectorAll('#tblSub_' + courseId + ' tbody tr').forEach(function(tr){
        tr.style.display = normalize(tr.dataset.subname || '').includes(q) ? '' : 'none';
    });
}

/* \u2500\u2500 Modal agregar asignatura \u2500\u2500 */
function openAddSubjectModal(courseId, courseName) {
    document.getElementById('formAddSubject').action = '?action=course_subjects&course_id=' + courseId;
    document.getElementById('addSubjectCourseName').textContent = courseName;
    document.getElementById('addSubjectName').value = '';
    document.getElementById('addSubjectCode').value = '';
    document.getElementById('modalAddSubject').classList.add('active');
    setTimeout(function(){ document.getElementById('addSubjectName').focus(); }, 150);
}
function closeAddSubjectModal() {
    document.getElementById('modalAddSubject').classList.remove('active');
}
document.getElementById('modalAddSubject').addEventListener('click', function(e){ if(e.target===this) closeAddSubjectModal(); });

/* \u2500\u2500 Modal docente \u2500\u2500 */
function abrirModalDocente(subjectId, subjectName, assignmentId, teacherId, courseId) {
    document.getElementById('modalDocenteSubjectId').value  = subjectId;
    document.getElementById('modalDocenteCourseId').value   = courseId;
    document.getElementById('modalDocenteAssignId').value   = assignmentId || '';
    document.getElementById('modalDocenteSubject').textContent = subjectName;
    var sel = document.getElementById('modalDocenteSelect');
    sel.value = teacherId || '';
    document.getElementById('modalDocente').classList.add('active');
}
function cerrarModalDocente() {
    document.getElementById('modalDocente').classList.remove('active');
}
document.getElementById('modalDocente').addEventListener('click', function(e){ if(e.target===this) cerrarModalDocente(); });

/* \u2500\u2500 Confirmar quitar asignatura \u2500\u2500 */
function confirmRemoveSubject(event, nombre) {
    event.preventDefault();
    showConfirmModal(
        'Quitar asignatura', '#dc3545',
        'Quitar <strong>' + nombre + '</strong> de este curso?',
        '', 'Si, Quitar', event.target
    );
    return false;
}

/* ESC para nuevos modales */
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') { closeAddSubjectModal(); cerrarModalDocente(); }
});
</script>


<script>
/* \u2500\u2500 Auto-reabrir panel tras POST \u2500\u2500 */
(function(){
    var openStudents = <?= isset($_GET['open_students']) ? (int)$_GET['open_students'] : 'null' ?>;
    var openSubjects = <?= isset($_GET['open_subjects']) ? (int)$_GET['open_subjects'] : 'null' ?>;
    if (openStudents) {
        var row = document.getElementById('rowEst_' + openStudents);
        var btn = document.getElementById('btnEst_' + openStudents);
        if (row) { row.style.display = ''; }
        if (btn) { btn.style.background = '#0056b3'; }
    }
    if (openSubjects) {
        var row = document.getElementById('rowSub_' + openSubjects);
        var btn = document.getElementById('btnSub_' + openSubjects);
        if (row) { row.style.display = ''; }
        if (btn) { btn.style.background = '#4a0e8f'; }
    }
})();
</script>


<!-- ══════════════════════════════════════════════════════
     MODAL: REPRESENTANTES DEL ESTUDIANTE (con script ANTES)
══════════════════════════════════════════════════════ -->

<!-- Script de la función openRepModal (DEFINIDO PRIMERO) -->
<script>
function openRepModal(studentId, studentName, courseId) {
    console.log('openRepModal called', studentId, studentName, courseId); // Para debugging
    
    // Verificar que los datos existen
    if (!studentId || !studentName || !courseId) {
        console.error('Faltan parámetros en openRepModal');
        return;
    }
    
    // Asignar valores a los campos del modal
    document.getElementById('repStudentId').value = studentId;
    document.getElementById('repCourseId').value = courseId;
    document.getElementById('repModalStudentName').textContent = studentName;
    document.getElementById('repSelect').value = '';
    document.getElementById('repRelationship').value = '';

    // Cargar representantes actuales
    var reps = [];
    if (typeof smRepsByStudent !== 'undefined' && smRepsByStudent) {
        reps = smRepsByStudent[String(studentId)] || [];
    }

    // Auto-marcar Principal si no tiene representantes, Secundario si ya tiene
    var chk = document.getElementById('repIsPrimary');
    if (chk) {
        chk.checked = (reps.length === 0);
    }
    
    var html = '';
    if (reps.length === 0) {
        html = '<p style="color:#aaa;font-size:13px;text-align:center;padding:12px 0;">Sin representantes asignados.</p>';
    } else {
        html = '<p style="font-size:13px;font-weight:600;color:#333;margin-bottom:10px;">Representantes actuales:</p>';
        reps.forEach(function(r) {
            var badge = r.is_primary
                ? '<span style="background:#17a2b8;color:#fff;font-size:11px;padding:2px 8px;border-radius:10px;margin-left:6px;">Principal</span>'
                : '<span style="background:#e9ecef;color:#555;font-size:11px;padding:2px 8px;border-radius:10px;margin-left:6px;">Secundario</span>';
            var removeUrl = '?action=remove_rep_from_academic&rep_id=' + r.id
                          + '&student_id=' + studentId + '&course_id=' + courseId;
            html += '<div style="display:flex;justify-content:space-between;align-items:center;'
                  + 'padding:9px 12px;border:1px solid #e0e0e0;border-radius:7px;margin-bottom:7px;background:#fafafa;">'
                  + '<div>'
                  + '<span style="font-size:13px;font-weight:600;">👤 ' + (r.last_name || '') + ' ' + (r.first_name || '') + '</span>'
                  + badge
                  + '<br><span style="font-size:11px;color:#888;">' + (r.relationship || '') + '</span>'
                  + '</div>'
                  + '<a href="' + removeUrl + '" onclick="return confirm(\'¿Quitar representante?\')"'
                  + ' style="padding:4px 10px;font-size:12px;background:#dc3545;color:white;border-radius:4px;text-decoration:none;">Quitar</a>'
                  + '</div>';
        });
    }
    
    var currentList = document.getElementById('repCurrentList');
    if (currentList) {
        currentList.innerHTML = html;
    } else {
        console.error('Elemento repCurrentList no encontrado');
    }
    
    // Mostrar el modal
    var modal = document.getElementById('modalRep');
    if (modal) {
        modal.classList.add('active');
    } else {
        console.error('Modal no encontrado');
    }
}

function closeRepModal() {
    var modal = document.getElementById('modalRep');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Event listeners para cerrar modal
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('modalRep');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeRepModal();
        });
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRepModal();
    }
});
</script>

<!-- Luego el HTML del modal -->
<div id="modalRep" class="modal-overlay">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <div>
                <h3 style="color:#17a2b8;">👨‍👩‍👦 Representantes</h3>
                <p id="repModalStudentName" style="color:#888;font-size:12px;"></p>
            </div>
            <button class="modal-close" onclick="closeRepModal()">✕</button>
        </div>
        <div class="modal-body">
            <!-- Lista de representantes actuales -->
            <div id="repCurrentList" style="margin-bottom:18px;"></div>

            <!-- Formulario nueva asignacion -->
            <div style="border-top:1px solid #f0f0f0;padding-top:16px;">
                <p style="font-size:13px;font-weight:600;color:#333;margin-bottom:12px;">➕ Asignar representante</p>
                <form method="POST" action="?action=assign_rep_from_academic" id="formAssignRep">
                    <input type="hidden" name="student_id" id="repStudentId">
                    <input type="hidden" name="course_id" id="repCourseId">
                    <div class="mf-row">
                        <div>
                            <label class="mf-label">Representante *</label>
                            <select name="representative_id" id="repSelect" required class="mf-input">
                                <option value="">Seleccionar...</option>
                                <?php foreach($representatives ?? [] as $rep): ?>
                                    <option value="<?= $rep['id'] ?>">
                                        <?= htmlspecialchars($rep['last_name'].' '.$rep['first_name']) ?>
                                        <?= $rep['dni'] ? ' ('.$rep['dni'].')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="mf-label">Parentesco *</label>
                            <select name="relationship" id="repRelationship" required class="mf-input">
                                <option value="">Seleccionar...</option>
                                <?php foreach(['Padre','Madre','Tutor Legal','Abuelo/a','Tío/a','Hermano/a','Otro'] as $rel): ?>
                                    <option value="<?= $rel ?>"><?= $rel ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:normal;font-size:13px;">
                            <input type="checkbox" name="is_primary" id="repIsPrimary" value="1"
                                   style="width:16px;height:16px;accent-color:#17a2b8;">
                            <span>
                                <strong>Representante Principal</strong>
                                <span style="color:#888;font-size:11px;display:block;">Recibe notificaciones prioritarias</span>
                            </span>
                        </label>
                    </div>
                    <button type="submit"
                            style="width:100%;padding:9px;background:#17a2b8;color:white;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;margin:0;">
                        ✓ Asignar Representante
                    </button>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeRepModal()">Cerrar</button>
        </div>
    </div>
</div>

</body>
</html>