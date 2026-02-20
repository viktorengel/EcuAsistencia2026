<?php
// views/partials/navbar.php

$current_page = $_GET['action'] ?? 'dashboard';

// Contar notificaciones no leídas
$_notifCount = 0;
if (isset($_SESSION['user_id'])) {
    require_once BASE_PATH . '/models/Notification.php';
    $_notifDb    = new Database();
    $_notifModel = new Notification($_notifDb);
    $_notifCount = $_notifModel->getUnreadCount($_SESSION['user_id']);
}
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

:root {
    --nav-bg:      #0f172a;
    --nav-accent:  #3b82f6;
    --nav-hover:   rgba(59,130,246,0.12);
    --nav-text:    #e2e8f0;
    --nav-muted:   #94a3b8;
    --nav-border:  rgba(255,255,255,0.07);
    --nav-dd-bg:   #1e293b;
    --nav-dd-hover:#263348;
    --nav-height:  52px;
}

*, *::before, *::after { box-sizing: border-box; }

.ec-nav {
    background: var(--nav-bg);
    height: var(--nav-height);
    display: flex;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 1px 0 var(--nav-border), 0 4px 20px rgba(0,0,0,0.35);
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}

.ec-nav__inner {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
    width: 100%;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* ── Brand ───────────────────────────────────── */
.ec-brand {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    margin-right: 12px;
    flex-shrink: 0;
}
.ec-brand__logo {
    width: 28px;
    height: 28px;
    background: var(--nav-accent);
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 800;
    color: white;
    letter-spacing: -0.5px;
}
.ec-brand__name {
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.3px;
    white-space: nowrap;
}
.ec-brand__name span { color: var(--nav-accent); }

/* ── Menu items ──────────────────────────────── */
.ec-menu {
    display: flex;
    align-items: center;
    gap: 1px;
    flex: 1;
}

.ec-item {
    position: relative;
}

.ec-item__btn {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 0 10px;
    height: var(--nav-height);
    color: var(--nav-text);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    cursor: pointer;
    border: none;
    background: none;
    transition: background 0.15s, color 0.15s;
    border-radius: 0;
}
.ec-item__btn:hover,
.ec-item:hover > .ec-item__btn {
    background: var(--nav-hover);
    color: #fff;
}
.ec-item__btn.active {
    color: var(--nav-accent);
}
.ec-item__btn.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 8px;
    right: 8px;
    height: 2px;
    background: var(--nav-accent);
    border-radius: 2px 2px 0 0;
}

.ec-item__icon { font-size: 14px; line-height: 1; }
.ec-item__caret {
    font-size: 9px;
    color: var(--nav-muted);
    margin-left: 1px;
    transition: transform 0.2s;
}
.ec-item:hover .ec-item__caret { transform: rotate(180deg); }

/* ── Dropdown ────────────────────────────────── */
.ec-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 2px);
    left: 0;
    min-width: 210px;
    background: var(--nav-dd-bg);
    border: 1px solid var(--nav-border);
    border-radius: 10px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    overflow: hidden;
    padding: 4px;
}
.ec-item:hover .ec-dropdown { display: block; }

.ec-dropdown a {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 12px;
    color: var(--nav-text);
    text-decoration: none;
    font-size: 13px;
    border-radius: 7px;
    transition: background 0.12s;
    border-left: 2px solid transparent;
}
.ec-dropdown a:hover { background: var(--nav-dd-hover); color: #fff; }
.ec-dropdown a.active {
    background: rgba(59,130,246,0.15);
    color: #93c5fd;
    border-left-color: var(--nav-accent);
    font-weight: 600;
}
.ec-dropdown__divider {
    height: 1px;
    background: var(--nav-border);
    margin: 4px 8px;
}

/* ── Right side ──────────────────────────────── */
.ec-right {
    display: flex;
    align-items: center;
    gap: 2px;
    margin-left: auto;
    flex-shrink: 0;
}

.ec-icon-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    color: var(--nav-text);
    text-decoration: none;
    font-size: 16px;
    transition: background 0.15s;
}
.ec-icon-btn:hover { background: var(--nav-hover); color: #fff; }

.ec-notif-badge {
    position: absolute;
    top: 3px;
    right: 3px;
    background: #ef4444;
    color: #fff;
    border-radius: 50%;
    min-width: 16px;
    height: 16px;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--nav-bg);
    line-height: 1;
}

.ec-user-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.ec-user-btn {
    display: flex;
    align-items: center;
    padding: 4px;
    border-radius: 50%;
    color: var(--nav-text);
    text-decoration: none;
    transition: background 0.15s;
}
.ec-user-btn:hover { background: var(--nav-hover); }
.ec-user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--nav-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
    letter-spacing: 0.5px;
}
.ec-user-tooltip {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: #1e293b;
    color: #e2e8f0;
    font-size: 12px;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: 7px;
    white-space: nowrap;
    box-shadow: 0 4px 16px rgba(0,0,0,0.4);
    border: 1px solid rgba(255,255,255,0.07);
    pointer-events: none;
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}
.ec-user-tooltip::before {
    content: '';
    position: absolute;
    top: -5px;
    right: 10px;
    width: 8px;
    height: 8px;
    background: #1e293b;
    border-left: 1px solid rgba(255,255,255,0.07);
    border-top: 1px solid rgba(255,255,255,0.07);
    transform: rotate(45deg);
}
.ec-user-wrap:hover .ec-user-tooltip { display: block; }

.ec-logout-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 6px 10px;
    border-radius: 8px;
    color: #f87171;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: background 0.15s, color 0.15s;
    white-space: nowrap;
}
.ec-logout-btn:hover { background: rgba(239,68,68,0.12); color: #fca5a5; }
.ec-logout-btn svg { flex-shrink: 0; }

.ec-divider-v {
    width: 1px;
    height: 22px;
    background: var(--nav-border);
    margin: 0 4px;
}

/* ── Breadcrumb global ───────────────────────── */
.breadcrumb {
    padding: 9px 24px;
    background: #fff;
    border-bottom: 1px solid #e8ecf0;
    font-size: 0.82rem;
    color: #94a3b8;
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}
.breadcrumb a { color: #3b82f6; text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }

@media (max-width: 992px) {
    .ec-nav { height: auto; flex-direction: column; align-items: stretch; }
    .ec-nav__inner { flex-wrap: wrap; padding: 8px 16px; gap: 0; }
    .ec-menu { flex-wrap: wrap; }
    .ec-item__btn { height: 40px; }
    .ec-dropdown { position: static; box-shadow: none; border: none; background: rgba(255,255,255,0.05); border-radius: 6px; }
}
</style>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/global.css">

<nav class="ec-nav">
    <div class="ec-nav__inner">

        <!-- Brand -->
        <a href="?action=dashboard" class="ec-brand">
            <div class="ec-brand__logo">EA</div>
            <span class="ec-brand__name">Ecu<span>Asistencia</span></span>
        </a>

        <!-- Menu -->
        <div class="ec-menu">

            <div class="ec-item">
                <a href="?action=dashboard" class="ec-item__btn <?= $current_page === 'dashboard' ? 'active' : '' ?>">
                    <span class="ec-item__icon">🏠</span> Inicio
                </a>
            </div>

            <!-- ASISTENCIA -->
            <?php if(Security::hasRole(['docente','autoridad','inspector','estudiante'])): ?>
            <div class="ec-item">
                <span class="ec-item__btn <?= in_array($current_page,['attendance_register','attendance_view','attendance_calendar','my_attendance','tutor_course_attendance']) ? 'active' : '' ?>">
                    <span class="ec-item__icon">📋</span> Asistencia
                    <span class="ec-item__caret">▼</span>
                </span>
                <div class="ec-dropdown">
                    <?php if(Security::hasRole(['docente','autoridad'])): ?>
                    <a href="?action=attendance_register" class="<?= $current_page==='attendance_register'?'active':'' ?>">
                        📝 Registrar Asistencia
                    </a>
                    <?php endif; ?>
                    <?php if(Security::hasRole('docente')): ?>
                    <a href="?action=tutor_course_attendance" class="<?= $current_page==='tutor_course_attendance'?'active':'' ?>">
                        🎓 Asistencia de Mi Curso
                    </a>
                    <?php endif; ?>
                    <?php if(Security::hasRole(['docente','inspector','autoridad'])): ?>
                    <a href="?action=attendance_view" class="<?= $current_page==='attendance_view'?'active':'' ?>">
                        📊 Ver Asistencias
                    </a>
                    <a href="?action=attendance_calendar" class="<?= $current_page==='attendance_calendar'?'active':'' ?>">
                        📅 Calendario
                    </a>
                    <?php endif; ?>
                    <?php if(Security::hasRole('estudiante')): ?>
                    <a href="?action=my_attendance" class="<?= $current_page==='my_attendance'?'active':'' ?>">
                        📋 Mi Asistencia
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- JUSTIFICACIONES -->
            <?php if(Security::hasRole(['estudiante','autoridad','inspector'])): ?>
            <div class="ec-item">
                <span class="ec-item__btn <?= in_array($current_page,['my_justifications','pending_justifications','reviewed_justifications']) ? 'active' : '' ?>">
                    <span class="ec-item__icon">📝</span> Justificaciones
                    <span class="ec-item__caret">▼</span>
                </span>
                <div class="ec-dropdown">
                    <?php if(Security::hasRole('estudiante')): ?>
                    <a href="?action=my_justifications" class="<?= $current_page==='my_justifications'?'active':'' ?>">
                        📄 Mis Justificaciones
                    </a>
                    <?php endif; ?>
                    <?php if(Security::hasRole(['autoridad','inspector'])): ?>
                    <a href="?action=pending_justifications" class="<?= $current_page==='pending_justifications'?'active':'' ?>">
                        ✅ Revisar Justificaciones
                    </a>
                    <a href="?action=reviewed_justifications" class="<?= $current_page==='reviewed_justifications'?'active':'' ?>">
                        📋 Historial Revisadas
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- ADMINISTRACIÓN -->
            <?php if(Security::hasRole('autoridad')): ?>
            <div class="ec-item">
                <span class="ec-item__btn <?= in_array($current_page,['institution','academic','users','assignments','tutor_management','manage_representatives','schedules','backups']) ? 'active' : '' ?>">
                    <span class="ec-item__icon">⚙️</span> Administración
                    <span class="ec-item__caret">▼</span>
                </span>
                <div class="ec-dropdown">
                    <a href="?action=institution" class="<?= $current_page==='institution'?'active':'' ?>">
                        🏢 Configuración General
                    </a>
                    <a href="?action=academic" class="<?= $current_page==='academic'?'active':'' ?>">
                        🏫 Configuración Académica
                    </a>
                    <a href="?action=users" class="<?= $current_page==='users'?'active':'' ?>">
                        👥 Gestión de Usuarios
                    </a>
                    <div class="ec-dropdown__divider"></div>
                    <a href="?action=assignments" class="<?= $current_page==='assignments'?'active':'' ?>">
                        📚 Asignaciones Docente-Materia
                    </a>
                    <a href="?action=tutor_management" class="<?= $current_page==='tutor_management'?'active':'' ?>">
                        👨‍🏫 Asignación de Tutores
                    </a>
                    <a href="?action=manage_representatives" class="<?= $current_page==='manage_representatives'?'active':'' ?>">
                        👨‍👩‍👧‍👦 Representantes
                    </a>
                    <div class="ec-dropdown__divider"></div>
                    <a href="?action=schedules" class="<?= $current_page==='schedules'?'active':'' ?>">
                        📅 Horarios de Clases
                    </a>
                    <a href="?action=backups" class="<?= $current_page==='backups'?'active':'' ?>">
                        💾 Respaldos del Sistema
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- REPORTES -->
            <?php if(Security::hasRole('autoridad')): ?>
            <div class="ec-item">
                <span class="ec-item__btn <?= in_array($current_page,['reports','stats']) ? 'active' : '' ?>">
                    <span class="ec-item__icon">📊</span> Reportes
                    <span class="ec-item__caret">▼</span>
                </span>
                <div class="ec-dropdown">
                    <a href="?action=reports" class="<?= $current_page==='reports'?'active':'' ?>">
                        📄 Generar Reportes
                    </a>
                    <a href="?action=stats" class="<?= $current_page==='stats'?'active':'' ?>">
                        📈 Estadísticas
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- MIS REPRESENTADOS -->
            <?php if(Security::hasRole('representante')): ?>
            <div class="ec-item">
                <a href="?action=my_children" class="ec-item__btn <?= $current_page==='my_children'?'active':'' ?>">
                    <span class="ec-item__icon">👨‍👩‍👧‍👦</span> Mis Representados
                </a>
            </div>
            <?php endif; ?>

        </div><!-- /.ec-menu -->

        <!-- Right: notificaciones + usuario + logout -->
        <div class="ec-right">

            <!-- Campana -->
            <a href="?action=notifications" class="ec-icon-btn" title="Notificaciones">
                🔔
                <?php if ($_notifCount > 0): ?>
                    <span class="ec-notif-badge" id="notif-badge">
                        <?= $_notifCount > 99 ? '99+' : $_notifCount ?>
                    </span>
                <?php else: ?>
                    <span class="ec-notif-badge" id="notif-badge" style="display:none;">0</span>
                <?php endif; ?>
            </a>

            <div class="ec-divider-v"></div>

            <!-- Usuario: iniciales + tooltip apellido, nombre -->
            <div class="ec-user-wrap">
                <a href="?action=profile" class="ec-user-btn">
                    <div class="ec-user-avatar">
                        <?= strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1) . substr($_SESSION['last_name'] ?? '', 0, 1)) ?>
                    </div>
                </a>
                <div class="ec-user-tooltip">
                    <?= htmlspecialchars($_SESSION['last_name'] . ', ' . $_SESSION['first_name']) ?>
                </div>
            </div>

            <!-- Logout -->
            <a href="?action=logout" class="ec-logout-btn" title="Cerrar sesión">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span>Salir</span>
            </a>

        </div>
    </div>
</nav>

<script>
(function() {
    var INTERVAL = 30000;
    var badge = document.getElementById('notif-badge');
    function updateBadge(count) {
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
    function pollNotifications() {
        fetch('?action=notifications_unread_json', { credentials: 'same-origin' })
            .then(function(r) { return r.json(); })
            .then(function(data) { if (typeof data.count !== 'undefined') updateBadge(data.count); })
            .catch(function() {});
    }
    setInterval(pollNotifications, INTERVAL);
})();
</script>