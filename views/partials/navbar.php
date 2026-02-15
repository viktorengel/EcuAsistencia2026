<?php
$current_page = $_GET['action'] ?? 'dashboard';
?>
<style>
    .main-navbar { background: #007bff; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .navbar-container { max-width: 1400px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
    .navbar-brand { font-size: 24px; font-weight: bold; padding: 15px 0; }
    .navbar-menu { display: flex; gap: 20px; align-items: center; }
    .navbar-section { position: relative; }
    .navbar-section > a { color: white; text-decoration: none; padding: 15px 12px; display: block; cursor: pointer; font-size: 14px; font-weight: 500; }
    .navbar-section:hover > a { background: rgba(255,255,255,0.2); }
    .dropdown { display: none; position: absolute; top: 100%; left: 0; background: white; min-width: 220px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); border-radius: 4px; z-index: 1000; }
    .navbar-section:hover .dropdown { display: block; }
    .dropdown a { color: #333; text-decoration: none; padding: 12px 20px; display: block; transition: background 0.2s; border-left: 3px solid transparent; }
    .dropdown a:hover { background: #f8f9fa; border-left-color: #007bff; }
    .dropdown a.active { background: #e7f3ff; border-left-color: #007bff; font-weight: 600; }
    .dropdown-divider { height: 1px; background: #e0e0e0; margin: 5px 0; }
    .navbar-user { display: flex; align-items: center; gap: 15px; }
    .navbar-user a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; transition: background 0.3s; }
    .navbar-user a:hover { background: rgba(255,255,255,0.2); }
    @media (max-width: 992px) {
        .navbar-container { flex-direction: column; align-items: flex-start; }
        .navbar-menu { width: 100%; flex-direction: column; gap: 0; align-items: stretch; }
        .navbar-section { width: 100%; }
        .navbar-section > a { width: 100%; }
        .dropdown { position: static; width: 100%; box-shadow: none; background: rgba(255,255,255,0.1); }
        .dropdown a { color: white; }
        .dropdown a:hover { background: rgba(255,255,255,0.1); }
    }
</style>

<nav class="main-navbar">
    <div class="navbar-container">
        <div class="navbar-brand">EcuAsist 2026</div>
        
        <div class="navbar-menu">
            <div class="navbar-section">
                <a href="?action=dashboard">🏠 Inicio</a>
            </div>
            
            <!-- ASISTENCIA -->
            <?php if(Security::hasRole(['docente', 'autoridad', 'inspector', 'estudiante'])): ?>
            <div class="navbar-section">
                <a>📋 Asistencia ▾</a>
                <div class="dropdown">
                    <?php if(Security::hasRole(['docente', 'autoridad'])): ?>
                    <a href="?action=attendance_register" class="<?= $current_page == 'attendance_register' ? 'active' : '' ?>">
                        📝 Registrar Asistencia
                    </a>
                    <?php endif; ?>
                    
                    <?php if(Security::hasRole(['docente', 'inspector', 'autoridad'])): ?>
                    <a href="?action=attendance_view" class="<?= $current_page == 'attendance_view' ? 'active' : '' ?>">
                        📊 Ver Asistencias
                    </a>
                    <a href="?action=attendance_calendar" class="<?= $current_page == 'attendance_calendar' ? 'active' : '' ?>">
                        📅 Calendario
                    </a>
                    <?php endif; ?>
                    
                    <?php if(Security::hasRole('estudiante')): ?>
                    <a href="?action=my_attendance" class="<?= $current_page == 'my_attendance' ? 'active' : '' ?>">
                        📋 Mi Asistencia
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- JUSTIFICACIONES -->
            <?php if(Security::hasRole(['estudiante', 'autoridad', 'inspector'])): ?>
            <div class="navbar-section">
                <a>📝 Justificaciones ▾</a>
                <div class="dropdown">
                    <?php if(Security::hasRole('estudiante')): ?>
                    <a href="?action=my_justifications" class="<?= $current_page == 'my_justifications' ? 'active' : '' ?>">
                        📝 Mis Justificaciones
                    </a>
                    <?php endif; ?>
                    
                    <?php if(Security::hasRole(['autoridad', 'inspector'])): ?>
                    <a href="?action=pending_justifications" class="<?= $current_page == 'pending_justifications' ? 'active' : '' ?>">
                        ✅ Revisar Justificaciones
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- ADMINISTRACIÓN -->
            <?php if(Security::hasRole('autoridad')): ?>
            <div class="navbar-section">
                <a>⚙️ Administración ▾</a>
                <div class="dropdown">
                    <a href="?action=users" class="<?= $current_page == 'users' ? 'active' : '' ?>">
                        👥 Gestión de Usuarios
                    </a>
                    <a href="?action=academic" class="<?= $current_page == 'academic' ? 'active' : '' ?>">
                        🏫 Configuración Académica
                    </a>
                    <a href="?action=assignments" class="<?= $current_page == 'assignments' ? 'active' : '' ?>">
                        📚 Asignar Docente-Materia
                    </a>
                    <a href="?action=tutor_management" class="<?= $current_page == 'tutor_management' ? 'active' : '' ?>">
                        👨‍🏫 Asignar Docente Tutor
                    </a>
                    <a href="?action=manage_representatives" class="<?= $current_page == 'manage_representatives' ? 'active' : '' ?>">
                        👨‍👩‍👧‍👦 Gestión Representantes
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="?action=backups" class="<?= $current_page == 'backups' ? 'active' : '' ?>">
                        💾 Respaldos
                    </a>
                    <a href="?action=schedules" class="<?= $current_page == 'schedules' ? 'active' : '' ?>">
                        📅 Horarios de Clases
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- REPORTES Y ESTADÍSTICAS -->
            <?php if(Security::hasRole('autoridad')): ?>
            <div class="navbar-section">
                <a>📊 Reportes ▾</a>
                <div class="dropdown">
                    <a href="?action=reports" class="<?= $current_page == 'reports' ? 'active' : '' ?>">
                        📄 Generar Reportes
                    </a>
                    <a href="?action=stats" class="<?= $current_page == 'stats' ? 'active' : '' ?>">
                        📈 Estadísticas
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- REPRESENTANTES -->
            <?php if(Security::hasRole('representante')): ?>
            <div class="navbar-section">
                <a href="?action=my_children">👨‍👩‍👧‍👦 Mis Representados</a>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="navbar-user">
            <a href="?action=profile">
                👤 <?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] ?>
            </a>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>
</nav>
