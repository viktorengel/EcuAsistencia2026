<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Asistencia - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .calendar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; margin-top: 20px; }
        .calendar-header { text-align: center; font-weight: bold; padding: 10px; background: #f8f9fa; }
        .calendar-day { border: 1px solid #ddd; padding: 10px; min-height: 80px; border-radius: 4px; position: relative; }
        .calendar-day.today { background: #fff3cd; border-color: #ffc107; }
        .calendar-day.has-attendance { background: #d4edda; }
        .calendar-day.weekend { background: #f8f9fa; color: #999; }
        .day-number { font-weight: bold; margin-bottom: 5px; }
        .attendance-summary { font-size: 11px; color: #666; }
        .month-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #0056b3; }
        .form-group { margin-bottom: 15px; }
        select { padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <div class="form-group">
                <label>Seleccionar Curso</label>
                <select id="course-select" onchange="loadCalendar()">
                    <option value="">Seleccionar curso...</option>
                    <?php foreach($courses as $course): ?>
                        <option value="<?= $course['id'] ?>" <?= isset($_GET['course_id']) && $_GET['course_id'] == $course['id'] ? 'selected' : '' ?>>
                            <?= $course['name'] ?> - <?= $course['shift_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if(isset($_GET['course_id'])): ?>
            <div class="month-nav">
                <a href="?action=attendance_calendar&course_id=<?= $_GET['course_id'] ?>&month=<?= $prevMonth ?>" class="btn">← Anterior</a>
                <h2><?= $monthName ?> <?= $year ?></h2>
                <a href="?action=attendance_calendar&course_id=<?= $_GET['course_id'] ?>&month=<?= $nextMonth ?>" class="btn">Siguiente →</a>
            </div>

            <div class="calendar">
                <?php foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $day): ?>
                    <div class="calendar-header"><?= $day ?></div>
                <?php endforeach; ?>

                <?php foreach($calendarDays as $day): ?>
                    <div class="calendar-day <?= $day['classes'] ?>">
                        <?php if($day['day']): ?>
                            <div class="day-number"><?= $day['day'] ?></div>
                            <?php if(isset($day['attendance'])): ?>
                                <div class="attendance-summary">
                                    ✓ <?= $day['attendance']['presente'] ?> 
                                    ✗ <?= $day['attendance']['ausente'] ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function loadCalendar() {
            const courseId = document.getElementById('course-select').value;
            if (courseId) {
                window.location.href = '?action=attendance_calendar&course_id=' + courseId;
            }
        }
    </script>
</body>
</html>