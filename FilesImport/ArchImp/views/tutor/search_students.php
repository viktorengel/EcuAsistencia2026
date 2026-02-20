<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Estudiantes - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=tutor_dashboard">Dashboard Tutor</a> &rsaquo;
    Buscar Estudiantes
</div>

<div class="container">

    <!-- Header -->
    <div class="page-header" style="background:linear-gradient(135deg,#1565c0,#1976d2);">
        <div class="ph-icon">🔍</div>
        <div>
            <h1>Buscar Estudiantes</h1>
            <p><?= htmlspecialchars($course['name']) ?></p>
        </div>
        <div class="ph-actions">
            <a href="?action=tutor_dashboard" class="btn" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.5);">← Dashboard</a>
        </div>
    </div>

    <!-- Buscador -->
    <div class="panel" style="margin-bottom:20px;">
        <form method="GET" action="">
            <input type="hidden" name="action" value="tutor_search_students">
            <div class="form-row" style="align-items:flex-end;">
                <div class="form-group" style="flex:1;">
                    <label>Nombre, apellido o cédula</label>
                    <input type="text" name="q" class="form-control"
                           placeholder="Ej: García, 1750..."
                           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                           autofocus>
                </div>
                <div class="form-group" style="flex:0 0 auto;display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary">🔍 Buscar</button>
                    <?php if(!empty($_GET['q'])): ?>
                    <a href="?action=tutor_search_students" class="btn btn-outline">✕ Limpiar</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Resultados -->
    <?php if(isset($_GET['q']) && empty($students)): ?>
    <div class="empty-state">
        <div class="icon">🔍</div>
        <p>No se encontraron estudiantes con "<?= htmlspecialchars($_GET['q']) ?>"</p>
    </div>

    <?php else: ?>
    <div class="table-wrap">
        <div class="table-info">
            <span>
                👥 <strong><?= isset($_GET['q']) && $_GET['q'] !== '' ? 'Resultados' : 'Todos los estudiantes' ?></strong>
                — <?= count($students) ?> encontrado<?= count($students)!=1?'s':'' ?>
                <?php if(!empty($_GET['q'])): ?>
                    para "<strong><?= htmlspecialchars($_GET['q']) ?></strong>"
                <?php endif; ?>
            </span>
        </div>
        <?php if(empty($students)): ?>
        <div class="empty-state" style="padding:30px;">
            <div class="icon">👥</div>
            <p>No hay estudiantes matriculados en este curso.</p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estudiante</th>
                    <th>Cédula</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th style="text-align:center;">Clases</th>
                    <th style="text-align:center;">✅</th>
                    <th style="text-align:center;">❌</th>
                    <th style="text-align:center;">⏰</th>
                    <th style="text-align:center;">% Asistencia</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($students as $s):
                    $total   = (int)($s['total_clases'] ?? 0);
                    $pres    = (int)($s['presentes']   ?? 0);
                    $ause    = (int)($s['ausentes']     ?? 0);
                    $tard    = (int)($s['tardanzas']    ?? 0);
                    $just    = (int)($s['justificados'] ?? 0);
                    $pct     = $total > 0 ? round(($pres + $just) / $total * 100) : 0;
                    $pctColor = $pct >= 90 ? '#2e7d32' : ($pct >= 75 ? '#f57f17' : '#c62828');
                    $pctBg    = $pct >= 90 ? '#e8f5e9' : ($pct >= 75 ? '#fff8e1' : '#ffebee');
                ?>
                <tr>
                    <td style="color:#999;font-size:12px;"><?= $i++ ?></td>
                    <td><strong><?= htmlspecialchars($s['last_name'] . ' ' . $s['first_name']) ?></strong></td>
                    <td style="font-size:13px;color:#666;"><?= htmlspecialchars($s['dni'] ?? '—') ?></td>
                    <td style="font-size:13px;color:#666;"><?= htmlspecialchars($s['email'] ?? '—') ?></td>
                    <td style="font-size:13px;color:#666;"><?= htmlspecialchars($s['phone'] ?? '—') ?></td>
                    <td style="text-align:center;font-size:13px;"><?= $total ?></td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;color:#2e7d32;"><?= $pres ?></span>
                    </td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;color:#c62828;"><?= $ause ?></span>
                    </td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;color:#f57f17;"><?= $tard ?></span>
                    </td>
                    <td style="text-align:center;">
                        <?php if($total > 0): ?>
                        <span style="background:<?= $pctBg ?>;color:<?= $pctColor ?>;font-weight:700;padding:3px 10px;border-radius:12px;font-size:13px;">
                            <?= $pct ?>%
                        </span>
                        <?php else: ?>
                        <span style="color:#ccc;font-size:12px;">Sin datos</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>
</body>
</html>