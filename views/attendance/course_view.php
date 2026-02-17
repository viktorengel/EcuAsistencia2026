<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia del Curso - EcuAsist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 70px; background: #f4f4f4; }
        .badge-presente { background-color: #28a745; }
        .badge-ausente { background-color: #dc3545; }
        .badge-tardanza { background-color: #ffc107; color: #000; }
        .badge-justificado { background-color: #17a2b8; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container mt-4">
        
        <?php if(empty($courses)): ?>
        <div class="alert alert-warning">
            <h5>⚠️ Sin Cursos Asignados</h5>
            <p class="mb-0">
                <?php if(Security::hasRole('docente')): ?>
                    No tiene cursos asignados como tutor ni asignaturas asignadas. 
                    Contacte con la autoridad para que le asignen cursos.
                <?php else: ?>
                    No hay cursos disponibles en el sistema.
                <?php endif; ?>
            </p>
        </div>
        <?php else: ?>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📊 Asistencia General del Curso</h5>
                <?php if(Security::hasRole('docente')): ?>
                    <small>Mostrando solo cursos donde es tutor o tiene asignaturas</small>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <input type="hidden" name="action" value="view_course_attendance">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Curso *</label>
                            <select name="course_id" class="form-select" required>
                                <option value="">Seleccionar curso...</option>
                                <?php foreach($courses as $c): 
                                    $isTutor = isset($c['is_tutor']) && $c['is_tutor'];
                                    $label = html_entity_decode($c['name'], ENT_QUOTES, 'UTF-8');
                                    if ($isTutor) $label .= ' ⭐ (Mi curso / Tutor)';
                                ?>
                                    <option value="<?= $c['id'] ?>" 
                                        <?= isset($courseId) && $courseId == $c['id'] ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="start_date" class="form-control" 
                                   value="<?= $startDate ?? date('Y-m-01') ?>">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="end_date" class="form-control" 
                                   value="<?= $endDate ?? date('Y-m-d') ?>">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                🔍 Consultar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if(isset($courseId) && !empty($attendanceData)): ?>
        
        <!-- Resumen estadístico -->
        <div class="row mb-4">
            <?php
            $totalPresente = array_sum(array_column($attendanceData, 'presente'));
            $totalAusente = array_sum(array_column($attendanceData, 'ausente'));
            $totalTardanza = array_sum(array_column($attendanceData, 'tardanza'));
            $totalJustificado = array_sum(array_column($attendanceData, 'justificado'));
            $totalRegistros = array_sum(array_column($attendanceData, 'total_registros'));
            ?>
            
            <div class="col-md-3">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <h3 class="text-success"><?= $totalPresente ?></h3>
                        <p class="mb-0">Presentes</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center border-danger">
                    <div class="card-body">
                        <h3 class="text-danger"><?= $totalAusente ?></h3>
                        <p class="mb-0">Ausentes</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <h3 class="text-warning"><?= $totalTardanza ?></h3>
                        <p class="mb-0">Tardanzas</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center border-info">
                    <div class="card-body">
                        <h3 class="text-info"><?= $totalJustificado ?></h3>
                        <p class="mb-0">Justificados</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de estudiantes -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Estudiantes del Curso</h5>
                <span class="badge bg-primary"><?= count($attendanceData) ?> estudiantes</span>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>📅 Período:</strong> 
                    <?= date('d/m/Y', strtotime($startDate)) ?> al <?= date('d/m/Y', strtotime($endDate)) ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Estudiante</th>
                                <th class="text-center">Total Registros</th>
                                <th class="text-center">Presentes</th>
                                <th class="text-center">Ausentes</th>
                                <th class="text-center">Tardanzas</th>
                                <th class="text-center">Justificados</th>
                                <th class="text-center">% Asistencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 1;
                            foreach($attendanceData as $data): 
                                $percentage = $data['total_registros'] > 0 
                                    ? round(($data['presente'] / $data['total_registros']) * 100, 1) 
                                    : 0;
                                
                                $percentageClass = '';
                                if ($percentage >= 90) $percentageClass = 'text-success fw-bold';
                                elseif ($percentage >= 75) $percentageClass = 'text-warning fw-bold';
                                else $percentageClass = 'text-danger fw-bold';
                            ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td><strong><?= $data['student_name'] ?></strong></td>
                                <td class="text-center"><?= $data['total_registros'] ?></td>
                                <td class="text-center">
                                    <span class="badge badge-presente"><?= $data['presente'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-ausente"><?= $data['ausente'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-tardanza"><?= $data['tardanza'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-justificado"><?= $data['justificado'] ?></span>
                                </td>
                                <td class="text-center <?= $percentageClass ?>">
                                    <?= $percentage ?>%
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Nota:</strong> El porcentaje de asistencia se calcula sobre el total de registros (Presentes / Total Registros × 100).
                    </small>
                </div>
            </div>
        </div>

        <?php elseif(isset($courseId) && empty($attendanceData)): ?>
        <div class="alert alert-warning text-center">
            ⚠️ No hay registros de asistencia para el curso seleccionado en el período indicado.
        </div>

        <?php else: ?>
        <div class="alert alert-info text-center">
            👆 Seleccione un curso y período para ver la asistencia.
        </div>
        <?php endif; ?>

        <?php endif; // Fin de validación de cursos ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>