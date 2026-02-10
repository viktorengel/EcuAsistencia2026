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
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .menu-item { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; cursor: pointer; transition: transform 0.2s; }
        .menu-item:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
        .menu-item h3 { color: #007bff; margin-bottom: 10px; }
        .menu-item p { color: #666; font-size: 14px; }
        .badge { display: inline-block; background: #28a745; color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px; margin: 2px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>EcuAsist 2026</h1>
        <div>
            <span><?= $_SESSION['username'] ?></span>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h2>Bienvenido, <?= $_SESSION['username'] ?></h2>
            <p>Roles asignados: 
                <?php foreach($_SESSION['roles'] as $role): ?>
                    <span class="badge"><?= ucfirst($role) ?></span>
                <?php endforeach; ?>
            </p>
        </div>

        <div class="menu-grid">
            <?php if(Security::hasRole(['docente', 'autoridad'])): ?>
            <div class="menu-item" onclick="location.href='?action=attendance_register'">
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

            <?php if(Security::hasRole('estudiante')): ?>
            <div class="menu-item" onclick="location.href='?action=my_attendance'">
                <h3>📋 Mi Asistencia</h3>
                <p>Ver mi historial de asistencia</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole('representante')): ?>
            <div class="menu-item" onclick="location.href='?action=my_children'">
                <h3>👨‍👩‍👧‍👦 Mis Representados</h3>
                <p>Ver estudiantes y su asistencia</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole('autoridad')): ?>
            <div class="menu-item" onclick="location.href='?action=manage_representatives'">
                <h3>👥 Gestión Representantes</h3>
                <p>Asignar representantes a estudiantes</p>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole('autoridad')): ?>
            <div class="menu-item" onclick="location.href='?action=users'">
                <h3>👥 Gestión de Usuarios</h3>
                <p>Administrar usuarios y roles</p>
            </div>

            <?php if(Security::hasRole('autoridad')): ?>
            <div class="menu-item" onclick="location.href='?action=assignments'">
                <h3>📚 Asignaciones Docentes</h3>
                <p>Gestionar docentes por materia y tutores</p>
            </div>
            <?php endif; ?>

            <div class="menu-item" onclick="location.href='?action=reports'">
                <h3>📄 Reportes</h3>
                <p>Generar informes PDF y Excel</p>
            </div>

            <div class="menu-item" onclick="location.href='?action=academic'">
                <h3>🏫 Configuración Académica</h3>
                <p>Cursos, materias, asignaciones</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>