<?php
// views/tutor/no_tutor.php
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>EcuAsist</title></head>
<body>
<?php include BASE_PATH . '/views/partials/navbar.php'; ?>
<div style="max-width:500px;margin:80px auto;text-align:center;font-family:sans-serif;color:#666;">
    <div style="font-size:3rem;">👨‍🏫</div>
    <h2 style="margin:16px 0 8px;">No eres tutor de ningún curso</h2>
    <p>Actualmente no tienes asignado un curso como tutor en el año lectivo activo.</p>
    <a href="?action=dashboard" style="display:inline-block;margin-top:20px;padding:10px 20px;background:#007bff;color:#fff;border-radius:6px;text-decoration:none;">← Volver al inicio</a>
</div>
</body>
</html>