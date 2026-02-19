<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tutor - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo; Dashboard Tutor
</div>

<div class="container">

    <?php
    $pct = ($stats['total'] > 0)
        ? round(($stats['presente'] + $stats['justificado']) / $stats['total'] * 100)
        : 0;
    $todayPct = ($todayStats['total'] > 0)
        ? round(($todayStats['presente'] + $todayStats['justificado']) / $todayStats['total'] * 100)
        : 0;
    ?>

    <!-- Header -->
    <div class="page-header" style="background:linear-gradient(135deg,#1565c0,#1976d2);">
        <div class="ph-icon">🎓</div>
        <div>
            <h1>Dashboard — Tutor</h1>
            <p><?= htmlspecialchars($course['name']) ?> &nbsp;|&nbsp; <?= count($students) ?> estudiantes</p>
        </div>
        <div class="ph-actions">
            <a href="?action=tutor_search_students" class="btn" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.5);">🔍 Buscar Estudiantes</a>
            <a href="?action=tutor_course_attendance" class="btn" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.5);margin-left:8px;">📋 Ver Asistencias</a>
        </div>
    </div>

    <!-- Métricas globales del curso -->
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:20px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e3f2fd;">📊</div>
            <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
            <div class="stat-label">Total registros</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f5e9;">✅</div>
            <div class="stat-value" style="color:#2e7d32;"><?= $stats['presente'] ?? 0 ?></div>
            <div class="stat-label">Presentes</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ffebee;">❌</div>
            <div class="stat-value" style="color:#c62828;"><?= $stats['ausente'] ?? 0 ?></div>
            <div class="stat-label">Ausentes</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fffde7;">⏰</div>
            <div class="stat-value" style="color:#f57f17;"><?= $stats['tardanza'] ?? 0 ?></div>
            <div class="stat-label">Tardanzas</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e0f7fa;">📝</div>
            <div class="stat-value" style="color:#00838f;"><?= $stats['justificado'] ?? 0 ?></div>
            <div class="stat-label">Justificados</div>
        </div>
    </div>

    <!-- Grid: asistencia general + hoy -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

        <!-- Asistencia general -->
        <div class="panel">
            <h3 style="font-size:.95rem;margin-bottom:12px;color:#555;">📈 Asistencia General del Curso</h3>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                <span style="font-size:.85rem;color:#666;">Efectividad (presentes + justificados)</span>
                <strong style="color:<?= $pct>=90?'#2e7d32':($pct>=75?'#f57f17':'#c62828') ?>"><?= $pct ?>%</strong>
            </div>
            <div style="background:#f0f0f0;border-radius:6px;height:12px;overflow:hidden;margin-bottom:16px;">
                <div style="height:100%;border-radius:6px;width:<?= $pct ?>%;background:<?= $pct>=90?'#4caf50':($pct>=75?'#ff9800':'#f44336') ?>;"></div>
            </div>
            <?php if($stats['total'] > 0): ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;">
                <?php
                $bars = [
                    ['presente',    '#4caf50', '✓ Presente'],
                    ['ausente',     '#f44336', '✗ Ausente'],
                    ['tardanza',    '#ff9800', '⏰ Tardanza'],
                    ['justificado', '#00acc1', '📝 Justificado'],
                ];
                foreach($bars as [$key, $color, $label]):
                    $n   = $stats[$key] ?? 0;
                    $pct_bar = round($n / $stats['total'] * 100);
                ?>
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
                        <span><?= $label ?></span>
                        <span style="font-weight:700;color:<?= $color ?>"><?= $n ?> (<?= $pct_bar ?>%)</span>
                    </div>
                    <div style="background:#f0f0f0;border-radius:4px;height:6px;">
                        <div style="height:100%;border-radius:4px;width:<?= $pct_bar ?>%;background:<?= $color ?>;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:#aaa;font-size:13px;text-align:center;margin-top:8px;">Sin registros de asistencia aún</p>
            <?php endif; ?>
        </div>

        <!-- Hoy -->
        <div class="panel">
            <h3 style="font-size:.95rem;margin-bottom:12px;color:#555;">📅 Hoy — <?= date('d/m/Y') ?></h3>
            <?php if($todayStats['total'] > 0): ?>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                <span style="font-size:.85rem;color:#666;">Asistencia del día</span>
                <strong style="color:<?= $todayPct>=90?'#2e7d32':($todayPct>=75?'#f57f17':'#c62828') ?>"><?= $todayPct ?>%</strong>
            </div>
            <div style="background:#f0f0f0;border-radius:6px;height:12px;overflow:hidden;margin-bottom:16px;">
                <div style="height:100%;border-radius:6px;width:<?= $todayPct ?>%;background:<?= $todayPct>=90?'#4caf50':($todayPct>=75?'#ff9800':'#f44336') ?>;"></div>
            </div>
            <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                <div style="text-align:center;padding:10px 14px;background:#e8f5e9;border-radius:8px;">
                    <div style="font-size:1.4rem;font-weight:700;color:#2e7d32;"><?= $todayStats['presente'] ?></div>
                    <div style="font-size:11px;color:#666;">Presentes</div>
                </div>
                <div style="text-align:center;padding:10px 14px;background:#ffebee;border-radius:8px;">
                    <div style="font-size:1.4rem;font-weight:700;color:#c62828;"><?= $todayStats['ausente'] ?></div>
                    <div style="font-size:11px;color:#666;">Ausentes</div>
                </div>
                <div style="text-align:center;padding:10px 14px;background:#fffde7;border-radius:8px;">
                    <div style="font-size:1.4rem;font-weight:700;color:#f57f17;"><?= $todayStats['tardanza'] ?></div>
                    <div style="font-size:11px;color:#666;">Tardanzas</div>
                </div>
                <div style="text-align:center;padding:10px 14px;background:#e0f7fa;border-radius:8px;">
                    <div style="font-size:1.4rem;font-weight:700;color:#00838f;"><?= $todayStats['justificado'] ?></div>
                    <div style="font-size:11px;color:#666;">Justificados</div>
                </div>
            </div>
            <?php else: ?>
            <div style="text-align:center;padding:30px 0;color:#aaa;">
                <div style="font-size:2rem;margin-bottom:8px;">📭</div>
                <p style="font-size:13px;">No hay registros de asistencia hoy</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grid: tendencia + top ausencias -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

        <!-- Tendencia 7 días -->
        <div class="panel">
            <h3 style="font-size:.95rem;margin-bottom:16px;color:#555;">📆 Últimos 7 días</h3>
            <?php if(!empty($trend)): ?>
            <div style="display:flex;align-items:flex-end;gap:8px;height:80px;">
                <?php foreach($trend as $day):
                    $dp = ($day['total'] > 0) ? round($day['presente'] / $day['total'] * 100) : 0;
                    $h  = max(4, round($dp * 0.8));
                    $col = $dp>=90?'#4caf50':($dp>=75?'#ff9800':'#f44336');
                ?>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
                    <span style="font-size:10px;color:#999;"><?= $dp ?>%</span>
                    <div style="width:100%;height:<?= $h ?>px;background:<?= $col ?>;border-radius:3px 3px 0 0;" title="<?= date('d/m', strtotime($day['date'])) ?>: <?= $dp ?>%"></div>
                    <span style="font-size:10px;color:#999;"><?= date('d/m', strtotime($day['date'])) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:#aaa;font-size:13px;text-align:center;padding:20px 0;">Sin datos de los últimos 7 días</p>
            <?php endif; ?>
        </div>

        <!-- Top ausencias -->
        <div class="panel">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <h3 style="font-size:.95rem;color:#555;">⚠️ Estudiantes con más ausencias</h3>
                <a href="?action=tutor_search_students" style="font-size:12px;color:#007bff;text-decoration:none;">Ver todos →</a>
            </div>
            <?php if(!empty($topAbsences)): ?>
            <?php foreach($topAbsences as $i => $row): ?>
            <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f5f5f5;">
                <span style="font-size:11px;color:#999;width:16px;text-align:right;"><?= $i+1 ?>.</span>
                <div style="flex:1;font-size:13px;color:#333;"><?= htmlspecialchars($row['student_name']) ?></div>
                <span class="badge <?= $row['total_ausencias']>=10?'badge-red':($row['total_ausencias']>=5?'badge-yellow':'badge-gray') ?>">
                    <?= $row['total_ausencias'] ?> faltas
                </span>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p style="color:#aaa;font-size:13px;text-align:center;padding:20px 0;">🎉 Sin ausencias registradas</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lista de estudiantes del curso -->
    <div class="table-wrap">
        <div class="table-info">
            <span>👥 <strong>Estudiantes del Curso</strong> — <?= count($students) ?></span>
            <a href="?action=tutor_search_students" class="btn btn-primary btn-sm">🔍 Buscar estudiante</a>
        </div>
        <?php if(empty($students)): ?>
        <div class="empty-state" style="padding:30px;">
            <div class="icon">👥</div>
            <p>No hay estudiantes matriculados en este curso.</p>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr><th>#</th><th>Estudiante</th></tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($students as $s): ?>
                <tr>
                    <td style="color:#999;font-size:12px;"><?= $i++ ?></td>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div>
</body>
</html>