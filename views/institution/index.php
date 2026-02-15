<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Institución - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        h2 { margin-bottom: 20px; color: #333; }
        .badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            margin: 3px;
        }
        .btn-remove {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            padding: 0;
            margin-left: 5px;
        }
        .btn-remove:hover { color: #ff0000; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Información actualizada correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['shift_assigned'])): ?>
            <div class="success">✓ Jornada asignada correctamente</div>
        <?php endif; ?>
        <?php if(isset($_GET['shift_removed'])): ?>
            <div class="success">✓ Jornada eliminada correctamente</div>
        <?php endif; ?>
        
        <div class="grid">
            <div class="form-group">
                <label>Nombre de la Institución *</label>
                <input type="text" name="name" value="<?= $institution['name'] ?>" required>
            </div>

            <div class="form-group">
                <label>Logo de la Institución</label>
                <?php if(isset($institution['logo_path']) && $institution['logo_path']): ?>
                    <img src="<?= BASE_URL ?>/<?= $institution['logo_path'] ?>" style="max-width: 150px; margin-bottom: 10px; display: block;">
                <?php endif; ?>
                <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg">
                <small style="color: #666; display: block; margin-top: 5px;">Formatos: PNG, JPG - Tamaño recomendado: 300x300px</small>
            </div>

            <div class="form-group">
                <label>Código AMIE</label>
                <input type="text" name="amie_code" value="<?= $institution['amie_code'] ?? '' ?>" placeholder="Ej: 01H00001">
            </div>
        </div>

        <div class="grid">

        <div class="card">
            <h2>📋 Información de la Institución</h2>
            <form method="POST" action="?action=update_institution" enctype="multipart/form-data">
                <div class="grid">
                    <div class="form-group">
                        <label>Nombre de la Institución *</label>
                        <input type="text" name="name" value="<?= $institution['name'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Código AMIE</label>
                        <input type="text" name="amie_code" value="<?= $institution['amie_code'] ?? '' ?>" placeholder="Ej: 01H00001">
                    </div>

                    <div class="form-group">
                        <label>Provincia *</label>
                        <select name="province" id="province" required>
                            <option value="">Seleccionar provincia...</option>
                            <option value="Pichincha" <?= ($institution['province'] ?? '') == 'Pichincha' ? 'selected' : '' ?>>Pichincha</option>
                            <option value="Guayas" <?= ($institution['province'] ?? '') == 'Guayas' ? 'selected' : '' ?>>Guayas</option>
                            <option value="Azuay" <?= ($institution['province'] ?? '') == 'Azuay' ? 'selected' : '' ?>>Azuay</option>
                            <option value="Manabí" <?= ($institution['province'] ?? '') == 'Manabí' ? 'selected' : '' ?>>Manabí</option>
                            <option value="El Oro" <?= ($institution['province'] ?? '') == 'El Oro' ? 'selected' : '' ?>>El Oro</option>
                            <option value="Los Ríos" <?= ($institution['province'] ?? '') == 'Los Ríos' ? 'selected' : '' ?>>Los Ríos</option>
                            <option value="Tungurahua" <?= ($institution['province'] ?? '') == 'Tungurahua' ? 'selected' : '' ?>>Tungurahua</option>
                            <option value="Esmeraldas" <?= ($institution['province'] ?? '') == 'Esmeraldas' ? 'selected' : '' ?>>Esmeraldas</option>
                            <option value="Chimborazo" <?= ($institution['province'] ?? '') == 'Chimborazo' ? 'selected' : '' ?>>Chimborazo</option>
                            <option value="Imbabura" <?= ($institution['province'] ?? '') == 'Imbabura' ? 'selected' : '' ?>>Imbabura</option>
                            <option value="Cotopaxi" <?= ($institution['province'] ?? '') == 'Cotopaxi' ? 'selected' : '' ?>>Cotopaxi</option>
                            <option value="Santo Domingo de los Tsáchilas" <?= ($institution['province'] ?? '') == 'Santo Domingo de los Tsáchilas' ? 'selected' : '' ?>>Santo Domingo de los Tsáchilas</option>
                            <option value="Santa Elena" <?= ($institution['province'] ?? '') == 'Santa Elena' ? 'selected' : '' ?>>Santa Elena</option>
                            <option value="Loja" <?= ($institution['province'] ?? '') == 'Loja' ? 'selected' : '' ?>>Loja</option>
                            <option value="Cañar" <?= ($institution['province'] ?? '') == 'Cañar' ? 'selected' : '' ?>>Cañar</option>
                            <option value="Sucumbíos" <?= ($institution['province'] ?? '') == 'Sucumbíos' ? 'selected' : '' ?>>Sucumbíos</option>
                            <option value="Orellana" <?= ($institution['province'] ?? '') == 'Orellana' ? 'selected' : '' ?>>Orellana</option>
                            <option value="Carchi" <?= ($institution['province'] ?? '') == 'Carchi' ? 'selected' : '' ?>>Carchi</option>
                            <option value="Bolívar" <?= ($institution['province'] ?? '') == 'Bolívar' ? 'selected' : '' ?>>Bolívar</option>
                            <option value="Napo" <?= ($institution['province'] ?? '') == 'Napo' ? 'selected' : '' ?>>Napo</option>
                            <option value="Pastaza" <?= ($institution['province'] ?? '') == 'Pastaza' ? 'selected' : '' ?>>Pastaza</option>
                            <option value="Morona Santiago" <?= ($institution['province'] ?? '') == 'Morona Santiago' ? 'selected' : '' ?>>Morona Santiago</option>
                            <option value="Zamora Chinchipe" <?= ($institution['province'] ?? '') == 'Zamora Chinchipe' ? 'selected' : '' ?>>Zamora Chinchipe</option>
                            <option value="Galápagos" <?= ($institution['province'] ?? '') == 'Galápagos' ? 'selected' : '' ?>>Galápagos</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ciudad *</label>
                        <select name="city" id="city" required>
                            <option value="">Primero seleccione provincia...</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dirección *</label>
                    <input type="text" name="address" value="<?= $institution['address'] ?>" required>
                </div>

                <div class="grid">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="tel" name="phone" value="<?= $institution['phone'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?= $institution['email'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Sitio Web</label>
                        <input type="text" name="website" id="website" value="<?= $institution['website'] ?? '' ?>" placeholder="Ej: www.institucion.com o institucion.com">
                    </div>

                    <div class="form-group">
                        <label>Nombre del Director/Rector</label>
                        <input type="text" name="director_name" value="<?= $institution['director_name'] ?? '' ?>">
                    </div>
                </div>

                <button type="submit">Guardar Cambios</button>
            </form>
        </div>

        <div class="card">
            <h2>🕐 Jornadas de la Institución</h2>
            
            <div style="margin-bottom: 20px;">
                <strong>Jornadas Activas:</strong><br>
                <?php 
                $assignedShifts = $this->institutionShiftModel->getByInstitution(1);
                if(empty($assignedShifts)): 
                ?>
                    <em style="color: #999;">No hay jornadas asignadas</em>
                <?php else: ?>
                    <?php foreach($assignedShifts as $shift): ?>
                        <span class="badge">
                            <?= ucfirst($shift['name']) ?>
                            <form method="POST" action="?action=remove_institution_shift" style="display: inline;" onsubmit="return confirmRemoveShift(event, '<?= ucfirst($shift['name']) ?>')">
                                <input type="hidden" name="shift_id" value="<?= $shift['id'] ?>">
                                <button type="submit" class="btn-remove">×</button>
                            </form>
                        </span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form method="POST" action="?action=assign_institution_shift" style="display: flex; gap: 10px; align-items: flex-end;">
                <div style="flex: 1;">
                    <label>Asignar Nueva Jornada</label>
                    <select name="shift_id" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($allShifts as $shift): ?>
                            <?php if(!in_array($shift['id'], $assignedShiftIds)): ?>
                                <option value="<?= $shift['id'] ?>"><?= ucfirst($shift['name']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Asignar Jornada</button>
            </form>
        </div>
    </div>

    <script>
    function confirmRemoveShift(event, shiftName) {
        event.preventDefault();
        
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
        
        modalContent.innerHTML = `
            <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Eliminar Jornada</h3>
            <p style="margin: 0 0 20px 0; color: #666;">
                ¿Está seguro de eliminar la jornada <strong>${shiftName}</strong> de esta institución?<br><br>
                <em>Nota: Los cursos asignados a esta jornada no se eliminarán.</em>
            </p>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="cancelBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Cancelar
                </button>
                <button type="button" id="confirmBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Sí, Eliminar
                </button>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        const form = event.target;
        
        document.getElementById('confirmBtn').onclick = function() {
            document.body.removeChild(modal);
            form.submit();
        };
        
        document.getElementById('cancelBtn').onclick = function() {
            document.body.removeChild(modal);
        };
        
        return false;
    }

        const cities = {
        'Azuay': ['Cuenca', 'Gualaceo', 'Paute', 'Sigsig', 'Girón', 'Santa Isabel', 'Nabón', 'Oña', 'Pucará', 'San Fernando', 'Chordeleg', 'El Pan', 'Guachapala', 'Camilo Ponce Enríquez', 'Sevilla de Oro'],
        'Bolívar': ['Guaranda', 'San Miguel', 'Chillanes', 'Chimbo', 'Echeandía', 'Caluma', 'Las Naves'],
        'Cañar': ['Azogues', 'Biblián', 'Cañar', 'La Troncal', 'El Tambo', 'Déleg', 'Suscal'],
        'Carchi': ['Tulcán', 'Bolívar', 'Espejo', 'Mira', 'Montúfar', 'San Pedro de Huaca'],
        'Chimborazo': ['Riobamba', 'Alausí', 'Colta', 'Chambo', 'Chunchi', 'Guamote', 'Guano', 'Pallatanga', 'Penipe', 'Cumandá'],
        'Cotopaxi': ['Latacunga', 'La Maná', 'Pangua', 'Pujilí', 'Salcedo', 'Saquisilí', 'Sigchos'],
        'El Oro': ['Machala', 'Arenillas', 'Atahualpa', 'Balsas', 'Chilla', 'El Guabo', 'Huaquillas', 'Marcabelí', 'Pasaje', 'Piñas', 'Portovelo', 'Santa Rosa', 'Zaruma', 'Las Lajas'],
        'Esmeraldas': ['Esmeraldas', 'Eloy Alfaro', 'Muisne', 'Quinindé', 'San Lorenzo', 'Atacames', 'Rioverde', 'La Concordia'],
        'Galápagos': ['Puerto Baquerizo Moreno', 'Puerto Ayora', 'Puerto Villamil'],
        'Guayas': ['Guayaquil', 'Durán', 'Milagro', 'Daule', 'Samborondón', 'Balao', 'Balzar', 'Colimes', 'El Empalme', 'El Triunfo', 'Naranjal', 'Naranjito', 'Palestina', 'Pedro Carbo', 'Santa Lucía', 'Yaguachi', 'Playas', 'Simón Bolívar', 'Coronel Marcelino Maridueña', 'Lomas de Sargentillo', 'Nobol', 'General Antonio Elizalde', 'Isidro Ayora'],
        'Imbabura': ['Ibarra', 'Antonio Ante', 'Cotacachi', 'Otavalo', 'Pimampiro', 'San Miguel de Urcuquí'],
        'Loja': ['Loja', 'Calvas', 'Catamayo', 'Celica', 'Chaguarpamba', 'Espíndola', 'Gonzanamá', 'Macará', 'Paltas', 'Puyango', 'Saraguro', 'Sozoranga', 'Zapotillo', 'Pindal', 'Quilanga', 'Olmedo'],
        'Los Ríos': ['Babahoyo', 'Baba', 'Montalvo', 'Puebloviejo', 'Quevedo', 'Urdaneta', 'Ventanas', 'Vinces', 'Palenque', 'Buena Fe', 'Valencia', 'Mocache', 'Quinsaloma'],
        'Manabí': ['Portoviejo', 'Bolívar', 'Chone', 'El Carmen', 'Flavio Alfaro', 'Jipijapa', 'Junín', 'Manta', 'Montecristi', 'Paján', 'Pichincha', 'Rocafuerte', 'Santa Ana', 'Sucre', 'Tosagua', 'Veinticuatro de Mayo', 'Pedernales', 'Olmedo', 'Puerto López', 'Jama', 'Jaramijó', 'San Vicente'],
        'Morona Santiago': ['Macas', 'Gualaquiza', 'Limón Indanza', 'Palora', 'Santiago', 'Sucúa', 'Huamboya', 'San Juan Bosco', 'Taisha', 'Logroño', 'Pablo Sexto', 'Tiwintza'],
        'Napo': ['Tena', 'Archidona', 'El Chaco', 'Quijos', 'Carlos Julio Arosemena Tola'],
        'Orellana': ['Francisco de Orellana (Coca)', 'Aguarico', 'La Joya de los Sachas', 'Loreto'],
        'Pastaza': ['Puyo', 'Arajuno', 'Mera', 'Santa Clara'],
        'Pichincha': ['Quito', 'Cayambe', 'Mejía', 'Pedro Moncayo', 'Rumiñahui', 'San Miguel de los Bancos', 'Pedro Vicente Maldonado', 'Puerto Quito'],
        'Santa Elena': ['Santa Elena', 'La Libertad', 'Salinas'],
        'Santo Domingo de los Tsáchilas': ['Santo Domingo'],
        'Sucumbíos': ['Nueva Loja (Lago Agrio)', 'Gonzalo Pizarro', 'Putumayo', 'Shushufindi', 'Sucumbíos', 'Cascales', 'Cuyabeno'],
        'Tungurahua': ['Ambato', 'Baños de Agua Santa', 'Cevallos', 'Mocha', 'Patate', 'Quero', 'Pelileo', 'Píllaro', 'Tisaleo'],
        'Zamora Chinchipe': ['Zamora', 'Chinchipe', 'Nangaritza', 'Yacuambi', 'Yantzaza', 'El Pangui', 'Centinela del Cóndor', 'Palanda', 'Paquisha']
    };

    const currentCity = '<?= $institution['city'] ?? '' ?>';
    const currentProvince = '<?= $institution['province'] ?? '' ?>';

    document.getElementById('province').addEventListener('change', function() {
        const province = this.value;
        const citySelect = document.getElementById('city');
        
        citySelect.innerHTML = '<option value="">Seleccionar ciudad...</option>';
        
        if (province && cities[province]) {
            cities[province].forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
        }
    });

    // Cargar ciudades al inicio si hay provincia seleccionada
    if (currentProvince && cities[currentProvince]) {
        const citySelect = document.getElementById('city');
        citySelect.innerHTML = '<option value="">Seleccionar ciudad...</option>';
        cities[currentProvince].forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            if (city === currentCity) {
                option.selected = true;
            }
            citySelect.appendChild(option);
        });
    }

    // Autocompletar URL del sitio web
    document.getElementById('website').addEventListener('blur', function() {
        let url = this.value.trim();
        
        if (url && !url.match(/^https?:\/\//)) {
            // Si no tiene www, agregarlo
            if (!url.startsWith('www.')) {
                url = 'www.' + url;
            }
            // Agregar https://
            this.value = 'https://' + url;
        }
    });
    </script>
</body>
</html>