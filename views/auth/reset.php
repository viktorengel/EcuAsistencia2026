<?php require_once BASE_PATH . '/config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña — EcuAsist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height:100vh; background:linear-gradient(135deg,#1a237e 0%,#3949ab 50%,#1565c0 100%); display:flex; align-items:center; justify-content:center; padding:20px; }
        .card { width:100%; max-width:420px; border:none; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.3); }
        .card-header { background:linear-gradient(135deg,#1b5e20,#2e7d32); color:#fff; border-radius:16px 16px 0 0 !important; padding:28px 30px 22px; text-align:center; }
        .card-header .icon { font-size:42px; margin-bottom:8px; }
        .card-header h1 { font-size:1.3rem; font-weight:800; margin:0; }
        .card-header p  { font-size:13px; opacity:.8; margin:4px 0 0; }
        .card-body { padding:28px 30px; }
        .form-control:focus { border-color:#2e7d32; box-shadow:0 0 0 3px rgba(46,125,50,.12); }
        .input-group .btn { border-color:#ced4da; background:#f8f9fa; color:#555; }
        .btn-save { background:#2e7d32; border-color:#2e7d32; color:#fff; font-weight:600; padding:10px; width:100%; }
        .btn-save:hover { background:#1b5e20; }
        .back-link { text-align:center; margin-top:16px; font-size:13px; }
        .back-link a { color:#1a237e; text-decoration:none; font-weight:600; }
        .req { font-size:11px; color:#888; margin-top:3px; }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <div class="icon">🔑</div>
        <h1>Nueva Contraseña</h1>
        <p>Crea una contraseña segura para tu cuenta</p>
    </div>
    <div class="card-body">

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2" style="font-size:13px;">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($error) && !$user): ?>
            <!-- Token inválido — solo mostrar enlace -->
            <div class="back-link">
                <a href="?action=forgot">← Solicitar nuevo enlace</a>
            </div>
        <?php else: ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:13px;">Nueva contraseña</label>
                <div class="input-group">
                    <input type="password" name="password" id="pass1" class="form-control"
                           placeholder="Mínimo 6 caracteres" minlength="6" required>
                    <button type="button" class="btn" onclick="togglePass('pass1',this)">👁</button>
                </div>
                <div class="req">Mínimo 6 caracteres</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Confirmar contraseña</label>
                <div class="input-group">
                    <input type="password" name="password_confirm" id="pass2" class="form-control"
                           placeholder="Repite la contraseña" minlength="6" required>
                    <button type="button" class="btn" onclick="togglePass('pass2',this)">👁</button>
                </div>
                <div id="match-msg" class="req"></div>
            </div>

            <button type="submit" class="btn-save btn" id="btn-save">✓ Guardar nueva contraseña</button>
        </form>

        <div class="back-link"><a href="?action=login">← Volver al inicio de sesión</a></div>

        <?php endif; ?>
    </div>
</div>

<script>
function togglePass(id, btn) {
    var el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
    btn.textContent = el.type === 'password' ? '👁' : '🙈';
}
document.getElementById('pass2').addEventListener('input', function() {
    var p1 = document.getElementById('pass1').value;
    var msg = document.getElementById('match-msg');
    if (this.value === '') { msg.textContent = ''; return; }
    if (p1 === this.value) {
        msg.textContent = '✓ Las contraseñas coinciden';
        msg.style.color = '#28a745';
    } else {
        msg.textContent = '✗ Las contraseñas no coinciden';
        msg.style.color = '#dc3545';
    }
});
</script>
</body>
</html>