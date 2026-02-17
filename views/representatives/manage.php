<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Representantes - EcuAsist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 70px; background: #f4f4f4; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container mt-4">
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                ✓ Relación asignada correctamente
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['removed'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                ✓ Relación eliminada correctamente
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                ✗ Error al procesar la solicitud
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulario de asignación -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">👥 Asignar Estudiante a Representante</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Representante *</label>
                            <select name="representative_id" class="form-select" required>
                                <option value="">Seleccionar representante...</option>
                                <?php foreach($representatives as $rep): ?>
                                    <option value="<?= $rep['id'] ?>">
                                        <?= $rep['last_name'] . ' ' . $rep['first_name'] ?> 
                                        (<?= $rep['dni'] ?? 'Sin cédula' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estudiante *</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Seleccionar estudiante...</option>
                                <?php foreach($students as $student): ?>
                                    <option value="<?= $student['id'] ?>">
                                        <?= $student['last_name'] . ' ' . $student['first_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Parentesco *</label>
                            <select name="relationship" class="form-select" required>
                                <option value="">Seleccionar parentesco...</option>
                                <option value="Padre">Padre</option>
                                <option value="Madre">Madre</option>
                                <option value="Tutor Legal">Tutor Legal</option>
                                <option value="Abuelo/a">Abuelo/a</option>
                                <option value="Tío/a">Tío/a</option>
                                <option value="Hermano/a">Hermano/a</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="form-check" style="padding-top: 8px;">
                                <input type="checkbox" name="is_primary" value="1" class="form-check-input" id="isPrimary">
                                <label class="form-check-label" for="isPrimary">
                                    <strong>Representante Principal</strong>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        ➕ Asignar Relación
                    </button>
                </form>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">🔍 Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Buscar por Representante</label>
                        <input type="text" id="filterRepresentative" class="form-control" placeholder="Nombre del representante...">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Buscar por Estudiante</label>
                        <input type="text" id="filterStudent" class="form-control" placeholder="Nombre del estudiante...">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Buscar por Curso</label>
                        <input type="text" id="filterCourse" class="form-control" placeholder="Nombre del curso...">
                    </div>
                </div>
                <button onclick="clearFilters()" class="btn btn-secondary btn-sm">
                    🗑️ Limpiar Filtros
                </button>
            </div>
        </div>

        <!-- Lista de relaciones -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📋 Representantes y sus Estudiantes</h5>
                <span class="badge bg-primary" id="totalCount">
                    <?php 
                    $totalRelations = 0;
                    foreach($representatives as $rep) {
                        $totalRelations += count($this->representativeModel->getStudentsByRepresentative($rep['id']));
                    }
                    echo $totalRelations;
                    ?> relaciones
                </span>
            </div>
            <div class="card-body">
                <?php 
                $hasRelations = false;
                foreach($representatives as $rep): 
                    $children = $this->representativeModel->getStudentsByRepresentative($rep['id']);
                    if(count($children) > 0):
                        $hasRelations = true;
                ?>
                <div class="representative-block mb-4 p-3 border rounded" 
                     data-representative="<?= strtolower($rep['last_name'] . ' ' . $rep['first_name']) ?>">
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">
                            👤 <?= $rep['last_name'] . ' ' . $rep['first_name'] ?>
                        </h5>
                        <small class="text-muted"><?= $rep['email'] ?></small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Parentesco</th>
                                    <th>Curso</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($children as $child): ?>
                                <tr class="student-row" 
                                    data-student="<?= strtolower($child['last_name'] . ' ' . $child['first_name']) ?>"
                                    data-course="<?= strtolower($child['course_name'] ?? '') ?>">
                                    <td>
                                        <strong><?= $child['last_name'] . ' ' . $child['first_name'] ?></strong>
                                    </td>
                                    <td><?= $child['relationship'] ?></td>
                                    <td>
                                        <?php if($child['course_name']): ?>
                                            <span class="badge bg-info"><?= $child['course_name'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Sin curso</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($child['is_primary']): ?>
                                            <span class="badge bg-warning text-dark">⭐ Principal</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Secundario</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button onclick="confirmRemove(<?= $rep['id'] ?>, <?= $child['id'] ?>, '<?= addslashes($rep['last_name'] . ' ' . $rep['first_name']) ?>', '<?= addslashes($child['last_name'] . ' ' . $child['first_name']) ?>')" 
                                                class="btn btn-danger btn-sm">
                                            ✗ Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php 
                    endif; 
                endforeach; 
                
                if(!$hasRelations):
                ?>
                <div class="alert alert-secondary text-center">
                    📂 No hay relaciones representante-estudiante registradas.
                    <br>Use el formulario superior para crear una.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filtros en tiempo real
        const filterRepresentative = document.getElementById('filterRepresentative');
        const filterStudent = document.getElementById('filterStudent');
        const filterCourse = document.getElementById('filterCourse');

        function applyFilters() {
            const repValue = filterRepresentative.value.toLowerCase();
            const stuValue = filterStudent.value.toLowerCase();
            const courValue = filterCourse.value.toLowerCase();

            const blocks = document.querySelectorAll('.representative-block');
            let visibleCount = 0;

            blocks.forEach(block => {
                const repName = block.dataset.representative;
                const rows = block.querySelectorAll('.student-row');
                let hasVisibleRows = false;

                rows.forEach(row => {
                    const stuName = row.dataset.student;
                    const course = row.dataset.course;

                    const matchRep = !repValue || repName.includes(repValue);
                    const matchStu = !stuValue || stuName.includes(stuValue);
                    const matchCou = !courValue || course.includes(courValue);

                    if (matchRep && matchStu && matchCou) {
                        row.style.display = '';
                        hasVisibleRows = true;
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                block.style.display = hasVisibleRows ? '' : 'none';
            });

            // Actualizar contador
            document.getElementById('totalCount').textContent = visibleCount + ' relaciones';
        }

        function clearFilters() {
            filterRepresentative.value = '';
            filterStudent.value = '';
            filterCourse.value = '';
            applyFilters();
        }

        filterRepresentative.addEventListener('input', applyFilters);
        filterStudent.addEventListener('input', applyFilters);
        filterCourse.addEventListener('input', applyFilters);

        // Modal de confirmación
        function confirmRemove(repId, studentId, repName, studentName) {
            const modal = document.createElement('div');
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
            
            const modalContent = document.createElement('div');
            modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
            
            modalContent.innerHTML = `
                <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Eliminar Relación</h3>
                <p style="margin: 0 0 20px 0; color: #666;">
                    ¿Está seguro de eliminar la relación entre:
                </p>
                <p style="margin: 0 0 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                    <strong>Representante:</strong> ${repName}<br>
                    <strong>Estudiante:</strong> ${studentName}
                </p>
                <p style="margin: 0 0 20px 0; color: #666; font-size: 14px; background: #f8d7da; padding: 10px; border-radius: 4px;">
                    <strong>⚠️ Advertencia:</strong> El representante ya no podrá ver la información del estudiante.
                </p>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" id="cancelRemoveBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="button" id="confirmRemoveBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Sí, Eliminar
                    </button>
                </div>
            `;
            
            modal.appendChild(modalContent);
            document.body.appendChild(modal);
            
            document.getElementById('confirmRemoveBtn').onclick = function() {
                document.body.removeChild(modal);
                window.location.href = '?action=remove_representative&rep_id=' + repId + '&student_id=' + studentId;
            };
            
            document.getElementById('cancelRemoveBtn').onclick = function() {
                document.body.removeChild(modal);
            };
        }
    </script>
</body>
</html>