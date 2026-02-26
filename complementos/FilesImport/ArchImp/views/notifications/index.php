<?php
// ARCHIVO COMPLETO - Reemplaza TODO el archivo
// views/notifications/index.php

$typeIcons = [
    'info'          => '📋',
    'success'       => '✅',
    'warning'       => '⚠️',
    'danger'        => '❌',
    'ausente'       => '📅',
    'justificacion' => '📝',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones - EcuAsist</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f6f9; color: #333; }

        .breadcrumb { padding: 10px 24px; background: #fff; border-bottom: 1px solid #e0e0e0; font-size: 0.83rem; color: #888; }
        .breadcrumb a { color: #007bff; text-decoration: none; }

        .container { max-width: 760px; margin: 28px auto; padding: 0 16px; }

        .page-title { font-size: 1.4rem; font-weight: 600; margin-bottom: 20px; }
        .page-title span { color: #6c757d; font-size: 0.95rem; font-weight: 400; margin-left: 8px; }

        .alert { padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 0.88rem; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        .btn { padding: 7px 14px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; transition: opacity 0.15s; }
        .btn:hover { opacity: 0.85; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-outline  { background: #fff; color: #555; border: 1px solid #ccc; }
        .btn-sm { padding: 4px 10px; font-size: 0.78rem; }

        .stats-row { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .stat-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 12px 18px; flex: 1; min-width: 100px; }
        .stat-card .number { font-size: 1.6rem; font-weight: 700; }
        .stat-card .label  { font-size: 0.78rem; color: #888; margin-top: 2px; }
        .stat-card.blue .number { color: #007bff; }

        .notif-list { display: flex; flex-direction: column; gap: 8px; }

        .notif-item {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 14px 16px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            position: relative;
            text-decoration: none;
            color: inherit;
            transition: box-shadow 0.15s, border-color 0.15s;
        }
        .notif-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .notif-item.has-link { cursor: pointer; }
        .notif-item.has-link:hover { border-color: #007bff; box-shadow: 0 3px 12px rgba(0,123,255,0.15); }
        .notif-item.unread { border-left: 4px solid #007bff; background: #f8fbff; }

        .notif-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #007bff;
            position: absolute; top: 14px; right: 14px;
        }

        .notif-icon { font-size: 1.5rem; min-width: 36px; text-align: center; line-height: 1; }
        .notif-body { flex: 1; min-width: 0; }
        .notif-title { font-weight: 600; font-size: 0.92rem; margin-bottom: 3px; }
        .notif-message { font-size: 0.85rem; color: #555; line-height: 1.4; }
        .notif-meta { display: flex; align-items: center; gap: 10px; margin-top: 6px; flex-wrap: wrap; }
        .notif-time { font-size: 0.78rem; color: #999; }
        .notif-link-hint { font-size: 0.78rem; color: #007bff; font-weight: 500; }
        .badge-new { font-size: 0.72rem; background: #007bff; color: #fff; padding: 1px 7px; border-radius: 10px; font-weight: 600; }

        .notif-actions { display: flex; gap: 6px; align-items: center; flex-shrink: 0; }

        .empty-state { text-align: center; padding: 60px 20px; color: #999; }
        .empty-state .icon { font-size: 3rem; margin-bottom: 12px; }

        .pagination { display: flex; justify-content: center; gap: 6px; margin-top: 24px; flex-wrap: wrap; }
        .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; border: 1px solid #ddd; text-decoration: none; color: #555; }
        .pagination a:hover { background: #f0f0f0; }
        .pagination .current { background: #007bff; color: #fff; border-color: #007bff; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Notificaciones
</div>

<div class="container">

    <?php if (isset($_GET['success'])): ?>
        <?php $msgs = ['read' => '✅ Marcadas como leídas.', 'deleted' => '🗑️ Notificación eliminada.', 'cleaned' => '🧹 Notificaciones leídas eliminadas.']; ?>
        <?php if (!empty($msgs[$_GET['success']])): ?>
            <div class="alert alert-success"><?= $msgs[$_GET['success']] ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="page-title">
        🔔 Notificaciones
        <span><?= $total ?> en total</span>
    </div>

    <div class="stats-row">
        <div class="stat-card blue">
            <div class="number"><?= $unreadCount ?></div>
            <div class="label">No leídas</div>
        </div>
        <div class="stat-card">
            <div class="number"><?= $total ?></div>
            <div class="label">Total</div>
        </div>
    </div>

    <div style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap;">
        <?php if ($unreadCount > 0): ?>
        <form method="POST" action="?action=notifications_mark_all" style="margin:0;">
            <button type="submit" class="btn btn-primary">✅ Marcar todas como leídas</button>
        </form>
        <?php endif; ?>
        <form method="POST" action="?action=notifications_delete_read"
              onsubmit="return confirm('¿Eliminar todas las notificaciones leídas?');" style="margin:0;">
            <button type="submit" class="btn btn-outline">🧹 Limpiar leídas</button>
        </form>
    </div>

    <div class="notif-list">
        <?php if (empty($notifications)): ?>
            <div class="empty-state">
                <div class="icon">🔔</div>
                <p>No tienes notificaciones.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <?php
                $icon    = $typeIcons[$n['type']] ?? '📋';
                $isNew   = !(bool)$n['is_read'];
                $hasLink = !empty($n['link']);
                $tag     = $hasLink ? 'a' : 'div';
                $href    = $hasLink ? ' href="' . htmlspecialchars($n['link']) . '"' : '';
                $css     = 'notif-item' . ($isNew ? ' unread' : '') . ($hasLink ? ' has-link' : '');
                $onclick = ($hasLink && $isNew) ? ' onclick="markRead(' . $n['id'] . ', this)"' : '';
                ?>
                <<?= $tag ?><?= $href ?> class="<?= $css ?>"<?= $onclick ?>>

                    <?php if ($isNew): ?><span class="notif-dot"></span><?php endif; ?>

                    <div class="notif-icon"><?= $icon ?></div>

                    <div class="notif-body">
                        <div class="notif-title"><?= htmlspecialchars($n['title']) ?></div>
                        <div class="notif-message"><?= htmlspecialchars($n['message']) ?></div>
                        <div class="notif-meta">
                            <span class="notif-time">🕐 <?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></span>
                            <?php if ($isNew): ?><span class="badge-new">Nueva</span><?php endif; ?>
                            <?php if ($hasLink): ?><span class="notif-link-hint">👆 Clic para ver →</span><?php endif; ?>
                        </div>
                    </div>

                    <div class="notif-actions" onclick="event.stopPropagation();">
                        <?php if ($isNew): ?>
                            <a href="?action=notifications_mark_read&id=<?= $n['id'] ?>"
                               class="btn btn-outline btn-sm" title="Marcar como leída">✓</a>
                        <?php endif; ?>
                        <form method="POST" action="?action=notifications_delete" style="margin:0;"
                              onsubmit="return confirm('¿Eliminar esta notificación?');">
                            <input type="hidden" name="notification_id" value="<?= $n['id'] ?>">
                            <button type="submit" class="btn btn-outline btn-sm" title="Eliminar">🗑️</button>
                        </form>
                    </div>

                </<?= $tag ?>>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?action=notifications&page=<?= $page - 1 ?>">← Anterior</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $page): ?>
                <span class="current"><?= $i ?></span>
            <?php else: ?>
                <a href="?action=notifications&page=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?action=notifications&page=<?= $page + 1 ?>">Siguiente →</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<script>
function markRead(id, el) {
    // Marcar como leída en background
    fetch('?action=notifications_mark_read&id=' + id, { credentials: 'same-origin' })
        .catch(function(){});

    // Quitar estilos no-leída visualmente
    el.classList.remove('unread');
    var dot = el.querySelector('.notif-dot');
    if (dot) dot.remove();
    var badgeNew = el.querySelector('.badge-new');
    if (badgeNew) badgeNew.remove();

    // Actualizar campana del navbar
    setTimeout(function(){
        fetch('?action=notifications_unread_json', { credentials: 'same-origin' })
            .then(function(r){ return r.json(); })
            .then(function(data){
                var b = document.getElementById('notif-badge');
                if (b) {
                    if (data.count > 0) {
                        b.textContent = data.count > 99 ? '99+' : data.count;
                        b.style.display = 'flex';
                    } else {
                        b.style.display = 'none';
                    }
                }
            }).catch(function(){});
    }, 200);
}
</script>

</body>
</html>