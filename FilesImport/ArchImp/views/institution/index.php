<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Institución - EcuAsist</title>
    <style>
        /* ── Jornadas toggle ── */
        .shifts-grid { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 4px; }
        .shift-card {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 20px; border-radius: 10px; cursor: pointer;
            border: 2px solid #e0e0e0; background: #f9f9f9;
            transition: all .2s; user-select: none; min-width: 150px;
        }
        .shift-card:hover { border-color: #90caf9; background: #e3f2fd; }
        .shift-card.active { border-color: #1976d2; background: #e3f2fd; }
        .shift-card .shift-icon { font-size: 22px; }
        .shift-card .shift-info { flex: 1; }
        .shift-card .shift-name { font-weight: 600; font-size: 14px; color: #333; }
        .shift-card .shift-status { font-size: 11px; margin-top: 2px; }
        .shift-card.active .shift-status { color: #1565c0; }
        .shift-card:not(.active) .shift-status { color: #999; }
        .shift-card .shift-toggle {
            width: 36px; height: 20px; border-radius: 10px; position: relative;
            background: #ccc; transition: background .2s; flex-shrink: 0;
        }
        .shift-card.active .shift-toggle { background: #1976d2; }
        .shift-card .shift-toggle::after {
            content: ''; position: absolute; top: 2px; left: 2px;
            width: 16px; height: 16px; border-radius: 50%; background: #fff;
            transition: left .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2);
        }
        .shift-card.active .shift-toggle::after { left: 18px; }
        .shift-saving { opacity: .5; pointer-events: none; }

        /* ── Logo preview ── */
        .logo-wrap { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
        .logo-preview {
            width: 100px; height: 100px; border-radius: 8px; border: 2px dashed #ddd;
            display: flex; align-items: center; justify-content: center;
            background: #fafafa; overflow: hidden; flex-shrink: 0;
        }
        .logo-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .logo-preview .no-logo { font-size: 32px; color: #ccc; }
        .logo-upload-btn {
            display: inline-block; padding: 8px 16px; background: #f0f7ff;
            border: 1.5px solid #90caf9; border-radius: 6px; cursor: pointer;
            font-size: 13px; color: #1565c0; transition: all .2s;
        }
        .logo-upload-btn:hover { background: #e3f2fd; }
        #logo-input { display: none; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Configuración de Institución
</div>

<div class="container">

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ Información actualizada correctamente</div>
    <?php endif; ?>

    <div class="page-header" style="background:linear-gradient(135deg,#1a237e,#283593);">
        <div class="ph-icon">🏫</div>
        <div>
            <h1>Configuración de Institución</h1>
            <p>Datos generales, jornadas y logotipo</p>
        </div>
    </div>

    <form method="POST" action="?action=update_institution" enctype="multipart/form-data" id="instForm">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

            <!-- ── Panel izquierdo: datos ── -->
            <div>
                <div class="panel" style="margin-bottom:16px;">
                    <h3 style="font-size:.95rem;color:#555;margin-bottom:14px;">📋 Datos de la Institución</h3>
                    <div class="form-group">
                        <label>Nombre de la institución *</label>
                        <input type="text" name="name" class="form-control" required
                               value="<?= htmlspecialchars($institution['name'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Código AMIE</label>
                            <input type="text" name="amie_code" class="form-control"
                                   value="<?= htmlspecialchars($institution['amie_code'] ?? '') ?>"
                                   placeholder="17H01988">
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?= htmlspecialchars($institution['phone'] ?? '') ?>"
                                   placeholder="02-2345678">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email institucional</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($institution['email'] ?? '') ?>"
                               placeholder="info@institucion.edu.ec">
                    </div>
                    <div class="form-group">
                        <label>Sitio web</label>
                        <input type="text" name="website" class="form-control" id="website"
                               value="<?= htmlspecialchars($institution['website'] ?? '') ?>"
                               placeholder="www.institucion.edu.ec"
                               onblur="autoHttps(this)">
                    </div>
                    <div class="form-group">
                        <label>Nombre del Director/Rector</label>
                        <input type="text" name="director_name" class="form-control"
                               value="<?= htmlspecialchars($institution['director_name'] ?? '') ?>"
                               placeholder="MSc. Nombre Apellido">
                    </div>
                </div>

                <div class="panel" style="margin-bottom:16px;">
                    <h3 style="font-size:.95rem;color:#555;margin-bottom:14px;">📍 Ubicación</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Provincia</label>
                            <select name="province" id="province" class="form-control" onchange="loadCities()">
                                <option value="">Seleccionar...</option>
                                <?php foreach(getProvinces() as $prov): ?>
                                <option value="<?= $prov ?>" <?= ($institution['province'] ?? '') === $prov ? 'selected' : '' ?>>
                                    <?= $prov ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ciudad</label>
                            <select name="city" id="city" class="form-control">
                                <option value="<?= htmlspecialchars($institution['city'] ?? '') ?>">
                                    <?= htmlspecialchars($institution['city'] ?? 'Seleccionar provincia...') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" name="address" class="form-control"
                               value="<?= htmlspecialchars($institution['address'] ?? '') ?>"
                               placeholder="Av. Principal 123">
                    </div>
                </div>
            </div>

            <!-- ── Panel derecho: logo + jornadas ── -->
            <div>
                <!-- Logo -->
                <div class="panel" style="margin-bottom:16px;">
                    <h3 style="font-size:.95rem;color:#555;margin-bottom:14px;">🖼️ Logo Institucional</h3>
                    <div class="logo-wrap">
                        <div class="logo-preview" id="logoPreview">
                            <?php
                            $logoUrl = '';
                            if (!empty($institution['logo_path'])) {
                                $logoUrl = BASE_URL . '/' . ltrim($institution['logo_path'], '/');
                            }
                            ?>
                            <?php if($logoUrl): ?>
                                <img src="<?= $logoUrl ?>?v=<?= time() ?>" id="logoImg" alt="Logo">
                            <?php else: ?>
                                <span class="no-logo" id="noLogoIcon">🏫</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="logo-input" class="logo-upload-btn">📁 Seleccionar imagen</label>
                            <input type="file" name="logo" id="logo-input" accept=".jpg,.jpeg,.png,.gif,.webp"
                                   onchange="previewLogo(this)">
                            <p style="font-size:12px;color:#999;margin-top:6px;">JPG, PNG o WebP — máx. 2MB</p>
                            <?php if($logoUrl): ?>
                            <p style="font-size:12px;color:#2e7d32;margin-top:4px;">✓ Logo actual cargado</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Jornadas toggle -->
                <div class="panel">
                    <h3 style="font-size:.95rem;color:#555;margin-bottom:6px;">⏰ Jornadas de la Institución</h3>
                    <p style="font-size:12px;color:#999;margin-bottom:14px;">Clic para activar o desactivar cada jornada</p>

                    <?php
                    $shiftIcons = [
                        'matutina'   => ['🌅', 'Mañana'],
                        'vespertina' => ['🌞', 'Tarde'],
                        'nocturna'   => ['🌙', 'Noche'],
                    ];
                    ?>
                    <div class="shifts-grid" id="shiftsGrid">
                        <?php foreach($allShifts as $shift):
                            $isActive = in_array($shift['id'], $assignedShiftIds);
                            $icon     = $shiftIcons[strtolower($shift['name'])][0] ?? '⏰';
                            $timeLabel= $shiftIcons[strtolower($shift['name'])][1] ?? ucfirst($shift['name']);
                        ?>
                        <div class="shift-card <?= $isActive ? 'active' : '' ?>"
                             id="shift-<?= $shift['id'] ?>"
                             onclick="toggleShift(<?= $shift['id'] ?>, this)"
                             title="Clic para <?= $isActive ? 'desactivar' : 'activar' ?>">
                            <span class="shift-icon"><?= $icon ?></span>
                            <div class="shift-info">
                                <div class="shift-name"><?= ucfirst($shift['name']) ?></div>
                                <div class="shift-status"><?= $isActive ? '✓ Activa' : 'Inactiva' ?></div>
                            </div>
                            <div class="shift-toggle"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div id="shift-msg" style="font-size:12px;margin-top:10px;min-height:18px;"></div>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
            <a href="?action=dashboard" class="btn btn-outline">Cancelar</a>
        </div>

    </form>
</div>

<script>
// ── Toggle jornada vía AJAX ──────────────────────────────────
function toggleShift(shiftId, card) {
    card.classList.add('shift-saving');
    var msg = document.getElementById('shift-msg');
    msg.textContent = 'Guardando...';
    msg.style.color = '#999';

    var fd = new FormData();
    fd.append('shift_id', shiftId);

    fetch('?action=toggle_institution_shift', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            card.classList.remove('shift-saving');
            var statusEl = card.querySelector('.shift-status');
            if (data.action === 'assigned') {
                card.classList.add('active');
                statusEl.textContent = '✓ Activa';
                msg.textContent = '✓ Jornada activada';
                msg.style.color = '#2e7d32';
            } else {
                card.classList.remove('active');
                statusEl.textContent = 'Inactiva';
                msg.textContent = '✓ Jornada desactivada';
                msg.style.color = '#f57f17';
            }
            setTimeout(function(){ msg.textContent = ''; }, 2500);
        })
        .catch(function() {
            card.classList.remove('shift-saving');
            msg.textContent = '✗ Error al guardar';
            msg.style.color = '#c62828';
        });
}

// ── Preview logo antes de guardar ───────────────────────────
function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var preview = document.getElementById('logoPreview');
        var noIcon  = document.getElementById('noLogoIcon');
        var img     = document.getElementById('logoImg');
        if (!img) {
            img = document.createElement('img');
            img.id = 'logoImg';
            img.alt = 'Logo';
            preview.innerHTML = '';
            preview.appendChild(img);
        }
        if (noIcon) noIcon.style.display = 'none';
        img.src = e.target.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}

// ── Auto https en website ────────────────────────────────────
function autoHttps(input) {
    var v = input.value.trim();
    if (v && !v.startsWith('http')) {
        input.value = 'https://' + v;
    }
}

// ── Ciudades por provincia ───────────────────────────────────
var cities = {
    'Pichincha':       ['Quito','Cayambe','Mejía','Pedro Moncayo','Rumiñahui','San Miguel de los Bancos'],
    'Guayas':          ['Guayaquil','Daule','Durán','El Triunfo','Milagro','Naranjal','Playas','Samborondón'],
    'Azuay':           ['Cuenca','Gualaceo','Paute','Santa Isabel','Sigsig'],
    'Manabí':          ['Portoviejo','Manta','Bahía de Caráquez','Chone','El Carmen','Jipijapa','Montecristi'],
    'Los Ríos':        ['Babahoyo','Quevedo','Ventanas','Vinces'],
    'El Oro':          ['Machala','Pasaje','Santa Rosa','Zaruma'],
    'Loja':            ['Loja','Catamayo','Macará','Saraguro'],
    'Tungurahua':      ['Ambato','Baños','Pelileo','Píllaro'],
    'Chimborazo':      ['Riobamba','Alausí','Colta','Guano'],
    'Imbabura':        ['Ibarra','Antonio Ante','Cotacachi','Otavalo','Pimampiro'],
    'Cotopaxi':        ['Latacunga','La Maná','Pujilí','Salcedo','Saquisilí'],
    'Bolívar':         ['Guaranda','Chillanes','San Miguel'],
    'Cañar':           ['Azogues','Biblián','Cañar','La Troncal'],
    'Carchi':          ['Tulcán','Bolívar','Espejo','Mira'],
    'Esmeraldas':      ['Esmeraldas','Atacames','La Concordia','Muisne','Quinindé'],
    'Napo':            ['Tena','Archidona','El Chaco'],
    'Pastaza':         ['Puyo','Mera','Santa Clara'],
    'Morona Santiago': ['Macas','Gualaquiza','Limón Indanza','Palora'],
    'Zamora Chinchipe':['Zamora','Chinchipe','Nangaritza','Yantzaza'],
    'Sucumbíos':       ['Nueva Loja','Cascales','Cuyabeno','Lago Agrio'],
    'Orellana':        ['Puerto Francisco de Orellana','Aguarico','La Joya de los Sachas'],
    'Galápagos':       ['Puerto Baquerizo Moreno','Puerto Ayora','Puerto Villamil'],
    'Santo Domingo':   ['Santo Domingo'],
    'Santa Elena':     ['Santa Elena','La Libertad','Salinas'],
};

function loadCities() {
    var prov  = document.getElementById('province').value;
    var sel   = document.getElementById('city');
    var list  = cities[prov] || [];
    sel.innerHTML = '<option value="">Seleccionar ciudad...</option>';
    list.forEach(function(c) {
        var opt = document.createElement('option');
        opt.value = c; opt.textContent = c;
        sel.appendChild(opt);
    });
}

// Cargar ciudades al inicio si hay provincia seleccionada
(function(){
    var prov = document.getElementById('province').value;
    var currentCity = '<?= addslashes($institution['city'] ?? '') ?>';
    if (prov) {
        loadCities();
        var sel = document.getElementById('city');
        for (var i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value === currentCity) {
                sel.selectedIndex = i; break;
            }
        }
    }
})();
</script>

<?php
function getProvinces() {
    return ['Azuay','Bolívar','Cañar','Carchi','Chimborazo','Cotopaxi','El Oro',
            'Esmeraldas','Galápagos','Guayas','Imbabura','Loja','Los Ríos','Manabí',
            'Morona Santiago','Napo','Orellana','Pastaza','Pichincha','Santa Elena',
            'Santo Domingo','Sucumbíos','Tungurahua','Zamora Chinchipe'];
}
?>

</body>
</html>