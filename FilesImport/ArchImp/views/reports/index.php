<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        button { flex: 1; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-preview { background: #007bff; color: white; }
        .btn-preview:hover { background: #0056b3; }
        .btn-pdf { background: #dc3545; color: white; }
        .btn-pdf:hover { background: #c82333; }
        .btn-excel { background: #28a745; color: white; }
        .btn-excel:hover { background: #218838; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 4px; border-left: 4px solid #007bff; margin-bottom: 20px; }
        h2 { margin-bottom: 20px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge { padding: 5px 10px; border-radius: 3px; color: white; font-size: 12px; }
        .badge-presente { background: #28a745; }
        .badge-ausente { background: #dc3545; }
        .badge-tardanza { background: #ffc107; color: #333; }
        .badge-justificado { background: #17a2b8; }
        .stats { background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
        .stat-item { text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #007bff; }
        .stat-label { font-size: 12px; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="info">
            <strong>📊 Tipos de reporte disponibles:</strong><br>
            • <strong>Vista Previa:</strong> Ver datos en pantalla antes de exportar<br>
            • <strong>PDF:</strong> Reporte formal con formato institucional<br>
            • <strong>Excel:</strong> Datos estructurados para análisis
        </div>

        <div class="card">
            <h2>Configuración del Reporte</h2>
            
            <form method="POST" id="report-form">
                <div class="form-group">
                    <label>Curso</label>
                    <select name="course_id" required>
                        <option value="">Seleccionar curso...</option>
                        <?php foreach($courses as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= isset($course) && $course['id'] == $c['id'] ? 'selected' : '' ?>>
                                <?= $c['name'] ?> - <?= $c['shift_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="start_date" value="<?= $startDate ?? date('Y-m-01') ?>" required>
                </div>

                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="end_date" value="<?= $endDate ?? date('Y-m-d') ?>" required>
                </div>

                <div class="btn-group">
                    <button type="submit" name="preview" class="btn-preview">
                        👁️ Vista Previa
                    </button>
                    <button type="button" class="btn-pdf" onclick="generateReport('pdf')" <?= empty($data) ? 'disabled' : '' ?>>
                        📄 Generar PDF
                    </button>
                    <button type="button" class="btn-excel" onclick="generateReport('excel')" <?= empty($data) ? 'disabled' : '' ?>>
                        📊 Generar Excel
                    </button>
                </div>
            </form>
        </div>

        <?php if(!empty($data)): ?>
        <div class="card">
            <h2>Vista Previa del Reporte</h2>
            
            <div class="info">
                <strong>Curso:</strong> <?= $course['name'] ?> - <?= $course['shift_name'] ?><br>
                <strong>Período:</strong> <?= date('d/m/Y', strtotime($startDate)) ?> al <?= date('d/m/Y', strtotime($endDate)) ?><br>
                <strong>Total Registros:</strong> <?= count($data) ?>
            </div>

            <?php
            $totalPresente = count(array_filter($data, fn($d) => $d['status'] === 'presente'));
            $totalAusente = count(array_filter($data, fn($d) => $d['status'] === 'ausente'));
            $totalTardanza = count(array_filter($data, fn($d) => $d['status'] === 'tardanza'));
            $totalJustificado = count(array_filter($data, fn($d) => $d['status'] === 'justificado'));
            $porcentaje = count($data) > 0 ? round(($totalPresente / count($data)) * 100, 1) : 0;
            ?>

            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number" style="color: #28a745;"><?= $totalPresente ?></div>
                    <div class="stat-label">Presentes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: #dc3545;"><?= $totalAusente ?></div>
                    <div class="stat-label">Ausentes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: #ffc107;"><?= $totalTardanza ?></div>
                    <div class="stat-label">Tardanzas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: #17a2b8;"><?= $totalJustificado ?></div>
                    <div class="stat-label">Justificados</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: #6f42c1;"><?= $porcentaje ?>%</div>
                    <div class="stat-label">Asistencia</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estudiante</th>
                        <th>Asignatura</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data as $row): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['date'])) ?></td>
                        <td><?= $row['student_name'] ?></td>
                        <td><?= $row['subject_name'] ?></td>
                        <td><?= $row['hour_period'] ?></td>
                        <td>
                            <span class="badge badge-<?= $row['status'] ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td><?= $row['observation'] ?: '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php elseif($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="card">
            <p style="text-align: center; color: #666; padding: 40px;">
                No hay registros de asistencia para el período seleccionado
            </p>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function generateReport(type) {
            const form = document.getElementById('report-form');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Crear un formulario temporal
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.style.display = 'none';
            
            if (type === 'pdf') {
                tempForm.action = '?action=generate_pdf';
            } else {
                tempForm.action = '?action=generate_excel';
            }
            
            // Copiar valores del formulario original
            const courseId = form.querySelector('[name="course_id"]').value;
            const startDate = form.querySelector('[name="start_date"]').value;
            const endDate = form.querySelector('[name="end_date"]').value;
            
            // Crear inputs ocultos
            const inputs = [
                { name: 'course_id', value: courseId },
                { name: 'start_date', value: startDate },
                { name: 'end_date', value: endDate }
            ];
            
            inputs.forEach(input => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = input.name;
                hiddenInput.value = input.value;
                tempForm.appendChild(hiddenInput);
            });
            
            // Agregar al body, enviar y eliminar
            document.body.appendChild(tempForm);
            tempForm.submit();
            document.body.removeChild(tempForm);
        }
    </script>
</body>
</html>