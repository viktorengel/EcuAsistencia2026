<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respaldos - EcuAsist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 70px; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 30px auto; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">💾 Respaldos de Base de Datos</h2>
                <button onclick="createBackup()" class="btn btn-primary">
                    ➕ Crear Respaldo Ahora
                </button>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    ✓ Respaldo creado correctamente: <strong><?= htmlspecialchars($_GET['file']) ?></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    ✗ Error al crear el respaldo. Verifique la configuración de MySQL.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['cleanup'])): ?>
                <div class="alert alert-info alert-dismissible fade show">
                    🗑️ <?= (int)$_GET['cleanup'] ?> respaldo(s) antiguo(s) eliminado(s).
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['deleted'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    ✓ Respaldo eliminado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="alert alert-info">
                <strong>ℹ️ Información:</strong><br>
                • Los respaldos se crean automáticamente en formato SQL<br>
                • Se recomienda descargar y guardar en ubicación segura<br>
                • Los respaldos antiguos (>30 días) pueden eliminarse para liberar espacio
            </div>

            <div class="d-flex justify-content-between mb-3">
                <h4>Respaldos Disponibles (<?= count($backups) ?>)</h4>
                <?php if(count($backups) > 0): ?>
                    <button onclick="confirmCleanup()" class="btn btn-warning btn-sm">
                        🗑️ Limpiar Antiguos (>30 días)
                    </button>
                <?php endif; ?>
            </div>

            <?php if(empty($backups)): ?>
                <div class="alert alert-secondary text-center">
                    📂 No hay respaldos disponibles. Crea uno usando el botón superior.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nombre del Archivo</th>
                                <th>Fecha de Creación</th>
                                <th>Tamaño</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 1;
                            foreach($backups as $backup): 
                            ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($backup['filename']) ?></strong>
                                </td>
                                <td><?= $backup['date'] ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= $backup['size'] ?></span>
                                </td>
                                <td>
                                    <a href="?action=download_backup&file=<?= urlencode($backup['filename']) ?>" 
                                       class="btn btn-sm btn-success">
                                        ⬇️ Descargar
                                    </a>
                                    <button onclick="confirmDelete('<?= addslashes($backup['filename']) ?>')" 
                                            class="btn btn-sm btn-danger">
                                        🗑️ Eliminar
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function createBackup() {
            const modal = document.createElement('div');
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
            
            const modalContent = document.createElement('div');
            modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
            
            modalContent.innerHTML = `
                <h3 style="margin: 0 0 15px 0; color: #007bff;">💾 Crear Respaldo</h3>
                <p style="margin: 0 0 20px 0; color: #666;">
                    ¿Crear un nuevo respaldo de la base de datos?
                </p>
                <p style="margin: 0 0 20px 0; color: #666; font-size: 14px; background: #e7f3ff; padding: 10px; border-radius: 4px;">
                    <strong>ℹ️ Nota:</strong> Este proceso puede tardar unos segundos dependiendo del tamaño de la base de datos.
                </p>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" id="cancelCreateBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="button" id="confirmCreateBtn" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Sí, Crear
                    </button>
                </div>
            `;
            
            modal.appendChild(modalContent);
            document.body.appendChild(modal);
            
            document.getElementById('confirmCreateBtn').onclick = function() {
                document.body.removeChild(modal);
                window.location.href = '?action=create_backup';
            };
            
            document.getElementById('cancelCreateBtn').onclick = function() {
                document.body.removeChild(modal);
            };
        }

        function confirmCleanup() {
            const modal = document.createElement('div');
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
            
            const modalContent = document.createElement('div');
            modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
            
            modalContent.innerHTML = `
                <h3 style="margin: 0 0 15px 0; color: #ffc107;">🗑️ Limpiar Respaldos Antiguos</h3>
                <p style="margin: 0 0 20px 0; color: #666;">
                    ¿Está seguro de eliminar todos los respaldos con más de <strong>30 días</strong> de antigüedad?
                </p>
                <p style="margin: 0 0 20px 0; color: #666; font-size: 14px; background: #fff3cd; padding: 10px; border-radius: 4px;">
                    <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer. 
                    Se recomienda descargar los respaldos importantes antes de eliminarlos.
                </p>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" id="cancelCleanupBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="button" id="confirmCleanupBtn" style="padding: 10px 20px; background: #ffc107; color: #000; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        Sí, Limpiar
                    </button>
                </div>
            `;
            
            modal.appendChild(modalContent);
            document.body.appendChild(modal);
            
            document.getElementById('confirmCleanupBtn').onclick = function() {
                document.body.removeChild(modal);
                window.location.href = '?action=cleanup_backups';
            };
            
            document.getElementById('cancelCleanupBtn').onclick = function() {
                document.body.removeChild(modal);
            };
        }

        function confirmDelete(filename) {
            const modal = document.createElement('div');
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;';
            
            const modalContent = document.createElement('div');
            modalContent.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
            
            modalContent.innerHTML = `
                <h3 style="margin: 0 0 15px 0; color: #dc3545;">⚠️ Eliminar Respaldo</h3>
                <p style="margin: 0 0 20px 0; color: #666;">
                    ¿Está seguro de eliminar el respaldo <strong>${filename}</strong>?
                </p>
                <p style="margin: 0 0 20px 0; color: #666; font-size: 14px; background: #f8d7da; padding: 10px; border-radius: 4px;">
                    <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer.
                </p>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" id="cancelDeleteBtn" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="button" id="confirmDeleteBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Sí, Eliminar
                    </button>
                </div>
            `;
            
            modal.appendChild(modalContent);
            document.body.appendChild(modal);
            
            document.getElementById('confirmDeleteBtn').onclick = function() {
                document.body.removeChild(modal);
                window.location.href = '?action=delete_backup&file=' + encodeURIComponent(filename);
            };
            
            document.getElementById('cancelDeleteBtn').onclick = function() {
                document.body.removeChild(modal);
            };
        }
    </script>
</body>
</html>