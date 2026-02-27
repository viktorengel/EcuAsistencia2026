<?php
// views/partials/navbar.php

$current_page = $_GET['action'] ?? 'dashboard';

$_notifCount = 0;
if (isset($_SESSION['user_id'])) {
    require_once BASE_PATH . '/models/Notification.php';
    $_notifDb    = new Database();
    $_notifModel = new Notification($_notifDb);
    $_notifCount = $_notifModel->getUnreadCount($_SESSION['user_id']);
}

// Determinar qué sección está activa para auto-expandirla
$_activeSection = '';
if (in_array($current_page, ['attendance_register','attendance_view','attendance_calendar','my_attendance','tutor_course_attendance'])) $_activeSection = 'asistencia';
elseif (in_array($current_page, ['my_justifications','pending_justifications','reviewed_justifications','tutor_pending_justifications'])) $_activeSection = 'justificaciones';
elseif (in_array($current_page, ['institution','academic','users','assignments','tutor_management','manage_representatives','link_requests','schedules','backups'])) $_activeSection = 'administracion';
elseif (in_array($current_page, ['reports','stats'])) $_activeSection = 'reportes';
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

:root {
    --nav-bg:      #1e40af;
    --nav-accent:  #60a5fa;
    --nav-hover:   rgba(96,165,250,0.18);
    --nav-text:    #e2e8f0;
    --nav-muted:   #bfdbfe;
    --nav-border:  rgba(255,255,255,0.12);
    --nav-dd-bg:   #1d4ed8;
    --nav-dd-hover:#2563eb;
    --nav-height:  52px;
}
*, *::before, *::after { box-sizing: border-box; }

/* ── Barra ──────────────────────────────────── */
.ec-nav {
    background: var(--nav-bg);
    height: var(--nav-height);
    display: flex; align-items: center;
    position: sticky; top: 0; z-index: 1000;
    box-shadow: 0 1px 0 var(--nav-border), 0 4px 20px rgba(0,0,0,0.25);
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}
.ec-nav__inner {
    max-width: 1400px; margin: 0 auto;
    padding: 0 20px; width: 100%;
    display: flex; align-items: center; gap: 4px;
}

/* ── Brand ──────────────────────────────────── */
.ec-brand { display: flex; align-items: center; gap: 8px; text-decoration: none; margin-right: 12px; flex-shrink: 0; }
.ec-brand__logo {
    width: 28px; height: 28px; background: #fff; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 800; color: var(--nav-bg); letter-spacing: -0.5px;
}
.ec-brand__name { font-size: 14px; font-weight: 700; color: #fff; letter-spacing: -0.3px; white-space: nowrap; }
.ec-brand__name span { color: #bfdbfe; }

/* ── Hamburger ──────────────────────────────── */
.ec-hamburger {
    display: none;
    flex-direction: column; justify-content: center; align-items: center;
    width: 38px; height: 38px; border-radius: 8px;
    cursor: pointer; border: none; background: none;
    gap: 5px; flex-shrink: 0; padding: 0;
}
.ec-hamburger span {
    display: block; width: 20px; height: 2px;
    background: #fff; border-radius: 2px;
    transition: transform 0.25s, opacity 0.2s; transform-origin: center;
}
.ec-hamburger:hover { background: var(--nav-hover); }
.ec-hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.ec-hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
.ec-hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* ── Menú desktop ───────────────────────────── */
.ec-menu { display: flex; align-items: center; gap: 1px; flex: 1; }
.ec-item { position: relative; }
.ec-item__btn {
    display: flex; align-items: center; gap: 5px;
    padding: 0 10px; height: var(--nav-height);
    color: var(--nav-text); text-decoration: none;
    font-size: 13px; font-weight: 500; white-space: nowrap;
    cursor: pointer; border: none; background: none;
    transition: background 0.15s, color 0.15s; position: relative;
}
.ec-item__btn:hover, .ec-item:hover > .ec-item__btn { background: var(--nav-hover); color: #fff; }
.ec-item__btn.active { color: #fff; background: rgba(255,255,255,0.15); border-radius: 6px; }
.ec-item__icon  { font-size: 14px; line-height: 1; }
.ec-item__caret { font-size: 9px; color: var(--nav-muted); margin-left: 1px; transition: transform 0.2s; }
.ec-item:hover .ec-item__caret { transform: rotate(180deg); }

/* ── Dropdown desktop ───────────────────────── */
.ec-dropdown {
    display: none; position: absolute;
    top: 100%; left: 0; min-width: 210px;
    background: var(--nav-dd-bg); border: 1px solid var(--nav-border);
    border-radius: 0 0 10px 10px; box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    padding: 4px; z-index: 200;
    /* Puente invisible para no perder hover al bajar el mouse */
    margin-top: 0;
}
.ec-item:hover .ec-dropdown { display: block; }
/* Puente invisible que cubre el espacio entre btn y dropdown */
.ec-item__btn::before {
    content: ''; position: absolute;
    bottom: -8px; left: 0; right: 0; height: 8px;
    background: transparent;
}
.ec-dropdown a {
    display: flex; align-items: center; gap: 9px; padding: 9px 12px;
    color: var(--nav-text); text-decoration: none; font-size: 13px;
    border-radius: 7px; transition: background 0.12s; border-left: 2px solid transparent;
}
.ec-dropdown a:hover { background: var(--nav-dd-hover); color: #fff; }
.ec-dropdown a.active { background: rgba(96,165,250,0.2); color: #fff; border-left-color: #fff; font-weight: 600; }
.ec-dropdown__divider { height: 1px; background: var(--nav-border); margin: 4px 8px; }

/* ── Right desktop ──────────────────────────── */
.ec-right { display: flex; align-items: center; gap: 2px; margin-left: auto; flex-shrink: 0; }
.ec-icon-btn {
    position: relative; display: flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: 8px;
    color: var(--nav-text); text-decoration: none; font-size: 16px; transition: background 0.15s;
}
.ec-icon-btn:hover { background: var(--nav-hover); color: #fff; }
.ec-notif-badge {
    position: absolute; top: 3px; right: 3px;
    background: #ef4444; color: #fff; border-radius: 50%;
    min-width: 16px; height: 16px; font-size: 10px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid var(--nav-bg); line-height: 1;
}
.ec-user-wrap { position: relative; display: flex; align-items: center; }
.ec-user-btn  { display: flex; align-items: center; padding: 4px; border-radius: 50%; text-decoration: none; transition: background 0.15s; }
.ec-user-btn:hover { background: var(--nav-hover); }
.ec-user-avatar {
    width: 30px; height: 30px; border-radius: 50%;
    background: rgba(255,255,255,0.22);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0;
    border: 1.5px solid rgba(255,255,255,0.4);
}
.ec-user-tooltip {
    display: none; position: absolute; top: calc(100% + 8px); right: 0;
    background: #1e3a8a; color: #e2e8f0; font-size: 12px; font-weight: 500;
    padding: 6px 12px; border-radius: 7px; white-space: nowrap;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.12);
    pointer-events: none; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; z-index: 300;
}
.ec-user-tooltip::before {
    content: ''; position: absolute; top: -5px; right: 10px;
    width: 8px; height: 8px; background: #1e3a8a;
    border-left: 1px solid rgba(255,255,255,0.12);
    border-top: 1px solid rgba(255,255,255,0.12);
    transform: rotate(45deg);
}
.ec-user-wrap:hover .ec-user-tooltip { display: block; }
.ec-logout-btn {
    display: flex; align-items: center; gap: 5px; padding: 6px 10px; border-radius: 8px;
    color: #fca5a5; text-decoration: none; font-size: 13px; font-weight: 500;
    transition: background 0.15s, color 0.15s; white-space: nowrap;
}
.ec-logout-btn:hover { background: rgba(239,68,68,0.15); color: #fecaca; }
.ec-divider-v { width: 1px; height: 22px; background: var(--nav-border); margin: 0 4px; }

/* ── Breadcrumb ─────────────────────────────── */
.breadcrumb {
    padding: 9px 24px; background: #fff;
    border-bottom: 1px solid #e8ecf0; font-size: 0.82rem; color: #94a3b8;
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}
.breadcrumb a { color: #3b82f6; text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }

/* ══════════════════════════════════════════════
   DRAWER MÓVIL — acordeón
══════════════════════════════════════════════ */

/* Strip de usuario */
.ec-mob-user {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--nav-border);
    background: rgba(0,0,0,0.15);
}
.ec-mob-user__avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #fff;
    border: 2px solid rgba(255,255,255,0.3); flex-shrink: 0;
}
.ec-mob-user__name { font-size: 13px; font-weight: 600; color: #fff; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ec-mob-user__actions { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }

/* Ítem directo (sin sub-menú) */
.ec-mob-direct {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 20px;
    color: var(--nav-text); text-decoration: none;
    font-size: 14px; font-weight: 500;
    border-left: 3px solid transparent;
    transition: background 0.12s, border-color 0.12s;
}
.ec-mob-direct:hover  { background: var(--nav-hover); color: #fff; }
.ec-mob-direct.active { background: rgba(255,255,255,0.12); color: #fff; border-left-color: #fff; font-weight: 600; }
.ec-mob-direct__icon  { font-size: 18px; flex-shrink: 0; }

/* ── Acordeón ───────────────────────────────── */
.ec-acc {
    border-bottom: 1px solid rgba(255,255,255,0.07);
}

/* Cabecera del acordeón */
.ec-acc__head {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 20px;
    color: var(--nav-muted);
    font-size: 13px; font-weight: 600;
    letter-spacing: 0.01em;
    cursor: pointer;
    user-select: none;
    transition: background 0.12s, color 0.12s;
    list-style: none;
}
.ec-acc__head:hover { background: var(--nav-hover); color: #fff; }
.ec-acc__head.has-active { color: #fff; }
.ec-acc__head__icon   { font-size: 18px; flex-shrink: 0; }
.ec-acc__head__label  { flex: 1; }
.ec-acc__head__arrow  {
    font-size: 11px; color: var(--nav-muted);
    transition: transform 0.22s ease;
    margin-left: auto; flex-shrink: 0;
    line-height: 1;
}
.ec-acc.is-open .ec-acc__head__arrow { transform: rotate(180deg); }
.ec-acc.is-open .ec-acc__head { color: #fff; }

/* Cuerpo del acordeón — colapsado por defecto vía JS (style="display:none") */
.ec-acc__body {
    padding: 2px 0 6px 0;
    background: rgba(0,0,0,0.1);
}
.ec-acc__link {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 20px 10px 44px;
    color: var(--nav-text); text-decoration: none;
    font-size: 13.5px; font-weight: 400;
    border-left: 3px solid transparent;
    transition: background 0.12s, color 0.12s;
}
.ec-acc__link:hover  { background: var(--nav-hover); color: #fff; }
.ec-acc__link.active {
    background: rgba(255,255,255,0.1);
    color: #fff; border-left-color: var(--nav-accent);
    font-weight: 600;
}
.ec-acc__link__icon { font-size: 16px; flex-shrink: 0; }

/* Logout */
.ec-mob-logout {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 20px;
    color: #fca5a5; text-decoration: none;
    font-size: 14px; font-weight: 500;
    border-top: 1px solid var(--nav-border);
    transition: background 0.12s, color 0.12s;
    margin-top: 4px;
}
.ec-mob-logout:hover { background: rgba(239,68,68,0.12); color: #fecaca; }

/* ── Responsive ─────────────────────────────── */
@media (max-width: 992px) {
    .ec-menu      { display: none !important; }
    .ec-right     { display: none !important; }
    .ec-hamburger { display: flex !important; margin-left: auto; }
}
@media (min-width: 993px) {
    .ec-hamburger { display: none !important; }
}

        /* ── Panel popup notificaciones ── */
        .ec-notif-wrap { position: relative; }
        .ec-notif-panel {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 340px; background: #fff; border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18); z-index: 10000;
            border: 1px solid #e5e7eb; overflow: hidden;
        }
        .ec-notif-panel__head {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 16px; background: #1e3a8a; color: #fff;
            font-size: 13px; font-weight: 700;
        }
        .ec-notif-panel__head a { color: #93c5fd; font-size: 12px; font-weight: 500; text-decoration: none; }
        .ec-notif-panel__body { max-height: 340px; overflow-y: auto; }
        .ec-notif-panel__loading { padding: 20px; text-align: center; color: #999; font-size: 13px; }
        .ec-notif-panel__empty  { padding: 28px 16px; text-align: center; color: #aaa; font-size: 13px; }
        .ec-notif-item {
            display: flex; gap: 10px; align-items: flex-start;
            padding: 12px 16px; border-bottom: 1px solid #f3f4f6;
            cursor: pointer; transition: background 0.15s; text-decoration: none; color: inherit;
        }
        .ec-notif-item:hover { background: #f0f4ff; }
        .ec-notif-item.unread { background: #f8fbff; border-left: 3px solid #3b82f6; }
        .ec-notif-item.unread:hover { background: #e8f0fe; }
        .ec-notif-item__icon { font-size: 18px; flex-shrink: 0; line-height: 1.2; margin-top: 1px; }
        .ec-notif-item__body { flex: 1; min-width: 0; }
        .ec-notif-item__title { font-size: 12.5px; font-weight: 700; color: #1e3a8a; margin-bottom: 2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ec-notif-item__msg { font-size: 12px; color: #555; line-height: 1.35;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .ec-notif-item__time { font-size: 10.5px; color: #aaa; margin-top: 3px; }
        .ec-notif-item__dot { width: 7px; height: 7px; border-radius: 50%; background: #3b82f6;
            flex-shrink: 0; margin-top: 6px; }
        .ec-notif-panel__foot { padding: 10px 16px; text-align: center; border-top: 1px solid #f0f0f0; }
        .ec-notif-panel__foot a { font-size: 12px; color: #3b82f6; text-decoration: none; font-weight: 600; }
        .ec-notif-panel__foot a:hover { text-decoration: underline; }

        /* ── Modal confirmación global ── */
        .ec-confirm-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.55); z-index: 99999;
            align-items: center; justify-content: center;
        }
        .ec-confirm-overlay.on { display: flex; }
        .ec-confirm-box {
            background: #fff; border-radius: 12px; padding: 28px 28px 22px;
            max-width: 380px; width: 90%; box-shadow: 0 8px 32px rgba(0,0,0,0.22); text-align: center;
        }
        .ec-confirm-icon { font-size: 2.2rem; margin-bottom: 10px; }
        .ec-confirm-title { font-size: 15px; font-weight: 700; color: #111; margin-bottom: 8px; }
        .ec-confirm-msg { font-size: 13px; color: #666; margin-bottom: 22px; line-height: 1.5; }
        .ec-confirm-btns { display: flex; gap: 10px; justify-content: center; }
        .ec-confirm-cancel { padding: 9px 22px; background: #f3f4f6; color: #555; border: none;
            border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .ec-confirm-cancel:hover { background: #e5e7eb; }
        .ec-confirm-ok { padding: 9px 22px; background: #dc3545; color: #fff; border: none;
            border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .ec-confirm-ok:hover { background: #c82333; }
</style>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/global.css">

<nav class="ec-nav">
    <div class="ec-nav__inner">

        <a href="?action=dashboard" class="ec-brand">
            <div class="ec-brand__logo">EA</div>
            <span class="ec-brand__name">Ecu<span>Asistencia</span></span>
        </a>

        <!-- Menú desktop -->
        <div class="ec-menu">
            <div class="ec-item">
                <a href="?action=dashboard" class="ec-item__btn <?= $current_page==='dashboard'?'active':'' ?>">
                    <span class="ec-item__icon">🏠</span> Inicio
                </a>
            </div>

            <?php if(Security::hasRole(['docente','autoridad','inspector','estudiante'])): ?>
            <div class="ec-item">
                <span class="ec-item__btn <?= in_array($current_page,['attendance_register','attendance_view','attendance_calendar','my_attendance','tutor_course_attendance'])?'active':'' ?>">
                    <span class="ec-item__icon">📋</span> Asistencia <span class="ec-item__caret">▼</span>
                </span>
                <div class="ec-dropdown">
                    <?php if(Security::hasRole(['docente','autoridad'])): ?>
                    <a href="?action=attendance_register" class="<?= $current_page==='attendance_register'?'active':'' ?>">📝 Registrar Asistencia</a>
                    <?php endif; ?>
                    <?php if(Security::hasRole('docente')): ?>
                    <a href="?action=tutor_course_attendance" class="<?= $current_page==='tutor_course_attendance'?'active':'' ?>">🎓 Asistencia de Mi Curso</a>
                    <?php endif; ?>
                    <?php if(Security::hasRole(['docente','inspector','autoridad'])): ?>
                    <a href="?action=attendance_view"     class="<?= $current_page==='attendance_view'?'active':'' ?>">📊 Ver Asistencias</a>
                    <a href="?action=attendance_calendar" class="<?= $current_page==='attendance_calendar'?'active':'' ?>">📅 Calendario</a>
                    <?php endif; ?>
                    <?php if(Security::hasRole('estudiante')): ?>
                    <a href="?action=my_attendance" class="<?= $current_page==='my_attendance'?'active':'' ?>">📋 Mi Asistencia</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole(['estudiante','autoridad','inspector','docente'])): ?>
            <div class="ec-item">
                <span class="ec-item__btn <?= in_array($current_page,['my_justifications','pending_justifications','reviewed_justifications','tutor_pending_justifications'])?'active':'' ?>">
                    <span class="ec-item__icon">📝</span> Justificaciones <span class="ec-item__caret">▼</span>
                </span>
                <div class="ec-dropdown">
                    <?php if(Security::hasRole('estudiante')): ?>
                    <a href="?action=my_justifications" class="<?= $current_page==='my_justifications'?'active':'' ?>">📄 Mis Justificaciones</a>
                    <?php endif; ?>
                    <?php if(Security::hasRole(['autoridad','inspector','docente'])): ?>
                    <a href="?action=pending_justifications"  class="<?= $current_page==='pending_justifications'?'active':'' ?>">✅ Revisar Justificaciones</a>
                    <a href="?action=reviewed_justifications" class="<?= $current_page==='reviewed_justifications'?'active':'' ?>">📋 Historial Revisadas</a>
                    <?php endif; ?>
                    <?php if(Security::hasRole('docente') && !empty($_SESSION['is_tutor'])): ?>
                    <a href="?action=tutor_pending_justifications" class="<?= $current_page==='tutor_pending_justifications'?'active':'' ?>">🎓 Justificaciones de mi Curso</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole('autoridad')): ?>
            <div class="ec-item">
                <span class="ec-item__btn <?= in_array($current_page,['institution','academic','users','assignments','tutor_management','manage_representatives','link_requests','schedules','backups'])?'active':'' ?>">
                    <span class="ec-item__icon">⚙️</span> Administración <span class="ec-item__caret">▼</span>
                </span>
                <div class="ec-dropdown">
                    <a href="?action=institution"  class="<?= $current_page==='institution'?'active':'' ?>">🏢 Configuración General</a>
                    <a href="?action=academic"     class="<?= $current_page==='academic'?'active':'' ?>">🏫 Configuración Académica</a>
                    <a href="?action=users"        class="<?= $current_page==='users'?'active':'' ?>">👥 Gestión de Usuarios</a>
                    <div class="ec-dropdown__divider"></div>
                    <a href="?action=manage_representatives" class="<?= $current_page==='manage_representatives'?'active':'' ?>">👨‍👩‍👧‍👦 Representantes</a>
                    <a href="?action=link_requests" class="<?= $current_page==='link_requests'?'active':'' ?>">🔗 Solicitudes de Vinculación</a>
                    <div class="ec-dropdown__divider"></div>
                    <a href="?action=schedules" class="<?= $current_page==='schedules'?'active':'' ?>">📅 Horarios de Clases</a>
                    <a href="?action=backups"   class="<?= $current_page==='backups'?'active':'' ?>">💾 Respaldos del Sistema</a>
                </div>
            </div>

            <div class="ec-item">
                <span class="ec-item__btn <?= in_array($current_page,['reports','stats'])?'active':'' ?>">
                    <span class="ec-item__icon">📊</span> Reportes <span class="ec-item__caret">▼</span>
                </span>
                <div class="ec-dropdown">
                    <a href="?action=reports" class="<?= $current_page==='reports'?'active':'' ?>">📄 Generar Reportes</a>
                    <a href="?action=stats"   class="<?= $current_page==='stats'?'active':'' ?>">📈 Estadísticas</a>
                </div>
            </div>
            <?php endif; ?>

            <?php if(Security::hasRole('representante')): ?>
            <div class="ec-item">
                <a href="?action=my_children" class="ec-item__btn <?= $current_page==='my_children'?'active':'' ?>">
                    <span class="ec-item__icon">👨‍👩‍👧‍👦</span> Mis Representados
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right desktop -->
        <div class="ec-right">
            <div class="ec-notif-wrap" id="ec-notif-wrap">
                <button class="ec-icon-btn" id="ec-notif-btn" onclick="toggleNotifPanel(event)" title="Notificaciones" style="background:none;border:none;cursor:pointer;">
                    🔔
                    <?php if ($_notifCount > 0): ?>
                        <span class="ec-notif-badge" id="notif-badge"><?= $_notifCount > 99 ? '99+' : $_notifCount ?></span>
                    <?php else: ?>
                        <span class="ec-notif-badge" id="notif-badge" style="display:none;">0</span>
                    <?php endif; ?>
                </button>
                <div class="ec-notif-panel" id="ec-notif-panel" style="display:none;">
                    <div class="ec-notif-panel__head">
                        <span>🔔 Notificaciones</span>
                        <a href="?action=notifications">Ver todas</a>
                    </div>
                    <div class="ec-notif-panel__body" id="ec-notif-body">
                        <div class="ec-notif-panel__loading">Cargando...</div>
                    </div>
                    <div class="ec-notif-panel__foot">
                        <a href="?action=notifications">Ver todas las notificaciones →</a>
                    </div>
                </div>
            </div>
            <div class="ec-divider-v"></div>
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
            <a href="?action=logout" class="ec-logout-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span>Salir</span>
            </a>
        </div>

        <!-- Hamburger -->
        <button class="ec-hamburger" id="ec-hamburger" aria-label="Menú" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

    </div>
</nav>

<!-- ══ DRAWER MÓVIL ══════════════════════════════════════════════════ -->
<div id="ec-mobile-drawer" style="
    display:none;
    position:fixed;
    top:var(--nav-height,52px);
    left:0;right:0;bottom:0;
    background:#1e40af;
    z-index:999;
    overflow-y:auto;
    font-family:'Plus Jakarta Sans',system-ui,sans-serif;
">

    <!-- Usuario -->
    <div class="ec-mob-user">
        <div class="ec-mob-user__avatar">
            <?= strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1) . substr($_SESSION['last_name'] ?? '', 0, 1)) ?>
        </div>
        <span class="ec-mob-user__name"><?= htmlspecialchars($_SESSION['last_name'] . ', ' . $_SESSION['first_name']) ?></span>
        <div class="ec-mob-user__actions">
            <button class="ec-icon-btn" style="position:relative;display:flex;background:none;border:none;cursor:pointer;" onclick="toggleNotifPanel(event)" title="Notificaciones">
                🔔
                <?php if ($_notifCount > 0): ?>
                    <span class="ec-notif-badge" id="notif-badge-mob"><?= $_notifCount > 99 ? '99+' : $_notifCount ?></span>
                <?php endif; ?>
            </button>
            <a href="?action=profile" class="ec-icon-btn" style="display:flex;" title="Mi perfil">👤</a>
        </div>
    </div>

    <!-- Inicio (sin sub-menú) -->
    <a href="?action=dashboard" class="ec-mob-direct <?= $current_page==='dashboard'?'active':'' ?>">
        <span class="ec-mob-direct__icon">🏠</span> Inicio
    </a>

    <!-- ── Asistencia ── -->
    <?php if(Security::hasRole(['docente','autoridad','inspector','estudiante'])): ?>
    <div class="ec-acc" data-section="asistencia">
        <div class="ec-acc__head <?= $_activeSection==='asistencia'?'has-active':'' ?>">
            <span class="ec-acc__head__icon">📋</span>
            <span class="ec-acc__head__label">Asistencia</span>
            <span class="ec-acc__head__arrow">▼</span>
        </div>
        <div class="ec-acc__body" style="display:none;">
            <?php if(Security::hasRole(['docente','autoridad'])): ?>
            <a href="?action=attendance_register"    class="ec-acc__link <?= $current_page==='attendance_register'?'active':'' ?>"><span class="ec-acc__link__icon">📝</span> Registrar Asistencia</a>
            <?php endif; ?>
            <?php if(Security::hasRole('docente')): ?>
            <a href="?action=tutor_course_attendance" class="ec-acc__link <?= $current_page==='tutor_course_attendance'?'active':'' ?>"><span class="ec-acc__link__icon">🎓</span> Asistencia de Mi Curso</a>
            <?php endif; ?>
            <?php if(Security::hasRole(['docente','inspector','autoridad'])): ?>
            <a href="?action=attendance_view"        class="ec-acc__link <?= $current_page==='attendance_view'?'active':'' ?>"><span class="ec-acc__link__icon">📊</span> Ver Asistencias</a>
            <a href="?action=attendance_calendar"    class="ec-acc__link <?= $current_page==='attendance_calendar'?'active':'' ?>"><span class="ec-acc__link__icon">📅</span> Calendario</a>
            <?php endif; ?>
            <?php if(Security::hasRole('estudiante')): ?>
            <a href="?action=my_attendance"          class="ec-acc__link <?= $current_page==='my_attendance'?'active':'' ?>"><span class="ec-acc__link__icon">📋</span> Mi Asistencia</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Justificaciones ── -->
    <?php if(Security::hasRole(['estudiante','autoridad','inspector','docente'])): ?>
    <div class="ec-acc" data-section="justificaciones">
        <div class="ec-acc__head <?= $_activeSection==='justificaciones'?'has-active':'' ?>">
            <span class="ec-acc__head__icon">📝</span>
            <span class="ec-acc__head__label">Justificaciones</span>
            <span class="ec-acc__head__arrow">▼</span>
        </div>
        <div class="ec-acc__body" style="display:none;">
            <?php if(Security::hasRole('estudiante')): ?>
            <a href="?action=my_justifications"      class="ec-acc__link <?= $current_page==='my_justifications'?'active':'' ?>"><span class="ec-acc__link__icon">📄</span> Mis Justificaciones</a>
            <?php endif; ?>
            <?php if(Security::hasRole(['autoridad','inspector','docente'])): ?>
            <a href="?action=pending_justifications"  class="ec-acc__link <?= $current_page==='pending_justifications'?'active':'' ?>"><span class="ec-acc__link__icon">✅</span> Revisar Justificaciones</a>
            <a href="?action=reviewed_justifications" class="ec-acc__link <?= $current_page==='reviewed_justifications'?'active':'' ?>"><span class="ec-acc__link__icon">📋</span> Historial Revisadas</a>
            <?php endif; ?>
            <?php if(Security::hasRole('docente') && !empty($_SESSION['is_tutor'])): ?>
            <a href="?action=tutor_pending_justifications" class="ec-acc__link <?= $current_page==='tutor_pending_justifications'?'active':'' ?>"><span class="ec-acc__link__icon">🎓</span> Justificaciones de mi Curso</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Administración ── -->
    <?php if(Security::hasRole('autoridad')): ?>
    <div class="ec-acc" data-section="administracion">
        <div class="ec-acc__head <?= $_activeSection==='administracion'?'has-active':'' ?>">
            <span class="ec-acc__head__icon">⚙️</span>
            <span class="ec-acc__head__label">Administración</span>
            <span class="ec-acc__head__arrow">▼</span>
        </div>
        <div class="ec-acc__body" style="display:none;">
            <a href="?action=institution"            class="ec-acc__link <?= $current_page==='institution'?'active':'' ?>"><span class="ec-acc__link__icon">🏢</span> Configuración General</a>
            <a href="?action=academic"               class="ec-acc__link <?= $current_page==='academic'?'active':'' ?>"><span class="ec-acc__link__icon">🏫</span> Configuración Académica</a>
            <a href="?action=users"                  class="ec-acc__link <?= $current_page==='users'?'active':'' ?>"><span class="ec-acc__link__icon">👥</span> Gestión de Usuarios</a>
            <a href="?action=manage_representatives" class="ec-acc__link <?= $current_page==='manage_representatives'?'active':'' ?>"><span class="ec-acc__link__icon">👨‍👩‍👧‍👦</span> Representantes</a>
            <a href="?action=link_requests" class="ec-acc__link <?= $current_page==='link_requests'?'active':'' ?>"><span class="ec-acc__link__icon">🔗</span> Solicitudes de Vinculación</a>
            <a href="?action=schedules"              class="ec-acc__link <?= $current_page==='schedules'?'active':'' ?>"><span class="ec-acc__link__icon">📅</span> Horarios de Clases</a>
            <a href="?action=backups"                class="ec-acc__link <?= $current_page==='backups'?'active':'' ?>"><span class="ec-acc__link__icon">💾</span> Respaldos del Sistema</a>
        </div>
    </div>

    <!-- ── Reportes ── -->
    <div class="ec-acc" data-section="reportes">
        <div class="ec-acc__head <?= $_activeSection==='reportes'?'has-active':'' ?>">
            <span class="ec-acc__head__icon">📊</span>
            <span class="ec-acc__head__label">Reportes</span>
            <span class="ec-acc__head__arrow">▼</span>
        </div>
        <div class="ec-acc__body" style="display:none;">
            <a href="?action=reports" class="ec-acc__link <?= $current_page==='reports'?'active':'' ?>"><span class="ec-acc__link__icon">📄</span> Generar Reportes</a>
            <a href="?action=stats"   class="ec-acc__link <?= $current_page==='stats'?'active':'' ?>"><span class="ec-acc__link__icon">📈</span> Estadísticas</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Mis Representados (sin sub-menú) -->
    <?php if(Security::hasRole('representante')): ?>
    <a href="?action=my_children" class="ec-mob-direct <?= $current_page==='my_children'?'active':'' ?>">
        <span class="ec-mob-direct__icon">👨‍👩‍👧‍👦</span> Mis Representados
    </a>
    <?php endif; ?>

    <!-- Logout -->
    <a href="?action=logout" class="ec-mob-logout">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Cerrar Sesión
    </a>

</div><!-- /#ec-mobile-drawer -->

<script>
/* ── Notificaciones: funciones globales ── */
var _nb  = document.getElementById('notif-badge');
var _nbm = document.getElementById('notif-badge-mob');
var _lastNotifCount = <?= $_notifCount ?>;
var _panelOpen = false;
var _notifBase = '<?= rtrim(BASE_URL, "/") ?>/';

function updateBadges(n) {
    [_nb, _nbm].forEach(function(b) {
        if (!b) return;
        if (n > 0) { b.textContent = n > 99 ? '99+' : n; b.style.display = 'flex'; }
        else b.style.display = 'none';
    });
}

function ringBell() {
    var btn = document.getElementById('ec-notif-btn');
    if (!btn) return;
    var i = 0, seq = [8,-8,6,-6,4,-4,0];
    var iv = setInterval(function() {
        btn.style.transform = 'rotate(' + (seq[i]||0) + 'deg)';
        i++;
        if (i >= seq.length) { clearInterval(iv); btn.style.transform = ''; }
    }, 80);
}

var _icons = {info:'📋',success:'✅',warning:'⚠️',danger:'❌',ausente:'📅',justificacion:'📝'};
function _esc(t) {
    return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function _ago(d) {
    var s = Math.floor((Date.now() - new Date(d).getTime()) / 1000);
    if (s < 60) return 'hace ' + s + 's';
    if (s < 3600) return 'hace ' + Math.floor(s/60) + 'min';
    if (s < 86400) return 'hace ' + Math.floor(s/3600) + 'h';
    return 'hace ' + Math.floor(s/86400) + 'd';
}

function renderPanel(data) {
    var body = document.getElementById('ec-notif-body');
    if (!body) return;
    if (!data.notifications || !data.notifications.length) {
        body.innerHTML = '<div class="ec-notif-panel__empty">🔔 Sin notificaciones nuevas</div>';
        return;
    }
    body.innerHTML = data.notifications.map(function(n) {
        var icon = _icons[n.type] || '📋';
        var unread = !parseInt(n.is_read);
        var link = n.link || '?action=notifications';
        return '<a class="ec-notif-item' + (unread ? ' unread' : '') + '" href="' + link + '" onclick="markPanelRead(' + n.id + ',this)">' +
            '<div class="ec-notif-item__icon">' + icon + '</div>' +
            '<div class="ec-notif-item__body">' +
                '<div class="ec-notif-item__title">' + _esc(n.title) + '</div>' +
                '<div class="ec-notif-item__msg">' + _esc(n.message) + '</div>' +
                '<div class="ec-notif-item__time">' + _ago(n.created_at) + '</div>' +
            '</div>' +
            (unread ? '<div class="ec-notif-item__dot"></div>' : '') +
        '</a>';
    }).join('');
}

function markPanelRead(id, el) {
    // Actualizar badge optimistamente antes de navegar
    if (el && el.classList.contains('unread')) {
        var newCount = Math.max(0, _lastNotifCount - 1);
        _lastNotifCount = newCount;
        updateBadges(newCount);
        el.classList.remove('unread');
        var dot = el.querySelector('.ec-notif-item__dot');
        if (dot) dot.remove();
    }
    // Marcar en servidor en background
    fetch(_notifBase + '?action=notifications_mark_read&id=' + id, {credentials:'same-origin'}).catch(function(){});
}

function toggleNotifPanel(e) {
    if (e) e.stopPropagation();
    var panel = document.getElementById('ec-notif-panel');
    if (!panel) { window.location.href = '?action=notifications'; return; }
    _panelOpen = !_panelOpen;
    panel.style.display = _panelOpen ? 'block' : 'none';
    if (_panelOpen) {
        document.getElementById('ec-notif-body').innerHTML = '<div class="ec-notif-panel__loading">Cargando...</div>';
        fetch(_notifBase + '?action=notifications_unread_json', {credentials:'same-origin'})
            .then(function(r){ return r.json(); })
            .then(function(d){ renderPanel(d); updateBadges(d.count); _lastNotifCount = d.count; })
            .catch(function(){ document.getElementById('ec-notif-body').innerHTML = '<div class="ec-notif-panel__empty">Error al cargar</div>'; });
    }
}

document.addEventListener('click', function(e) {
    var wrap = document.getElementById('ec-notif-wrap');
    if (wrap && !wrap.contains(e.target)) {
        var panel = document.getElementById('ec-notif-panel');
        if (panel) { panel.style.display = 'none'; _panelOpen = false; }
    }
});

/* Polling cada 10s */
setInterval(function() {
    if (_panelOpen) return;
    fetch(_notifBase + '?action=notifications_unread_json', {credentials:'same-origin'})
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (typeof d.count === 'undefined') return;
            if (d.count > _lastNotifCount) ringBell();
            _lastNotifCount = d.count;
            updateBadges(d.count);
        }).catch(function(){});
}, 10000);

(function () {
    /* ── Drawer ── */
    var btn    = document.getElementById('ec-hamburger');
    var drawer = document.getElementById('ec-mobile-drawer');
    if (!btn || !drawer) return;

    function openDrawer() {
        drawer.style.display = 'block';
        btn.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        drawer.style.display = 'none';
        btn.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    btn.addEventListener('click', function() {
        drawer.style.display === 'none' || !drawer.style.display ? openDrawer() : closeDrawer();
    });

    /* Cerrar al navegar */
    drawer.querySelectorAll('a').forEach(function(a) {
        a.addEventListener('click', closeDrawer);
    });

    /* Escape */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDrawer();
    });

    /* Cerrar si se pasa a desktop */
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) closeDrawer();
    });

    /* ── Acordeón ── */
    var accs = drawer.querySelectorAll('.ec-acc');
    accs.forEach(function(acc) {
        var head = acc.querySelector('.ec-acc__head');
        var body = acc.querySelector('.ec-acc__body');
        if (!head || !body) return;

        /* Auto-expandir si contiene el ítem activo */
        if (head.classList.contains('has-active')) {
            body.style.display = 'block';
            acc.classList.add('is-open');
        }

        head.addEventListener('click', function() {
            var isOpen = acc.classList.contains('is-open');

            /* Cerrar todos los demás (comportamiento acordeón) */
            accs.forEach(function(other) {
                if (other !== acc) {
                    other.classList.remove('is-open');
                    var ob = other.querySelector('.ec-acc__body');
                    if (ob) ob.style.display = 'none';
                }
            });

            /* Toggle el actual */
            if (isOpen) {
                acc.classList.remove('is-open');
                body.style.display = 'none';
            } else {
                acc.classList.add('is-open');
                body.style.display = 'block';
            }
        });
    });
})();

/* ── Modal confirmación global (reemplaza confirm() nativo) ── */
document.addEventListener('DOMContentLoaded', function() {
    var ov = document.createElement('div');
    ov.className = 'ec-confirm-overlay';
    ov.id = 'ec-confirm-overlay';
    ov.innerHTML =
        '<div class="ec-confirm-box">' +
            '<div class="ec-confirm-icon" id="ec-confirm-icon">⚠️</div>' +
            '<div class="ec-confirm-title" id="ec-confirm-title">¿Estás seguro?</div>' +
            '<div class="ec-confirm-msg" id="ec-confirm-msg"></div>' +
            '<div class="ec-confirm-btns">' +
                '<button class="ec-confirm-cancel" onclick="ecConfirmClose()">Cancelar</button>' +
                '<button class="ec-confirm-ok" id="ec-confirm-ok">Confirmar</button>' +
            '</div>' +
        '</div>';
    document.body.appendChild(ov);
});

function ecConfirm(opts) {
    var ov = document.getElementById('ec-confirm-overlay');
    if (!ov) return;
    document.getElementById('ec-confirm-icon').textContent  = opts.icon    || '⚠️';
    document.getElementById('ec-confirm-title').textContent = opts.title   || '¿Estás seguro?';
    document.getElementById('ec-confirm-msg').textContent   = opts.message || '';
    var ok = document.getElementById('ec-confirm-ok');
    ok.textContent = opts.okText || 'Confirmar';
    ok.onclick = function() { ecConfirmClose(); if (opts.onOk) opts.onOk(); };
    ov.classList.add('on');
}
function ecConfirmClose() {
    var ov = document.getElementById('ec-confirm-overlay');
    if (ov) ov.classList.remove('on');
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') ecConfirmClose(); });

</script>