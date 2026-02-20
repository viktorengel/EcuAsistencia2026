<?php
require_once BASE_PATH . '/helpers/Security.php';
Security::requireLogin();
if (!Security::hasRole('autoridad')) die('Acceso denegado');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario - EcuAsist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; padding-top: 0; }
        .container { max-width: 860px; }
        .form-section { background: #fff; border: 1px solid #e8ecf0; border-radius: 10px; padding: 22px 26px; margin-bottom: 18px; }
        .form-section h5 { color: #1a237e; border-bottom: 2px solid #e8ecf0; padding-bottom: 10px; margin-bottom: 18px; font-size: 15px; font-weight: 700; }
        .required::after { content: " *"; color: #dc3545; }
        .form-control:focus { border-color: #1a237e; box-shadow: 0 0 0 3px rgba(26,35,126,0.1); }
        .form-control.is-valid  { border-color: #28a745; }
        .form-control.is-invalid { border-color: #dc3545; }
        .feedback-icon { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 14px; pointer-events: none; }
        .input-wrap { position: relative; }
        .field-hint { font-size: 11.5px; color: #888; margin-top: 3px; }
        .field-error { font-size: 12px; color: #dc3545; margin-top: 3px; display: none; }
        .field-error.show { display: block; }
        #passport-row { display: none; }
        .doc-toggle { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .doc-toggle label { margin: 0; font-size: 13px; color: #555; cursor: pointer; user-select: none; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=users">Gestión de Usuarios</a> &rsaquo;
    Crear Usuario
</div>

<div class="container mt-4">

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>⚠ Errores:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="?action=create_user" id="createUserForm" novalidate>

        <!-- ── Datos de Acceso ───────────────────────────────── -->
        <div class="form-section">
            <h5>🔐 Datos de Acceso</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Nombre de Usuario</label>
                    <input type="text" name="username" id="username" class="form-control"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           autocomplete="off" required>
                    <div class="field-hint">Sin espacios ni caracteres especiales</div>
                    <div class="field-error" id="err-username"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Correo Electrónico</label>
                    <div class="input-wrap">
                        <input type="email" name="email" id="email" class="form-control"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               autocomplete="off" required>
                        <span class="feedback-icon" id="icon-email"></span>
                    </div>
                    <div class="field-error" id="err-email"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control"
                           minlength="6" required>
                    <div class="field-hint">Mínimo 6 caracteres</div>
                    <div class="field-error" id="err-password"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Confirmar Contraseña</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                           minlength="6" required>
                    <div class="field-error" id="err-confirm"></div>
                </div>
            </div>
        </div>

        <!-- ── Datos Personales ──────────────────────────────── -->
        <div class="form-section">
            <h5>👨‍💼 Datos Personales</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Nombres</label>
                    <input type="text" name="first_name" class="form-control"
                           value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Apellidos</label>
                    <input type="text" name="last_name" class="form-control"
                           value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                </div>

                <!-- Documento de identidad -->
                <div class="col-12">
                    <div class="doc-toggle">
                        <span style="font-size:13px;font-weight:600;color:#333;">Documento de Identidad</span>
                        <div class="form-check form-switch ms-3">
                            <input class="form-check-input" type="checkbox" id="es_extranjero" onchange="toggleDoc()">
                            <label class="form-check-label" for="es_extranjero">Extranjero (Pasaporte)</label>
                        </div>
                    </div>
                </div>

                <!-- Cédula ecuatoriana -->
                <div class="col-md-6" id="cedula-row">
                    <label class="form-label">Cédula de Identidad</label>
                    <div class="input-wrap">
                        <input type="text" name="dni" id="cedula" class="form-control"
                               value="<?= htmlspecialchars($_POST['dni'] ?? '') ?>"
                               placeholder="0000000000" maxlength="10"
                               inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'')">
                        <span class="feedback-icon" id="icon-cedula"></span>
                    </div>
                    <div class="field-hint">10 dígitos — validación algoritmo Ecuador</div>
                    <div class="field-error" id="err-cedula"></div>
                </div>

                <!-- Pasaporte (extranjero) — oculto por defecto -->
                <div class="col-md-6" id="passport-row" style="display:none;">
                    <label class="form-label">Número de Pasaporte</label>
                    <input type="text" name="passport" id="passport" class="form-control"
                           value="<?= htmlspecialchars($_POST['passport'] ?? '') ?>"
                           placeholder="Ej: AB123456" maxlength="20">
                    <div class="field-hint">Alfanumérico, hasta 20 caracteres</div>
                </div>

                <!-- Teléfono -->
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <div class="input-wrap">
                        <input type="text" name="phone" id="phone" class="form-control"
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                               placeholder="09XXXXXXXX o 0X-XXXXXXX"
                               maxlength="15" inputmode="tel">
                        <span class="feedback-icon" id="icon-phone"></span>
                    </div>
                    <div class="field-hint">Celular: 09XXXXXXXX · Fijo: 0X-XXXXXXX (7 dígitos locales)</div>
                    <div class="field-error" id="err-phone"></div>
                </div>

            </div>
        </div>

        <!-- ── Roles ────────────────────────────────────────── -->
        <div class="form-section">
            <h5>🎭 Roles del Usuario</h5>
            <p class="text-muted" style="font-size:13px;">Opcional — puedes asignarlos después</p>
            <div class="row">
                <?php foreach ($roles as $role): ?>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="roles[]" value="<?= $role['id'] ?>"
                               id="role_<?= $role['id'] ?>"
                               <?= (isset($_POST['roles']) && in_array($role['id'], $_POST['roles'])) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="role_<?= $role['id'] ?>">
                            <?= ucfirst($role['name']) ?>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ── Botones ───────────────────────────────────────── -->
        <div class="d-flex justify-content-between mb-5">
            <a href="?action=users" class="btn btn-secondary">← Cancelar</a>
            <button type="submit" class="btn btn-primary px-4">✓ Crear Usuario</button>
        </div>

    </form>
</div>

<script>
// ── Validación cédula Ecuador (algoritmo oficial) ──────────────────────────
function validarCedula(cedula) {
    if (!/^\d{10}$/.test(cedula)) return false;
    var prov = parseInt(cedula.substring(0, 2));
    if (prov < 1 || prov > 24) return false;
    var dig = cedula.split('').map(Number);
    var coef = [2,1,2,1,2,1,2,1,2];
    var suma = 0;
    for (var i = 0; i < 9; i++) {
        var r = dig[i] * coef[i];
        suma += r >= 10 ? r - 9 : r;
    }
    var residuo = suma % 10;
    var digVer = residuo === 0 ? 0 : 10 - residuo;
    return digVer === dig[9];
}

// ── Validación teléfono Ecuador ────────────────────────────────────────────
// Celular: 09XXXXXXXX (10 dígitos, empieza en 09)
// Fijo:    0[2-7]XXXXXXX (9 dígitos: código provincia + 7 locales)
function validarTelefono(tel) {
    var limpio = tel.replace(/[\s\-]/g, '');
    if (/^09\d{8}$/.test(limpio)) return { ok: true, tipo: 'Celular' };
    if (/^0[2-7]\d{7}$/.test(limpio)) return { ok: true, tipo: 'Fijo' };
    return { ok: false, tipo: null };
}

// ── Validación email ───────────────────────────────────────────────────────
function validarEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email.trim());
}

// ── Helpers UI ─────────────────────────────────────────────────────────────
function setValid(inputId, iconId, errId) {
    var el = document.getElementById(inputId);
    el.classList.remove('is-invalid'); el.classList.add('is-valid');
    if (iconId) document.getElementById(iconId).textContent = '✅';
    if (errId)  { document.getElementById(errId).textContent = ''; document.getElementById(errId).classList.remove('show'); }
}
function setInvalid(inputId, iconId, errId, msg) {
    var el = document.getElementById(inputId);
    el.classList.remove('is-valid'); el.classList.add('is-invalid');
    if (iconId) document.getElementById(iconId).textContent = '❌';
    if (errId)  { document.getElementById(errId).textContent = msg; document.getElementById(errId).classList.add('show'); }
}
function clearState(inputId, iconId) {
    var el = document.getElementById(inputId);
    el.classList.remove('is-valid','is-invalid');
    if (iconId) document.getElementById(iconId).textContent = '';
}

// ── Evento: cédula ─────────────────────────────────────────────────────────
document.getElementById('cedula').addEventListener('input', function() {
    var v = this.value.trim();
    if (v === '') { clearState('cedula','icon-cedula'); return; }
    if (v.length < 10) {
        setInvalid('cedula','icon-cedula','err-cedula', v.length + '/10 dígitos');
    } else if (validarCedula(v)) {
        setValid('cedula','icon-cedula','err-cedula');
    } else {
        setInvalid('cedula','icon-cedula','err-cedula', 'Cédula inválida — verifica los dígitos');
    }
});

// ── Evento: teléfono ───────────────────────────────────────────────────────
document.getElementById('phone').addEventListener('input', function() {
    var v = this.value.trim();
    if (v === '') { clearState('phone','icon-phone'); return; }
    var res = validarTelefono(v);
    if (res.ok) {
        setValid('phone','icon-phone','err-phone');
        document.getElementById('err-phone').textContent = '✓ ' + res.tipo;
        document.getElementById('err-phone').style.color = '#28a745';
        document.getElementById('err-phone').classList.add('show');
    } else {
        setInvalid('phone','icon-phone','err-phone',
            'Formato inválido. Celular: 09XXXXXXXX · Fijo: 0XXXXXXXX');
        document.getElementById('err-phone').style.color = '#dc3545';
    }
});

// ── Evento: email ──────────────────────────────────────────────────────────
document.getElementById('email').addEventListener('blur', function() {
    var v = this.value.trim();
    if (v === '') { clearState('email','icon-email'); return; }
    if (validarEmail(v)) {
        setValid('email','icon-email','err-email');
    } else {
        setInvalid('email','icon-email','err-email', 'Correo electrónico inválido');
    }
});

// ── Evento: contraseña ─────────────────────────────────────────────────────
document.getElementById('confirm_password').addEventListener('input', function() {
    var p1 = document.getElementById('password').value;
    var p2 = this.value;
    if (p2 === '') { clearState('confirm_password', null); return; }
    if (p1 === p2) {
        setValid('confirm_password', null, 'err-confirm');
    } else {
        setInvalid('confirm_password', null, 'err-confirm', 'Las contraseñas no coinciden');
    }
});

// ── Toggle extranjero ──────────────────────────────────────────────────────
function toggleDoc() {
    var esExtranjero = document.getElementById('es_extranjero').checked;
    document.getElementById('cedula-row').style.display  = esExtranjero ? 'none' : '';
    document.getElementById('passport-row').style.display = esExtranjero ? '' : 'none';
    // Limpiar campo no activo
    if (esExtranjero) {
        document.getElementById('cedula').value = '';
        clearState('cedula','icon-cedula');
        document.getElementById('err-cedula').classList.remove('show');
    } else {
        document.getElementById('passport').value = '';
    }
    // El campo activo no es required, la validación es en submit
}

// ── Validación al submit ───────────────────────────────────────────────────
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    var ok = true;

    // Email
    var email = document.getElementById('email').value.trim();
    if (!validarEmail(email)) {
        setInvalid('email','icon-email','err-email','Correo electrónico inválido'); ok = false;
    }

    // Contraseñas
    var p1 = document.getElementById('password').value;
    var p2 = document.getElementById('confirm_password').value;
    if (p1.length < 6) {
        setInvalid('password', null, 'err-password', 'Mínimo 6 caracteres'); ok = false;
    }
    if (p1 !== p2) {
        setInvalid('confirm_password', null, 'err-confirm', 'Las contraseñas no coinciden'); ok = false;
    }

    // Cédula (solo si no es extranjero y hay valor)
    var esExtranjero = document.getElementById('es_extranjero').checked;
    if (!esExtranjero) {
        var ced = document.getElementById('cedula').value.trim();
        if (ced !== '' && !validarCedula(ced)) {
            setInvalid('cedula','icon-cedula','err-cedula','Cédula inválida — verifica los dígitos'); ok = false;
        }
    }

    // Teléfono (si hay valor)
    var tel = document.getElementById('phone').value.trim();
    if (tel !== '' && !validarTelefono(tel).ok) {
        setInvalid('phone','icon-phone','err-phone',
            'Formato inválido. Celular: 09XXXXXXXX · Fijo: 0XXXXXXXX'); ok = false;
    }

    if (!ok) e.preventDefault();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>