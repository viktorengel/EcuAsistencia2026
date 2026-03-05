<?php
// public/img.php — Sirve imágenes y PDFs desde uploads/
// NO incluir config.php para evitar redirecciones de sesión

if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    $basePath = realpath(__DIR__ . '/..');
} else {
    $basePath = '/home/ecuasysc/ecuasistencia';
}

$file = $_GET['f'] ?? '';

// Seguridad: bloquear path traversal
$file = str_replace(['..', '\\', "\0"], '', $file);
$file = ltrim($file, '/');

if (!$file) { http_response_code(400); exit; }

// f puede venir como "uploads/justifications/x.png" o "justifications/x.png"
if (strpos($file, 'uploads/') === 0) {
    $fullPath = $basePath . '/' . $file;
} else {
    $fullPath = $basePath . '/uploads/' . $file;
}

$realFull   = realpath($fullPath);
$uploadsDir = realpath($basePath . '/uploads');

if (!$realFull || !$uploadsDir || strpos($realFull, $uploadsDir) !== 0) {
    http_response_code(403); exit;
}

if (!is_file($realFull)) {
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
header('Content-Disposition: inline; filename="' . basename($realFull) . '"');
header('Content-Length: ' . filesize($realFull));
header('Cache-Control: public, max-age=86400');
readfile($realFull);
exit;