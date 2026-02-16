<?php
session_start();

// Zona horaria Ecuador
date_default_timezone_set('America/Guayaquil');
//define('BASE_PATH', dirname(__DIR__));
define('BASE_PATH', __DIR__ . '/..');
define('BASE_URL', 'http://localhost/ecuasistencia2026');
define('EDIT_ATTENDANCE_HOURS', 48);

// SMTP Config
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'viktorengel@gmail.com');
define('SMTP_PASS', 'Orktvi.5/*83gM');
define('SMTP_FROM', 'noreply@ecuasist.edu.ec');
define('SMTP_NAME', 'EcuAsist2026');

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/helpers/Security.php';

// Sesión dura 24 horas
ini_set('session.gc_maxlifetime', 86400);

// Cookie de sesión dura 24 horas (no expira al cerrar navegador)
session_set_cookie_params([
    'lifetime' => 86400,  // 24 horas
    'path' => '/',
    'domain' => '',
    'secure' => false,    // true si usas HTTPS
    'httponly' => true,   // Protección XSS
    'samesite' => 'Lax'   // Protección CSRF
]);

session_start();

// Timeout de INACTIVIDAD: 30 minutos
$inactive_timeout = 1800; // 30 minutos en segundos

if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity'] > $inactive_timeout)) {
    session_unset();
    session_destroy();
    header('Location: ?action=login&timeout=1');
    exit;
}

$_SESSION['last_activity'] = time();