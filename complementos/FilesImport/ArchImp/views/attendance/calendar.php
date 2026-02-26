<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Asistencia - EcuAsist</title>
    <style>
        .calendar-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; margin-top: 16px; }
        .cal-header { text-align: center; font-size: 0.78rem; font-weight: 600; color: #555; padding: 8px; background: #f8f9fa; border-radius: 4px; }
        .cal-day { border: 1px solid #e0e0e0; padding: 8px; min-height: 70px; border-radius: 6px; background: #fff; position: relative; font-size: 0.82rem; }
        .cal-day.empty { background: #f8f9fa; border: none; }
        .cal-day.today { background: #fff8e1; border-color: #ffc107; }
        .cal-day.has-data { background: #f0faf0; border-color: #28a745; }
        .cal-day.weekend { background: #fafafa; color: #bbb; }
        .day-num { font-weight: 700; font-size: 0.9rem; margin-bottom: 4px; }
        .day-summary { font-size: 0.72rem; color: #666; line-height: 1.4; }
        .day-summary .ok { color: #28a745; font-weight: 600; }
        .day-summary .no { color: #dc3545; font-weight: 600; }
        .month-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .month-nav h2 { font-size: 1.1rem; font-weight: 600; color: #333; }
    </style>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    <a href="?action=view_course_attendance">Asistencia</a> &rsaquo;
    Calendario
</div>

<div class="container">

    <!-- Header -->
    <div class="page-header teal">
        <div class="ph-icon">📅</div>
        <div>
            <h1>Calendario de Asistencia</h1>
            <p>Vista mensual del registro de asistencia por curso</p>
        </div>
    </div>

    <!-- Selector de curso -->
    <div class="panel" style="margin-bottom:16px;">
        <div class="form-row">
            <div class="form-group" style="flex:2;">
                <label>Seleccionar Curso</label>
                <select id="course-select" class="form-control" onchange="loadCalendar()">
                    <option value="">Seleccionar curso...</option>
                    <?php foreach($courses as $course): ?>
                        <option value="<?= $course['id'] ?>"
                            <?= isset($_GET['course_id']) && $_GET['course_id'] == $course['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['name']) ?> — <?= ucfirst($course['shift_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <?php if(isset($_GET['course_id'])): ?>

    <!-- Navegación del mes -->
    <div class="month-nav">
        <a href="?action=attendance_calendar&course_id=<?= (int)$_GET['course_id'] ?>&month=<?= htmlspecialchars($prevMonth) ?>"
           class="btn btn-outline">← Anterior</a>
        <h2><?= htmlspecialchars($monthName) ?> <?= $year ?></h2>
        <a href="?action=attendance_calendar&course_id=<?= (int)$_GET['course_id'] ?>&month=<?= htmlspecialchars($nextMonth) ?>"
           class="btn btn-outline">Siguiente →</a>
    </div>

    <!-- Calendario -->
    <div class="panel" style="padding:16px;">
        <div class="calendar-grid">
            <?php foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie'] as $d): ?>
                <div class="cal-header"><?= $d ?></div>
            <?php endforeach; ?>

            <?php foreach($calendarDays as $day): ?>
                <?php if(!$day['day']): ?>
                    <div class="cal-day empty"></div>
                <?php else: ?>
                    <div class="cal-day <?= $day['classes'] ?>">
                        <div class="day-num"><?= $day['day'] ?></div>
                        <?php if(isset($day['attendance'])): ?>
                            <div class="day-summary">
                                <span class="ok">✓ <?= $day['attendance']['presente'] ?></span>
                                &nbsp;
                                <span class="no">✗ <?= $day['attendance']['ausente'] ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Leyenda -->
        <div style="margin-top:16px;display:flex;gap:16px;flex-wrap:wrap;font-size:0.8rem;color:#666;">
            <span><span style="display:inline-block;width:12px;height:12px;background:#f0faf0;border:1px solid #28a745;border-radius:2px;"></span> Con asistencia</span>
            <span><span style="display:inline-block;width:12px;height:12px;background:#fff8e1;border:1px solid #ffc107;border-radius:2px;"></span> Hoy</span>
        </div>
    </div>

    <?php else: ?>
    <div class="empty-state">
        <div class="icon">📅</div>
        <p>Seleccione un curso para ver el calendario.</p>
    </div>
    <?php endif; ?>

</div>

<script>
function loadCalendar() {
    const id = document.getElementById('course-select').value;
    if (id) window.location.href = '?action=attendance_calendar&course_id=' + id;
}
</script>
</body>
</html>