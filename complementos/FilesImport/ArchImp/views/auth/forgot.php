<?php require_once BASE_PATH . '/config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña — EcuAsist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height:100vh; background:linear-gradient(135deg,#1a237e 0%,#3949ab 50%,#1565c0 100%); display:flex; align-items:center; justify-content:center; padding:20px; }
        .card { width:100%; max-width:420px; border:none; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.3); }
        .card-header { background:linear-gradient(135deg,#1a237e,#3949ab); color:#fff; border-radius:16px 16px 0 0 !important; padding:28px 30px 22px; text-align:center; }
        .card-header .icon { font-size:42px; margin-bottom:8px; }
        .card-header h1 { font-size:1.3rem; font-weight:800; margin:0; }
        .card-header p  { font-size:13px; opacity:.8; margin:4px 0 0; }
        .card-body { padding:28px 30px; }
        .form-control:focus { border-color:#1a237e; box-shadow:0 0 0 3px rgba(26,35,126,.12); }
        .btn-reset { background:#1a237e; border-color:#1a237e; color:#fff; font-weight:600; padding:10px; width:100%; }
        .btn-reset:hover { background:#283593; }
        .back-link { text-align:center; margin-top:16px; font-size:13px; }
        .back-link a { color:#1a237e; text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <div class="icon">🔐</div>
        <h1>Recuperar Contraseña</h1>
        <p>Ingresa tu correo y te enviaremos un enlace</p>
    </div>
    <div class="card-body">

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2" style="font-size:13px;">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success py-2" style="font-size:13px;"><?= htmlspecialchars($message) ?></div>
            <div class="back-link"><a href="?action=login">← Volver al inicio de sesión</a></div>
        <?php else: ?>

        <p style="font-size:13px;color:#555;margin-bottom:20px;">
            Escribe el correo con el que te registraste y recibirás un enlace para crear una nueva contraseña.
        </p>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Correo electrónico</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="usuario@correo.com" required autofocus>
            </div>
            <button type="submit" class="btn-reset btn">📧 Enviar enlace de recuperación</button>
        </form>

        <div class="back-link"><a href="?action=login">← Volver al inicio de sesión</a></div>

        <?php endif; ?>
    </div>
</div>
</body>
</html>