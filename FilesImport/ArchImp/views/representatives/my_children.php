<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Representados - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .student-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .student-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .student-card h3 { color: #007bff; margin-bottom: 15px; }
        .info-row { padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        .info-row:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #666; display: inline-block; width: 120px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-top: 15px; text-align: center; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Mis Representados</h1>
        <div>
            <a href="?action=dashboard">← Dashboard</a>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <?php if(count($children) > 0): ?>
        <div class="student-grid">
            <?php foreach($children as $child): ?>
            <div class="student-card">
                <h3><?= $child['last_name'] . ' ' . $child['first_name'] ?></h3>
                
                <div class="info-row">
                    <span class="label">Cédula:</span>
                    <?= $child['dni'] ?? 'No registrada' ?>
                </div>
                
                <div class="info-row">
                    <span class="label">Parentesco:</span>
                    <?= $child['relationship'] ?>
                    <?php if($child['is_primary']): ?>
                        <strong style="color: #ffc107;">(Principal)</strong>
                    <?php endif; ?>
                </div>
                
                <div class="info-row">
                    <span class="label">Curso:</span>
                    <?= $child['course_name'] ?? 'Sin asignar' ?>
                </div>
                
                <div class="info-row">
                    <span class="label">Jornada:</span>
                    <?= $child['shift_name'] ? ucfirst($child['shift_name']) : '-' ?>
                </div>
                
                <a href="?action=child_attendance&student_id=<?= $child['id'] ?>" class="btn">
                    Ver Asistencia
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="student-card" style="text-align: center; padding: 40px;">
            <p style="color: #666;">No tiene estudiantes asignados como representante.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>