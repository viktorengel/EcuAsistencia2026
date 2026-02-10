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
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        button { flex: 1; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-pdf { background: #dc3545; color: white; }
        .btn-pdf:hover { background: #c82333; }
        .btn-excel { background: #28a745; color: white; }
        .btn-excel:hover { background: #218838; }
        h2 { margin-bottom: 20px; color: #333; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 4px; border-left: 4px solid #007bff; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Generación de Reportes</h1>
        <div>
            <a href="?action=dashboard">← Dashboard</a>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <div class="info">
            <strong>📊 Tipos de reporte disponibles:</strong><br>
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
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['id'] ?>">
                                <?= $course['name'] ?> - <?= $course['shift_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="start_date" value="<?= date('Y-m-01') ?>" required>
                </div>

                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="end_date" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn-pdf" onclick="generateReport('pdf')">
                        📄 Generar PDF
                    </button>
                    <button type="button" class="btn-excel" onclick="generateReport('excel')">
                        📊 Generar Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function generateReport(type) {
            const form = document.getElementById('report-form');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (type === 'pdf') {
                form.action = '?action=generate_pdf';
            } else {
                form.action = '?action=generate_excel';
            }
            
            form.submit();
        }
    </script>
</body>
</html>