<?php
// Zona horaria Ecuador
date_default_timezone_set('America/Guayaquil');

// Definir constantes ANTES de todo
define('BASE_PATH', __DIR__ . '/..');
define('BASE_URL', 'http://localhost/ecuasistencia2026');
define('EDIT_ATTENDANCE_HOURS', 48);

// Configuración de sesión ANTES de session_start()
ini_set('session.gc_maxlifetime', 86400); // 24 horas
session_set_cookie_params([
    'lifetime' => 86400,  // 24 horas
    'path' => '/',
    'domain' => '',
    'secure' => false,    // true si usas HTTPS
    'httponly' => true,   // Protección XSS
    'samesite' => 'Lax'   // Protección CSRF
]);

// Iniciar sesión DESPUÉS de configurar
session_start();

// Timeout de inactividad: 30 minutos
$inactive_timeout = 1800; // 30 minutos en segundos
if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity'] > $inactive_timeout)) {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . '/public/?action=login&timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

// SMTP Config
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'viktorengel@gmail.com');
define('SMTP_PASS', 'Orktvi.5/*83gM');
define('SMTP_FROM', 'noreply@ecuasist.edu.ec');
define('SMTP_NAME', 'EcuAsist2026');

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/helpers/Security.php';