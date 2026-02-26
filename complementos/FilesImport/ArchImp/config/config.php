<?php
// config/config.php
// BASE_PATH y BASE_URL vienen de env.php
require_once __DIR__ . '/env.php';

date_default_timezone_set('America/Guayaquil');

define('EDIT_ATTENDANCE_HOURS', 48);

// Sesión
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params([
    'lifetime' => 86400,
    'path'     => '/',
    'domain'   => '',
    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Timeout 30 minutos
$inactive_timeout = 1800;
if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity'] > $inactive_timeout)) {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . '/?action=login&timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

// SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-password-app');
define('SMTP_FROM', 'noreply@ecuasist.edu.ec');
define('SMTP_NAME', 'EcuAsistencia2026');

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/helpers/Security.php';