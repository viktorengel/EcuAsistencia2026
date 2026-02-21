<?php
// Detecta automáticamente si estás en local o producción
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    // LOCAL
    define('BASE_PATH', __DIR__ . '/..');
    define('BASE_URL',  'http://localhost/ecuasistencia2026/public');
} else {
    // PRODUCCIÓN
    define('BASE_PATH', '/home/ecuasysc/ecuasistencia');
    define('BASE_URL',  'https://www.ecuasys.com');
}