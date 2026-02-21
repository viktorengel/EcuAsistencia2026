<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respaldos - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Respaldos
</div>

<div class="container">

    <!-- Mensajes flash -->
    <?php if(isset($_GET['backup_created'])): ?>
        <div class="alert alert-success">✓ Respaldo creado correctamente</div>
    <?php endif; ?>
    <?php if(isset($_GET['cleanup'])): ?>
        <div class="alert alert-info">🗑️ <?= (int)$_GET['cleanup'] ?> respaldo(s) antiguo(s) eliminado(s)</div>
    <?php endif; ?>
    <?php if(isset($_GET['deleted'])): ?>
        <div class="alert alert-success">✓ Respaldo eliminado correctamente</div>
    <?php endif; ?>

    <!-- Header -->
    <div class="page-header dark">
        <div class="ph-icon">💾</div>
        <div>
            <h1>Gestión de Respaldos</h1>
            <p>Crea y descarga copias de seguridad de la base de datos</p>
        </div>
        <div class="ph-actions">
            <button class="btn btn-success" onclick="openModal('modalCreate')">+ Crear Respaldo</button>
            <?php if(count($backups) > 0): ?>
            <button class="btn btn-warning" onclick="openModal('modalCleanup')">🗑️ Limpiar Antiguos</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info -->
    <div class="alert alert-info">
        <strong>ℹ️ Información:</strong>
        Los respaldos se crean en formato SQL · Se recomienda descargar semanalmente · Los respaldos >30 días pueden limpiarse para liberar espacio
    </div>

    <!-- Tabla de respaldos -->
    <div class="table-wrap">
        <div class="table-info">
            <span>💾 <strong><?= count($backups) ?></strong> respaldos disponibles</span>
        </div>

        <?php if(empty($backups)): ?>
            <div class="empty-state">
                <div class="icon">📂</div>
                <p>No hay respaldos disponibles. Crea uno usando el botón superior.</p>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre del Archivo</th>
                    <th>Fecha de Creación</th>
                    <th>Tamaño</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; foreach($backups as $backup): ?>
                <tr>
                    <td style="color:#999;"><?= $counter++ ?></td>
                    <td><strong><?= htmlspecialchars($backup['filename']) ?></strong></td>
                    <td><?= htmlspecialchars($backup['date']) ?></td>
                    <td><span class="badge badge-gray"><?= htmlspecialchars($backup['size']) ?></span></td>
                    <td style="white-space:nowrap;">
                        <a href="?action=download_backup&file=<?= urlencode($backup['filename']) ?>" class="btn btn-success btn-sm">⬇️ Descargar</a>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete('<?= addslashes(htmlspecialchars($backup['filename'])) ?>')">🗑️ Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div>

<!-- Modal Crear Respaldo -->
<div class="modal-overlay" id="modalCreate">
    <div class="modal-box">
        <h3 style="color:#007bff;">💾 Crear Respaldo</h3>
        <p>¿Crear un nuevo respaldo de la base de datos?<br>
        <span class="form-hint">Este proceso puede tardar unos segundos dependiendo del tamaño de la base de datos.</span></p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalCreate')">Cancelar</button>
            <button class="btn btn-primary" onclick="window.location.href='?action=create_backup'">Sí, Crear</button>
        </div>
    </div>
</div>

<!-- Modal Limpiar Antiguos -->
<div class="modal-overlay" id="modalCleanup">
    <div class="modal-box">
        <h3 style="color:#e6ac00;">🗑️ Limpiar Respaldos Antiguos</h3>
        <p>¿Eliminar todos los respaldos con más de <strong>30 días</strong> de antigüedad?<br>
        <span class="form-hint">⚠️ Esta acción no se puede deshacer. Descarga los importantes antes de limpiar.</span></p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalCleanup')">Cancelar</button>
            <button class="btn btn-warning" onclick="window.location.href='?action=cleanup_backups'">Sí, Limpiar</button>
        </div>
    </div>
</div>

<!-- Modal Eliminar Respaldo -->
<div class="modal-overlay" id="modalDelete">
    <div class="modal-box">
        <h3 style="color:#dc3545;">⚠️ Eliminar Respaldo</h3>
        <p>¿Está seguro de eliminar el respaldo <strong id="deleteFileName"></strong>?<br>
        <span class="form-hint">Esta acción no se puede deshacer.</span></p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeModal('modalDelete')">Cancelar</button>
            <button class="btn btn-danger" id="deleteConfirmBtn">Sí, Eliminar</button>
        </div>
    </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('on'); }
function closeModal(id) { document.getElementById(id).classList.remove('on'); }

function confirmDelete(filename) {
    document.getElementById('deleteFileName').textContent = filename;
    document.getElementById('deleteConfirmBtn').onclick = function() {
        window.location.href = '?action=delete_backup&file=' + encodeURIComponent(filename);
    };
    openModal('modalDelete');
}

document.querySelectorAll('.modal-overlay').forEach(function(m) {
    m.addEventListener('click', function(e) { if(e.target === m) closeModal(m.id); });
});
</script>

</body>
</html>
