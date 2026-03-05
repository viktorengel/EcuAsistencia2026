<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Vinculación - EcuAsist</title>
    <style>
        .req-card { background:#fff; border:1px solid #e0e0e0; border-radius:10px; padding:20px; margin-bottom:14px; border-left:5px solid #ffc107; }
        .req-card.aprobado { border-left-color:#28a745; opacity:.75; }
        .req-card.rechazado { border-left-color:#dc3545; opacity:.75; }
        .req-header { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px; }
        .req-names { font-size:15px; font-weight:700; color:#333; }
        .req-names span { color:#1a237e; }
        .req-meta { font-size:12px; color:#888; margin-top:5px; display:flex; gap:14px; flex-wrap:wrap; }
        .req-message { margin-top:10px; padding:10px 14px; background:#f8f9fa; border-radius:6px; font-size:13px; color:#555; font-style:italic; }
        .req-actions { display:flex; gap:8px; margin-top:14px; flex-wrap:wrap; align-items:center; }
        .req-actions input { flex:1; min-width:180px; padding:8px 12px; border:1.5px solid #ddd; border-radius:6px; font-size:13px; }
        .req-actions input:focus { border-color:#1a237e; outline:none; }
        .btn-approve { padding:8px 18px; background:#28a745; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; }
        .btn-approve:hover { background:#218838; }
        .btn-reject  { padding:8px 18px; background:#dc3545; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; }
        .btn-reject:hover { background:#c82333; }
        .badge-pend { background:#ffc107; color:#000; padding:4px 12px; border-radius:10px; font-size:12px; font-weight:700; }
        .badge-apro { background:#28a745; color:#fff; padding:4px 12px; border-radius:10px; font-size:12px; font-weight:700; }
        .badge-rech { background:#dc3545; color:#fff; padding:4px 12px; border-radius:10px; font-size:12px; font-weight:700; }
        .tabs { display:flex; gap:4px; margin-bottom:20px; border-bottom:2px solid #e0e0e0; }
        .tab { padding:9px 18px; font-size:13px; font-weight:600; color:#888; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px; text-decoration:none; }
        .tab.active { color:#1a237e; border-bottom-color:#1a237e; }
        .tab:hover { color:#1a237e; }

        /* ── Toast flotante ── */
        .ec-toast {
            position: fixed; bottom: 28px; right: 28px; z-index: 99999;
            padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 600;
            box-shadow: 0 6px 24px rgba(0,0,0,0.18); display: flex; align-items: center; gap: 10px;
            animation: ecToastIn 0.35s ease, ecToastOut 0.4s ease 3.5s forwards;
            max-width: 380px;
        }
        .ec-toast--success { background: #d1fae5; color: #065f46; border-left: 5px solid #10b981; }
        .ec-toast--warning { background: #fff3cd; color: #856404; border-left: 5px solid #ffc107; }
        @keyframes ecToastIn  { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        @keyframes ecToastOut { from { opacity:1; } to { opacity:0; pointer-events:none; } }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=manage_representatives">Representantes</a> &rsaquo;
    Solicitudes de Vinculación
</div>

<div class="container">

    <?php if(isset($_GET['approved'])): ?>
    <div id="ec-toast" class="ec-toast ec-toast--success">✓ Solicitud aprobada. El representante ha sido vinculado al estudiante.</div>
    <?php endif; ?>
    <?php if(isset($_GET['rejected'])): ?>
    <div id="ec-toast" class="ec-toast ec-toast--warning">✗ Solicitud rechazada.</div>
    <?php endif; ?>

    <div class="page-header" style="background:linear-gradient(135deg,#1a237e,#283593);">
        <div class="ph-icon">🔗</div>
        <div>
            <h1>Solicitudes de Vinculación</h1>
            <p>Representantes solicitando acceso a estudiantes</p>
        </div>
    </div>

    <!-- Tabs -->
    <?php $filter = $_GET['filter'] ?? 'pendiente'; ?>
    <div class="tabs">
        <a href="?action=link_requests&filter=pendiente" class="tab <?= $filter==='pendiente'?'active':'' ?>">
            ⏳ Pendientes
            <?php if($pendingCount > 0): ?><span style="background:#ffc107;color:#000;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;"><?= $pendingCount ?></span><?php endif; ?>
        </a>
        <a href="?action=link_requests&filter=aprobado"  class="tab <?= $filter==='aprobado'?'active':'' ?>">✅ Aprobadas</a>
        <a href="?action=link_requests&filter=rechazado" class="tab <?= $filter==='rechazado'?'active':'' ?>">✗ Rechazadas</a>
    </div>

    <?php if(empty($requests)): ?>
    <div class="empty-state">
        <div class="icon">🔗</div>
        <p>No hay solicitudes <?= $filter === 'pendiente' ? 'pendientes' : ($filter === 'aprobado' ? 'aprobadas' : 'rechazadas') ?>.</p>
    </div>
    <?php else: ?>
    <?php foreach($requests as $req): ?>
    <div class="req-card <?= $req['status'] ?>">
        <div class="req-header">
            <div>
                <div class="req-names">
                    <span><?= htmlspecialchars($req['rep_name']) ?></span>
                    <span style="color:#888;font-weight:400;"> solicita ser representante de </span>
                    <span><?= htmlspecialchars($req['student_name']) ?></span>
                </div>
                <div class="req-meta">
                    <span>👤 Rep. CI: <?= htmlspecialchars($req['rep_dni'] ?? 'Sin cédula') ?></span>
                    <span>🎓 Est. CI: <?= htmlspecialchars($req['student_dni'] ?? 'Sin cédula') ?></span>
                    <span>🔗 Parentesco: <strong><?= htmlspecialchars($req['relationship']) ?></strong></span>
                    <span>📅 <?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></span>
                    <?php if($req['status'] !== 'pendiente'): ?>
                    <span>· Revisado por <?= htmlspecialchars($req['reviewer_name'] ?? '—') ?> el <?= date('d/m/Y', strtotime($req['reviewed_at'])) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <?php if($req['status'] === 'pendiente'): ?>
                    <span class="badge-pend">⏳ Pendiente</span>
                <?php elseif($req['status'] === 'aprobado'): ?>
                    <span class="badge-apro">✅ Aprobada</span>
                <?php else: ?>
                    <span class="badge-rech">✗ Rechazada</span>
                <?php endif; ?>
            </div>
        </div>

        <?php if($req['message']): ?>
        <div class="req-message">💬 "<?= htmlspecialchars($req['message']) ?>"</div>
        <?php endif; ?>

        <?php if($req['status'] === 'rechazado' && $req['review_notes']): ?>
        <div class="req-message" style="background:#fff0f0;color:#c62828;">
            📝 Motivo de rechazo: <?= htmlspecialchars($req['review_notes']) ?>
        </div>
        <?php endif; ?>

        <?php if($req['status'] === 'pendiente'): ?>
        <div class="req-actions">
            <form id="form-approve-<?= $req['id'] ?>" method="POST" action="?action=review_link_request">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                <input type="hidden" name="decision" value="aprobado">
                <input type="hidden" name="is_primary" value="0">
                <button type="button" class="btn-approve"
                        onclick="ecConfirm({icon:'✅',title:'Aprobar solicitud',message:'Se vinculará al representante con el estudiante.',okText:'Aprobar',onOk:function(){ document.getElementById('form-approve-<?= $req['id'] ?>').submit(); }})">
                    ✅ Aprobar
                </button>
            </form>
            <form id="form-reject-<?= $req['id'] ?>" method="POST" action="?action=review_link_request" style="display:flex;gap:8px;align-items:center;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                <input type="hidden" name="decision" value="rechazado">
                <input type="text" name="review_notes" id="notes-<?= $req['id'] ?>" placeholder="Motivo del rechazo (opcional)...">
                <button type="button" class="btn-reject"
                        onclick="ecConfirm({icon:'❌',title:'Rechazar solicitud',message:'¿Confirmas el rechazo de esta vinculación?',okText:'Rechazar',onOk:function(){ document.getElementById('form-reject-<?= $req['id'] ?>').submit(); }})">
                    ✗ Rechazar
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

</div>

<script>
var t = document.getElementById('ec-toast');
if (t) setTimeout(function(){ t.style.display = 'none'; }, 4000);
</script>
</body>
</html>