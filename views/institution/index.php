<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Institución - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Configuración de Institución
</div>

<div class="container">

    <?php
    $msgs = [
        'success'        => '✓ Información actualizada correctamente',
        'shift_assigned' => '✓ Jornada asignada correctamente',
        'shift_removed'  => '✓ Jornada eliminada correctamente',
    ];
    foreach($msgs as $key => $msg):
        if(isset($_GET[$key])): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; endforeach; ?>

    <!-- Header -->
    <div class="page-header" style="background:linear-gradient(135deg,#2d6a4f,#40916c);">
        <div class="ph-icon">🏫</div>
        <div>
            <h1>Configuración de Institución</h1>
            <p>Datos generales, logo y jornadas de la institución</p>
        </div>
    </div>

    <!-- Grid: Formulario | Logo + Jornadas -->
    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:20px;align-items:start;">

        <!-- Formulario datos -->
        <div class="panel">
            <h3 style="margin-bottom:16px;font-size:1rem;">📋 Datos Generales</h3>
            <form method="POST" action="?action=update_institution" enctype="multipart/form-data">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre de la Institución *</label>
                        <input type="text" name="name" class="form-control"
                               value="<?= htmlspecialchars($institution['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Código AMIE</label>
                        <input type="text" name="amie_code" class="form-control"
                               value="<?= htmlspecialchars($institution['amie_code'] ?? '') ?>"
                               placeholder="Ej: 17H00001">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Provincia</label>
                        <select name="province" id="province" class="form-control" onchange="loadCities()">
                            <option value="">Seleccionar provincia...</option>
                            <?php
                            $provincias = [
                                'Pichincha','Guayas','Azuay','Manabí','El Oro','Tungurahua',
                                'Loja','Imbabura','Chimborazo','Cotopaxi','Los Ríos','Bolívar',
                                'Cañar','Carchi','Esmeraldas','Galápagos','Morona Santiago',
                                'Napo','Orellana','Pastaza','Santa Elena','Santo Domingo de los Tsáchilas',
                                'Sucumbíos','Zamora Chinchipe'
                            ];
                            foreach($provincias as $p):
                                $sel = ($institution['province'] ?? '') === $p ? 'selected' : ''; ?>
                                <option value="<?= $p ?>" <?= $sel ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ciudad</label>
                        <select name="city" id="city" class="form-control">
                            <option value="<?= htmlspecialchars($institution['city'] ?? '') ?>">
                                <?= htmlspecialchars($institution['city'] ?? 'Seleccionar ciudad...') ?>
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= htmlspecialchars($institution['phone'] ?? '') ?>"
                               placeholder="02-XXX-XXXX">
                    </div>
                    <div class="form-group">
                        <label>Email institucional</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($institution['email'] ?? '') ?>"
                               placeholder="info@colegio.edu.ec">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Director / Rectora</label>
                        <input type="text" name="director_name" class="form-control"
                               value="<?= htmlspecialchars($institution['director_name'] ?? '') ?>"
                               placeholder="Nombre completo">
                    </div>
                    <div class="form-group">
                        <label>Sitio web</label>
                        <input type="text" name="website" id="website" class="form-control"
                               value="<?= htmlspecialchars($institution['website'] ?? '') ?>"
                               placeholder="www.colegio.edu.ec"
                               onblur="fixUrl(this)">
                    </div>
                </div>

                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" name="address" class="form-control"
                           value="<?= htmlspecialchars($institution['address'] ?? '') ?>"
                           placeholder="Calle principal y secundaria">
                </div>

                <div class="form-group">
                    <label>Logo <span style="color:#999;font-weight:400;">(PNG/JPG, máx. 2MB)</span></label>
                    <?php if(!empty($institution['logo_path'])): ?>
                        <div style="margin-bottom:8px;">
                            <img src="<?= BASE_URL ?>/uploads/institution/<?= htmlspecialchars($institution['logo_path']) ?>"
                                 alt="Logo actual" style="height:60px;border:1px solid #ddd;border-radius:4px;padding:4px;">
                            <span style="font-size:12px;color:#999;margin-left:8px;">Logo actual</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg">
                </div>

                <button type="submit" class="btn btn-success">💾 Guardar Información</button>
            </form>
        </div>

        <!-- Panel derecho: Jornadas + Vista previa -->
        <div style="display:flex;flex-direction:column;gap:16px;">

            <!-- Vista previa logo -->
            <div class="panel" style="text-align:center;padding:20px;">
                <div style="font-size:48px;margin-bottom:8px;">
                    <?php if(!empty($institution['logo_path'])): ?>
                        <img src="<?= BASE_URL ?>/uploads/institution/<?= htmlspecialchars($institution['logo_path']) ?>"
                             style="max-height:80px;max-width:100%;">
                    <?php else: ?>
                        🏫
                    <?php endif; ?>
                </div>
                <div style="font-weight:700;font-size:1rem;color:#333;">
                    <?= htmlspecialchars($institution['name'] ?? 'Nombre de Institución') ?>
                </div>
                <?php if(!empty($institution['city'])): ?>
                    <div style="font-size:12px;color:#666;margin-top:4px;">
                        📍 <?= htmlspecialchars($institution['city']) ?>, <?= htmlspecialchars($institution['province']) ?>
                    </div>
                <?php endif; ?>
                <?php if(!empty($institution['amie_code'])): ?>
                    <div style="font-size:11px;color:#999;margin-top:4px;">
                        AMIE: <?= htmlspecialchars($institution['amie_code']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Jornadas -->
            <div class="panel">
                <h3 style="margin-bottom:12px;font-size:1rem;">🌅 Jornadas Asignadas</h3>

                <?php if(empty($assignedShifts)): ?>
                    <div style="color:#999;font-size:13px;margin-bottom:12px;">Sin jornadas asignadas</div>
                <?php else: ?>
                    <div style="margin-bottom:12px;">
                        <?php foreach($assignedShifts as $s): ?>
                            <span class="badge badge-teal" style="display:inline-flex;align-items:center;gap:6px;margin:2px;">
                                <?= ucfirst(htmlspecialchars($s['name'])) ?>
                                <form method="POST" action="?action=remove_institution_shift" style="display:inline;">
                                    <input type="hidden" name="shift_id" value="<?= $s['id'] ?>">
                                    <button type="submit"
                                        style="background:none;border:none;color:inherit;cursor:pointer;font-size:14px;line-height:1;padding:0;"
                                        onclick="return confirm('¿Quitar esta jornada?')">×</button>
                                </form>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php
                $assignedIds = array_column($assignedShifts ?? [], 'id');
                $available = array_filter($allShifts ?? [], fn($s) => !in_array($s['id'], $assignedIds));
                ?>
                <?php if(!empty($available)): ?>
                    <form method="POST" action="?action=assign_institution_shift" style="display:flex;gap:8px;">
                        <select name="shift_id" class="form-control" required style="font-size:13px;">
                            <option value="">Agregar jornada...</option>
                            <?php foreach($available as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= ucfirst(htmlspecialchars($s['name'])) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm" style="white-space:nowrap;">+ Agregar</button>
                    </form>
                <?php else: ?>
                    <div style="font-size:12px;color:#999;">Todas las jornadas están asignadas</div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

<script>
const cities = {
    'Pichincha':    ['Quito','Cayambe','Machachi','Sangolquí','Tabacundo'],
    'Guayas':       ['Guayaquil','Samborondón','Durán','Milagro','Daule','Playas','El Triunfo'],
    'Azuay':        ['Cuenca','Gualaceo','Paute','Santa Isabel','Sigsig'],
    'Manabí':       ['Portoviejo','Manta','Chone','Bahía de Caráquez','El Carmen','Jipijapa'],
    'El Oro':       ['Machala','Pasaje','Santa Rosa','Huaquillas','Zaruma','Piñas'],
    'Tungurahua':   ['Ambato','Baños','Pelileo','Pillaro','Mocha'],
    'Loja':         ['Loja','Catamayo','Cariamanga','Macará','Espíndola'],
    'Imbabura':     ['Ibarra','Otavalo','Cotacachi','Atuntaqui','Pimampiro'],
    'Chimborazo':   ['Riobamba','Alausí','Colta','Chambo','Guano'],
    'Cotopaxi':     ['Latacunga','La Maná','Salcedo','Pujilí','Saquisilí'],
    'Los Ríos':     ['Babahoyo','Quevedo','Ventanas','Vinces','Baba'],
    'Bolívar':      ['Guaranda','Chillanes','San Miguel','Chimbo'],
    'Cañar':        ['Azogues','Cañar','La Troncal','Biblián'],
    'Carchi':       ['Tulcán','Montúfar','Mira','Espejo'],
    'Esmeraldas':   ['Esmeraldas','Atacames','Muisne','Quinindé','San Lorenzo'],
    'Galápagos':    ['Puerto Baquerizo Moreno','Puerto Ayora','Puerto Villamil'],
    'Morona Santiago':['Macas','Gualaquiza','Palora','Sucúa'],
    'Napo':         ['Tena','Archidona','El Chaco','Quijos'],
    'Orellana':     ['Puerto Francisco de Orellana','La Joya de los Sachas','Loreto'],
    'Pastaza':      ['Puyo','Mera','Santa Clara','Arajuno'],
    'Santa Elena':  ['Santa Elena','Salinas','La Libertad'],
    'Santo Domingo de los Tsáchilas':['Santo Domingo','La Concordia'],
    'Sucumbíos':    ['Nueva Loja (Lago Agrio)','Shushufindi','Gonzalo Pizarro'],
    'Zamora Chinchipe':['Zamora','Yantzaza','El Pangui','Centinela del Cóndor'],
};

const currentCity = "<?= addslashes($institution['city'] ?? '') ?>";

function loadCities(selected) {
    const prov = document.getElementById('province').value;
    const sel  = document.getElementById('city');
    const opts = cities[prov] || [];
    sel.innerHTML = '<option value="">Seleccionar ciudad...</option>';
    opts.forEach(c => {
        const o = document.createElement('option');
        o.value = o.textContent = c;
        if(c === (selected || currentCity)) o.selected = true;
        sel.appendChild(o);
    });
}
function fixUrl(el) {
    const v = el.value.trim();
    if(v && !v.startsWith('http')) el.value = 'https://' + v;
}
// init
loadCities(currentCity);
</script>

</body>
</html>
