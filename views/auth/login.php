<?php
if(isset($_SESSION['user_id'])) {
    header('Location: ?action=dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - EcuAsistencia</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
               background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
               min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #fff; border-radius: 14px; box-shadow: 0 12px 40px rgba(0,0,0,0.2);
                      max-width: 420px; width: 100%; padding: 40px; }
        .login-logo { text-align: center; margin-bottom: 28px; }
        .login-logo .icon { font-size: 3.5rem; margin-bottom: 12px; }
        .login-logo h2 { font-size: 1.5rem; font-weight: 700; color: #333; }
        .login-logo p { color: #888; font-size: 0.88rem; margin-top: 4px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 0.85rem; font-weight: 600; color: #444; }
        .form-group input { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px;
                            font-size: 0.9rem; transition: border-color 0.2s; }
        .form-group input:focus { border-color: #667eea; outline: none; box-shadow: 0 0 0 3px rgba(102,126,234,0.15); }
        .btn-login { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea, #764ba2);
                     color: #fff; border: none; border-radius: 8px; font-size: 0.95rem; font-weight: 600;
                     cursor: pointer; transition: opacity 0.2s; margin-top: 4px; }
        .btn-login:hover { opacity: 0.9; }
        .btn-register { display: block; width: 100%; padding: 11px; margin-top: 10px;
                        background: #fff; color: #667eea; border: 2px solid #667eea;
                        border-radius: 8px; font-size: 0.95rem; font-weight: 600;
                        cursor: pointer; text-align: center; text-decoration: none;
                        transition: all 0.2s; }
        .btn-register:hover { background: #f0f2ff; }
        .alert { padding: 10px 14px; border-radius: 8px; margin-bottom: 18px; font-size: 0.85rem; }
        .alert-danger  { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .login-footer { text-align: center; margin-top: 20px; }
        .login-footer a { color: #667eea; text-decoration: none; font-size: 0.85rem; }
        .login-footer a:hover { text-decoration: underline; }
        .divider { border: none; border-top: 1px solid #f0f0f0; margin: 20px 0; }
        .divider-label { text-align: center; position: relative; margin: 20px 0; }
        .divider-label::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #eee; }
        .divider-label span { background: #fff; padding: 0 12px; color: #bbb; font-size: 0.8rem; position: relative; }
        .remember-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
        .remember-check { display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.85rem; color:#555; }
        .remember-check input[type=checkbox] { display:none; }
        .toggle-switch { position:relative; width:42px; height:24px; background:#ccc; border-radius:12px; cursor:pointer; transition:background .3s; flex-shrink:0; }
        .toggle-switch::after { content:''; position:absolute; width:18px; height:18px; background:#fff; border-radius:50%; top:3px; left:3px; transition:left .3s; box-shadow:0 1px 3px rgba(0,0,0,0.2); }
        .remember-check input:checked + .toggle-switch { background:#0ea5e9; }
        .remember-check input:checked + .toggle-switch::after { left:21px; }
        .toggle-pass { background:none; border:none; cursor:pointer; font-size:1rem; padding:0 4px; color:#888; }
        .input-wrap { position:relative; }
        .input-wrap input { padding-right:38px; }
        .input-wrap .toggle-pass { position:absolute; right:10px; top:50%; transform:translateY(-50%); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <div class="icon">🏫</div>
            <h2 style="font-size:1.8rem;letter-spacing:-0.5px;">
                <span style="color:#0ea5e9;font-weight:900;">Ecu</span><span style="color:#0d9488;font-weight:900;font-size:2.1rem;vertical-align:-3px;">A</span><span style="color:#10b981;font-weight:700;">sistencia</span>
            </h2>
            <p>Sistema de Asistencia Escolar</p>
        </div>

        <?php if(isset($error)): ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if(isset($_GET['registered'])): ?>
        <div class="alert alert-success">✓ Registro exitoso. Ya puedes iniciar sesión.</div>
        <?php endif; ?>

        <?php if(isset($_GET['reset'])): ?>
        <div class="alert alert-success">✓ Contraseña restablecida correctamente.</div>
        <?php endif; ?>

        <form method="POST">
            <?php Security::generateToken(); ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <?php
            $rememberedUser = isset($_COOKIE['ec_remember_user']) ? htmlspecialchars($_COOKIE['ec_remember_user']) : '';
            $rememberedPass = isset($_COOKIE['ec_remember_pass']) ? htmlspecialchars($_COOKIE['ec_remember_pass']) : '';
            $isRemembered   = !empty($rememberedUser);
            ?>

            <div class="form-group">
                <label>👤 Usuario o Correo Electrónico</label>
                <input type="text" name="email" required autofocus
                       placeholder="Ingrese usuario o email"
                       value="<?= $rememberedUser ?>">
            </div>

            <div class="form-group">
                <label>🔒 Contraseña</label>
                <div class="input-wrap">
                    <input type="password" name="password" id="inp_pass" required
                           placeholder="Ingrese contraseña"
                           value="<?= $rememberedPass ?>">
                    <?php if (!$isRemembered): ?>
                    <button type="button" class="toggle-pass" id="btn_toggle_pass" onclick="togglePass()">👁</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="remember-row">
                <label class="remember-check">
                    <input type="checkbox" name="remember_me" value="1" <?= $isRemembered ? 'checked' : '' ?>>
                    <span class="toggle-switch"></span>
                    Recordar mis datos
                </label>
                <a href="?action=forgot" style="font-size:0.85rem;color:#667eea;text-decoration:none;">¿Olvidó su contraseña?</a>
            </div>

            <button type="submit" class="btn-login">🔑 Iniciar Sesión</button>
        </form>

        <div class="login-footer" style="margin-top:16px;"></div>

        <div class="divider-label"><span>¿Eres representante?</span></div>

        <a href="?action=register" class="btn-register">👨‍👩‍👧‍👦 Registrarse como Representante</a>

    </div>
    
    <script>
    function togglePass() {
        var inp = document.getElementById('inp_pass');
        inp.type = inp.type === 'password' ? 'text' : 'password';
    }
    </script>
</body>
</html>