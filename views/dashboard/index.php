<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    🏠 Inicio &rsaquo; Dashboard
</div>

<div class="container">

    <!-- Header de bienvenida -->
    <div class="page-header" style="background: linear-gradient(135deg, #667eea, #764ba2);">
        <div class="ph-icon">👋</div>
        <div>
            <h1>Bienvenido, <?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?></h1>
            <p>
                <?php foreach($_SESSION['roles'] as $role): ?>
                    <span class="badge badge-blue"><?= ucfirst($role) ?></span>
                <?php endforeach; ?>
            </p>
        </div>
    </div>

    <!-- ── Stats: Autoridad ── -->
    <?php if(Security::hasRole('autoridad') && isset($stats['total_students'])): ?>
    <div class="stats-row">
        <div class="stat-card blue">
            <div class="number"><?= $stats['total_students'] ?></div>
            <div class="label">Estudiantes</div>
        </div>
        <div class="stat-card green">
            <div class="number"><?= $stats['total_teachers'] ?></div>
            <div class="label">Docentes</div>
        </div>
        <div class="stat-card orange">
            <div class="number"><?= $stats['total_courses'] ?></div>
            <div class="label">Cursos</div>
        </div>
        <?php if(!empty($stats['today_attendance']['total']) && $stats['today_attendance']['total'] > 0): ?>
        <div class="stat-card teal">
            <div class="number"><?= round(($stats['today_attendance']['presente'] / $stats['today_attendance']['total']) * 100, 1) ?>%</div>
            <div class="label">Asistencia Hoy</div>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?= round(($stats['today_attendance']['presente'] / $stats['today_attendance']['total']) * 100) ?>%;background:#17a2b8;"></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Stats: Docente ── -->
    <?php if(Security::hasRole('docente') && isset($stats['my_courses'])): ?>
    <div class="stats-row">
        <div class="stat-card blue">
            <div class="number"><?= $stats['my_courses'] ?></div>
            <div class="label">Mis Cursos</div>
        </div>
        <div class="stat-card green">
            <div class="number"><?= $stats['my_students'] ?></div>
            <div class="label">Mis Estudiantes</div>
        </div>
        <?php if(!empty($stats['tutor_course'])): ?>
        <div class="stat-card purple">
            <div style="font-size:1.4rem;margin-bottom:4px;">🎓</div>
            <div class="number" style="font-size:1rem;"><?= htmlspecialchars($stats['tutor_course']) ?></div>
            <div class="label">Mi Curso como Tutor</div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── Stats: Estudiante ── -->
    <?php if(Security::hasRole('estudiante') && isset($stats['my_attendance'])): ?>
    <?php $pctAsis = $stats['my_attendance']['total'] > 0 ? round(($stats['my_attendance']['presente'] / $stats['my_attendance']['total']) * 100, 1) : 0; ?>
    <div class="stats-row">
        <div class="stat-card green">
            <div class="number"><?= $pctAsis ?>%</div>
            <div class="label">Mi Asistencia (30 días)</div>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $pctAsis ?>%;background:#28a745;"></div></div>
        </div>
        <div class="stat-card red">
            <div class="number"><?= $stats['my_attendance']['ausente'] ?></div>
            <div class="label">Ausencias</div>
        </div>
        <div class="stat-card yellow">
            <div class="number"><?= $stats['my_attendance']['tardanza'] ?></div>
            <div class="label">Tardanzas</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Stats: Representante ── -->
    <?php if(Security::hasRole('representante') && isset($stats['my_children'])): ?>
    <div class="stats-row">
        <div class="stat-card blue">
            <div class="number"><?= $stats['my_children'] ?></div>
            <div class="label">Representados</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Menú de accesos rápidos ── -->
    <div class="section-title">Accesos rápidos</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;margin-bottom:24px;">

        <?php if(Security::hasRole(['docente','autoridad'])): ?>
        <a href="?action=attendance_register" class="menu-card green">
            <div class="mc-icon">📝</div>
            <div class="mc-title">Registrar Asistencia</div>
            <div class="mc-desc">Tomar asistencia de estudiantes</div>
        </a>
        <?php endif; ?>

        <?php if(Security::hasRole('docente')): ?>
        <a href="?action=tutor_course_attendance" class="menu-card blue">
            <div class="mc-icon">🎓</div>
            <div class="mc-title">Asistencia de Mi Curso</div>
            <div class="mc-desc">Ver asistencia del curso que tutoro</div>
        </a>
        <?php endif; ?>

        <?php if(Security::hasRole(['docente','inspector','autoridad'])): ?>
        <a href="?action=attendance_view" class="menu-card blue">
            <div class="mc-icon">📊</div>
            <div class="mc-title">Ver Asistencias</div>
            <div class="mc-desc">Consultar registros de asistencia</div>
        </a>
        <a href="?action=attendance_calendar" class="menu-card orange">
            <div class="mc-icon">📅</div>
            <div class="mc-title">Calendario</div>
            <div class="mc-desc">Vista mensual de asistencias</div>
        </a>
        <?php endif; ?>

        <?php if(Security::hasRole('estudiante')): ?>
        <a href="?action=my_attendance" class="menu-card blue">
            <div class="mc-icon">📋</div>
            <div class="mc-title">Mi Asistencia</div>
            <div class="mc-desc">Ver mi historial de asistencia</div>
        </a>
        <a href="?action=my_justifications" class="menu-card green">
            <div class="mc-icon">📝</div>
            <div class="mc-title">Mis Justificaciones</div>
            <div class="mc-desc">Justificar ausencias</div>
        </a>
        <?php endif; ?>

        <?php if(Security::hasRole(['autoridad','inspector'])): ?>
        <a href="?action=pending_justifications" class="menu-card orange">
            <div class="mc-icon">✅</div>
            <div class="mc-title">Revisar Justificaciones</div>
            <div class="mc-desc">Aprobar o rechazar justificaciones pendientes</div>
        </a>
        <?php endif; ?>

        <?php if(Security::hasRole('autoridad')): ?>
        <a href="?action=stats" class="menu-card purple">
            <div class="mc-icon">📈</div>
            <div class="mc-title">Estadísticas</div>
            <div class="mc-desc">Análisis y métricas de asistencia</div>
        </a>
        <a href="?action=reports" class="menu-card purple">
            <div class="mc-icon">📄</div>
            <div class="mc-title">Reportes</div>
            <div class="mc-desc">Generar informes PDF y Excel</div>
        </a>
        <a href="?action=users" class="menu-card blue">
            <div class="mc-icon">👥</div>
            <div class="mc-title">Gestión de Usuarios</div>
            <div class="mc-desc">Administrar usuarios y roles</div>
        </a>
        <a href="?action=academic" class="menu-card teal">
            <div class="mc-icon">🏫</div>
            <div class="mc-title">Configuración Académica</div>
            <div class="mc-desc">Cursos, materias, matrículas</div>
        </a>
        <a href="?action=assignments" class="menu-card green">
            <div class="mc-icon">📚</div>
            <div class="mc-title">Asignaciones Docentes</div>
            <div class="mc-desc">Gestionar docentes y tutores</div>
        </a>
        <a href="?action=manage_representatives" class="menu-card orange">
            <div class="mc-icon">👨‍👩‍👧‍👦</div>
            <div class="mc-title">Gestión Representantes</div>
            <div class="mc-desc">Asignar representantes a estudiantes</div>
        </a>
        <a href="?action=backups" class="menu-card dark">
            <div class="mc-icon">💾</div>
            <div class="mc-title">Respaldos</div>
            <div class="mc-desc">Gestionar respaldos de base de datos</div>
        </a>
        <?php endif; ?>

        <?php if(Security::hasRole('representante')): ?>
        <a href="?action=my_children" class="menu-card purple">
            <div class="mc-icon">👨‍👩‍👧‍👦</div>
            <div class="mc-title">Mis Representados</div>
            <div class="mc-desc">Ver estudiantes y su asistencia</div>
        </a>
        <?php endif; ?>

    </div>
</div>

<style>
/* Menu cards del dashboard */
.menu-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-left: 4px solid #007bff;
    border-radius: 8px;
    padding: 20px;
    text-decoration: none;
    color: #333;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}
.menu-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    text-decoration: none;
}
.menu-card .mc-icon  { font-size: 1.8rem; margin-bottom: 8px; }
.menu-card .mc-title { font-size: 0.95rem; font-weight: 700; margin-bottom: 4px; }
.menu-card .mc-desc  { font-size: 0.8rem; color: #888; }

.menu-card.blue   { border-left-color: #007bff; } .menu-card.blue .mc-title   { color: #007bff; }
.menu-card.green  { border-left-color: #28a745; } .menu-card.green .mc-title  { color: #28a745; }
.menu-card.orange { border-left-color: #fd7e14; } .menu-card.orange .mc-title { color: #fd7e14; }
.menu-card.purple { border-left-color: #6f42c1; } .menu-card.purple .mc-title { color: #6f42c1; }
.menu-card.teal   { border-left-color: #17a2b8; } .menu-card.teal .mc-title   { color: #17a2b8; }
.menu-card.dark   { border-left-color: #343a40; } .menu-card.dark .mc-title   { color: #343a40; }
</style>

</body>
</html>