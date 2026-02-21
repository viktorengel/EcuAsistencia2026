<?php // views/tutor/no_tutor.php ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asistencia de Mi Curso - EcuAsist</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background:#f4f4f4; }
        .container { max-width:1200px; margin:24px auto; padding:0 16px; }
        .page-header { background:linear-gradient(135deg,#1565c0,#1976d2); color:#fff; border-radius:10px; padding:20px 24px; margin-bottom:20px; display:flex; align-items:center; gap:16px; }
        .page-header .ph-icon { font-size:2.2rem; }
        .page-header h1 { font-size:1.3rem; font-weight:700; }
        .page-header p  { font-size:0.85rem; opacity:0.85; margin-top:4px; }
        .empty-state { background:white; border-radius:10px; padding:60px 20px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.08); }
        .empty-state .icon { font-size:3.5rem; margin-bottom:16px; }
        .empty-state h2 { font-size:1.3rem; color:#333; margin-bottom:8px; }
        .empty-state p  { color:#888; font-size:14px; margin-bottom:24px; }
        .btn-back { display:inline-block; padding:10px 22px; background:#007bff; color:#fff; border-radius:6px; text-decoration:none; font-size:14px; }
        .btn-back:hover { background:#0056b3; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Asistencia de Mi Curso
</div>

<div class="container">

    <div class="page-header">
        <div class="ph-icon">🎓</div>
        <div>
            <h1>Asistencia de Mi Curso</h1>
            <p>Vista del docente tutor sobre la asistencia de su curso</p>
        </div>
    </div>

    <div class="empty-state">
        <div class="icon">👨‍🏫</div>
        <h2>No eres tutor de ningún curso</h2>
        <p>Actualmente no tienes asignado un curso como tutor en el año lectivo activo.</p>
        <a href="?action=dashboard" class="btn-back">← Volver al inicio</a>
    </div>

</div>
</body>
</html>