<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Representados - EcuAsist</title>
    <style>
        .children-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
        .child-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 20px; border-top: 4px solid #007bff; }
        .child-card h3 { font-size: 1rem; font-weight: 700; color: #007bff; margin-bottom: 16px; }
        .child-info { display: flex; flex-direction: column; gap: 8px; }
        .child-row { display: flex; font-size: 0.85rem; padding: 6px 0; border-bottom: 1px solid #f0f0f0; }
        .child-row:last-child { border-bottom: none; }
        .child-label { font-weight: 600; color: #888; width: 100px; flex-shrink: 0; }
        .child-val { color: #333; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Mis Representados
</div>

<div class="container">

    <!-- Header -->
    <div class="page-header teal">
        <div class="ph-icon">👨‍👩‍👧‍👦</div>
        <div>
            <h1>Mis Representados</h1>
            <p>Estudiantes vinculados a tu cuenta como representante</p>
        </div>
    </div>

    <?php if(empty($children)): ?>
    <div class="empty-state">
        <div class="icon">👧</div>
        <p>No tiene estudiantes asignados como representante.</p>
        <small style="color:#bbb;">Contacte con la autoridad para vincular a sus representados.</small>
    </div>
    <?php else: ?>
    <div class="children-grid">
        <?php foreach($children as $child): ?>
        <div class="child-card">
            <h3>👤 <?= htmlspecialchars($child['last_name'] . ' ' . $child['first_name']) ?>
                <?php if($child['is_primary']): ?>
                    <span class="badge badge-yellow" style="font-size:0.7rem;margin-left:6px;">Principal</span>
                <?php endif; ?>
            </h3>
            <div class="child-info">
                <div class="child-row">
                    <span class="child-label">Cédula</span>
                    <span class="child-val"><?= htmlspecialchars($child['dni'] ?? 'No registrada') ?></span>
                </div>
                <div class="child-row">
                    <span class="child-label">Parentesco</span>
                    <span class="child-val"><?= htmlspecialchars($child['relationship']) ?></span>
                </div>
                <div class="child-row">
                    <span class="child-label">Curso</span>
                    <span class="child-val"><?= htmlspecialchars($child['course_name'] ?? 'Sin asignar') ?></span>
                </div>
                <div class="child-row">
                    <span class="child-label">Jornada</span>
                    <span class="child-val"><?= $child['shift_name'] ? ucfirst($child['shift_name']) : '—' ?></span>
                </div>
            </div>
            <div style="margin-top:16px;">
                <a href="?action=child_attendance&student_id=<?= $child['id'] ?>" class="btn btn-primary">
                    📋 Ver Asistencia
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
