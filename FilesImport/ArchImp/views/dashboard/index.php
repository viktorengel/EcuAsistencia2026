<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .navbar h1 { font-size: 24px; }
        .navbar .user-info { display: flex; align-items: center; gap: 15px; }
        .navbar a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; transition: background 0.3s; }
        .navbar a:hover { background: rgba(255,255,255,0.2); }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .welcome-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .welcome-card h2 { margin-bottom: 10px; font-size: 28px; }
        .badge { display: inline-block; background: rgba(255,255,255,0.3); color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px; margin: 2px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        .stat-number { font-size: 36px; font-weight: bold; color: #007bff; margin-bottom: 5px; }
        .stat-label { color: #666; font-size: 14px; }
        .stat-card.green .stat-number { color: #28a745; }
        .stat-card.red .stat-number { color: #dc3545; }
        .stat-card.orange .stat-number { color: #fd7e14; }
        .stat-card.purple .stat-number { color: #6f42c1; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .menu-item { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s; border-left: 4px solid #007bff; }
        .menu-item:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
        .menu-item h3 { color: #007bff; margin-bottom: 10px; font-size: 18px; }
        .menu-item p { color: #666; font-size: 14px; }
        .menu-item.green { border-left-color: #28a745; }
        .menu-item.green h3 { color: #28a745; }
        .menu-item.orange { border-left-color: #fd7e14; }
        .menu-item.orange h3 { color: #fd7e14; }
        .menu-item.purple { border-left-color: #6f42c1; }
        .menu-item.purple h3 { color: #6f42c1; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Dashboard - EcuAsist</h1>
        <div>
            <a href="?action=profile">👤 <?= $_SESSION['username'] ?></a>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome-card">
            <h2>Bienvenido, <?= $_SESSION['username'] ?></h2>
            <p>Roles asignados: 
                <?php foreach($_SESSION['roles'] as $role): ?>
                    <span class="badge"><?= ucfirst($role) ?></span>
                <?php endforeach; ?>
            </p>
        </div>

        <?php if(Security::hasRole('autoridad') && isset($stats['total_students'])): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_students'] ?></div>
                <div class="stat-label">Estudiantes</div>
            </div>
            <div class="stat-card green">
                <div class="stat-number"><?= $stats['total_teachers'] ?></div>
                <div class="stat-label">Docentes</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-number"><?= $stats['total_courses'] ?></div>
                <div class="stat-label">Cursos</div>
            </div>
            <?php if($stats['today_attendance']['total'] > 0): ?>
            <div class="stat-card purple">
                <div class="stat-number">
                    <?= round(($stats['today_attendance']['presente'] / $stats['today_attendance']['total']) * 100, 1) ?>%
                </div>
                <div class="stat-label">Asistencia Hoy</div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if(Security::hasRole('docente') && isset($stats['my_courses'])): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['my_courses'] ?></div>
                <div class="stat-label">Mis Cursos</div>
            </div>
            <div class="stat-card green">
                <div class="stat-number"><?= $stats['my_students'] ?></div>
                <div class="stat-label">Mis Estudiantes</div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(Security::hasRole('estudiante') && isset($stats['my_attendance'])): ?>
        <div class="stats-grid">
            <div class="stat-card green">
                <div class="stat-number">
                    <?= $stats['my_attendance']['total'] > 0 ? round(($stats['my_attendance']['presente'] / $stats['my_attendance']['total']) * 100, 1) : 0 ?>%
                </div>
                <div class="stat-label">Mi Asistencia (30 días)</div>
            </div>
            <div class="stat-card red">
                <div class="stat-number"><?= $stats['my_attendance']['ausente'] ?></div>
                <div class="stat-label">Ausencias</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-number"><?= $stats['my_attendance']['tardanza'] ?></div>
                <div class="stat-label">Tardanzas</div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(Security::hasRole('representante') && isset($stats['my_children'])): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['my_children'] ?></div>
                <div class="stat-label">Representados</div>
            </div>
        </div>
        <?php endif; ?>

        <div class="menu-grid">
            <?php if(Security::hasRole(['docente', 'autoridad'])): ?>
            <div class="menu-item green" onclick="location.href='?action=attendance_register'">
                <h3>📝 Registrar Asistencia</h3>
                <p>Tomar asistencia de estudiantes</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole(['docente', 'inspector', 'autoridad'])): ?>
            <div class="menu-item" onclick="location.href='?action=attendance_view'">
                <h3>📊 Ver Asistencias</h3>
                <p>Consultar registros de asistencia</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole('autoridad')): ?>
            <div class="menu-item purple" onclick="location.href='?action=stats'">
                <h3>📈 Estadísticas</h3>
                <p>Análisis y métricas de asistencia</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole(['docente', 'autoridad'])): ?>
            <div class="menu-item orange" onclick="location.href='?action=attendance_calendar'">
                <h3>📅 Calendario</h3>
                <p>Vista mensual de asistencias</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole('estudiante')): ?>
            <div class="menu-item" onclick="location.href='?action=my_attendance'">
                <h3>📋 Mi Asistencia</h3>
                <p>Ver mi historial de asistencia</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole('estudiante')): ?>
            <div class="menu-item green" onclick="location.href='?action=my_justifications'">
                <h3>📝 Mis Justificaciones</h3>
                <p>Justificar ausencias</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole(['autoridad', 'inspector'])): ?>
            <div class="menu-item orange" onclick="location.href='?action=pending_justifications'">
                <h3>✅ Revisar Justificaciones</h3>
                <p>Aprobar o rechazar justificaciones</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole('autoridad')): ?>
            <div class="menu-item orange" onclick="location.href='?action=users'">
                <h3>👥 Gestión de Usuarios</h3>
                <p>Administrar usuarios y roles</p>
            </div>

            <?php if(Security::hasRole('autoridad')): ?>
            <div class="menu-item purple" onclick="location.href='?action=backups'">
                <h3>💾 Respaldos</h3>
                <p>Gestionar respaldos de base de datos</p>
            </div>
            <?php endif; ?>

            <div class="menu-item purple" onclick="location.href='?action=reports'">
                <h3>📄 Reportes</h3>
                <p>Generar informes PDF y Excel</p>
            </div>

            <div class="menu-item" onclick="location.href='?action=academic'">
                <h3>🏫 Configuración Académica</h3>
                <p>Cursos, materias, matrículas</p>
            </div>

            <div class="menu-item green" onclick="location.href='?action=assignments'">
                <h3>📚 Asignaciones Docentes</h3>
                <p>Gestionar docentes y tutores</p>
            </div>

            <div class="menu-item orange" onclick="location.href='?action=manage_representatives'">
                <h3>👨‍👩‍👧‍👦 Gestión Representantes</h3>
                <p>Asignar representantes a estudiantes</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole('representante')): ?>
            <div class="menu-item purple" onclick="location.href='?action=my_children'">
                <h3>👨‍👩‍👧‍👦 Mis Representados</h3>
                <p>Ver estudiantes y su asistencia</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>