<?php
// public/img.php — Sirve imágenes desde uploads/ (fuera del document root)
// Depende de env.php para obtener BASE_PATH correcto en local y producción

require_once __DIR__ . '/../config/env.php';

$uploadsDir = realpath(BASE_PATH . '/uploads');

if (!$uploadsDir) {
    http_response_code(404); exit;
}

$file = $_GET['f'] ?? '';

// Seguridad: bloquear path traversal
$file = str_replace(['..', '\\', "\0"], '', $file);
$file = ltrim($file, '/');

if (!$file) { http_response_code(400); exit; }

$fullPath = $uploadsDir . DIRECTORY_SEPARATOR . $file;
$realFull = realpath($fullPath);

// Verificar que el archivo está dentro de uploads/
if (!$realFull || strpos($realFull, $uploadsDir) !== 0) {
    http_response_code(403); exit;
}

if (!file_exists($realFull) || !is_file($realFull)) {
    http_response_code(404); exit;
}

$ext = strtolower(pathinfo($realFull, PATHINFO_EXTENSION));
$mimes = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'webp' => 'image/webp',
    'pdf'  => 'application/pdf',
];

if (!isset($mimes[$ext])) { http_response_code(403); exit; }

header('Content-Type: ' . $mimes[$ext]);
header('Content-Length: ' . filesize($realFull));
header('Cache-Control: public, max-age=86400');
if ($ext === 'pdf') {
    header('Content-Disposition: inline; filename="' . basename($realFull) . '"');
}
readfile($realFull);
exit;