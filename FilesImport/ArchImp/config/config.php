<?php
session_start();

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost/ecuasistencia2026');
define('EDIT_ATTENDANCE_HOURS', 48);

// SMTP Config
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-password-app');
define('SMTP_FROM', 'noreply@ecuasist.edu.ec');
define('SMTP_NAME', 'EcuAsist 2026');

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/helpers/Security.php';