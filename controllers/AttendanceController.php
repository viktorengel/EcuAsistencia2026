<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Attendance.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Subject.php';
require_once BASE_PATH . '/models/SchoolYear.php';
require_once BASE_PATH . '/models/Shift.php';

class AttendanceController {
    private $attendanceModel;
    private $courseModel;
    private $subjectModel;
    private $schoolYearModel;
    private $shiftModel;

    public function __construct() {
        Security::requireLogin();
        $db = new Database();
        
        $this->attendanceModel = new Attendance($db);
        $this->courseModel = new Course($db);
        $this->subjectModel = new Subject($db);
        $this->schoolYearModel = new SchoolYear($db);
        $this->shiftModel = new Shift($db);
    }

    private function isWithin48BusinessHours($date) {
        $targetDate = new DateTime($date);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        
        // Si es el mismo día, siempre está permitido
        if ($targetDate->format('Y-m-d') === $today->format('Y-m-d')) {
            return true;
        }
        
        // Si la fecha es futura, no permitir
        if ($targetDate > $today) {
            return false;
        }
        
        $businessHours = 0;
        $current = clone $targetDate;
        
        // Contar horas hábiles desde la fecha objetivo hasta hoy
        while ($current < $today) {
            $current->modify('+1 day');
            $dayOfWeek = (int)$current->format('N'); // 1=Lunes, 7=Domingo
            
            // Solo contar si no es fin de semana (sábado=6, domingo=7)
            if ($dayOfWeek < 6) {
                $businessHours += 24;
            }
        }
        
        return $businessHours <= EDIT_ATTENDANCE_HOURS;
    }

    // Agregar nueva función para calcular fecha mínima
    private function getMinDateAllowed() {
        $today = new DateTime();
        $businessHoursNeeded = EDIT_ATTENDANCE_HOURS;
        $current = clone $today;
        
        while ($businessHoursNeeded > 0) {
            $current->modify('-1 day');
            $dayOfWeek = (int)$current->format('N');
            
            // Solo restar si no es fin de semana
            if ($dayOfWeek < 6) {
                $businessHoursNeeded -= 24;
            }
        }
        
        return $current->format('Y-m-d');
    }

    public function register() {
        if (!Security::hasRole(['docente', 'autoridad'])) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activeYear = $this->schoolYearModel->getActive();
            $date = $_POST['date'];
            $today = date('Y-m-d');

            // Validar fecha no futura
            if (strtotime($date) > strtotime($today)) {
                header('Location: ?action=attendance_register&error=future');
                exit;
            }

            // Validar 48 horas hábiles
            if (!$this->isWithin48BusinessHours($date)) {
                header('Location: ?action=attendance_register&error=toolate');
                exit;
            }

            $courseId = (int)$_POST['course_id'];
            $subjectId = (int)$_POST['subject_id'];
            $shiftId = (int)$_POST['shift_id'];
            $hourPeriod = Security::sanitize($_POST['hour_period']);

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'status_') === 0) {
                    $studentId = (int)str_replace('status_', '', $key);
                    $status = Security::sanitize($value);
                    $observation = Security::sanitize($_POST['obs_' . $studentId] ?? '');

                    $data = [
                        ':student_id' => $studentId,
                        ':course_id' => $courseId,
                        ':subject_id' => $subjectId,
                        ':teacher_id' => $_SESSION['user_id'],
                        ':school_year_id' => $activeYear['id'],
                        ':shift_id' => $shiftId,
                        ':date' => $date,
                        ':hour_period' => $hourPeriod,
                        ':status' => $status,
                        ':observation' => $observation
                    ];

                    $this->attendanceModel->create($data);
                }
            }

            header('Location: ?action=attendance_register&success=1');
            exit;
        }

        // Si es autoridad: todos los cursos
        // Si es docente: solo sus cursos asignados
        if (Security::hasRole('autoridad')) {
            $courses = $this->courseModel->getAll();
        } else {
            $courses = $this->getTeacherCourses($_SESSION['user_id']);
        }
        
        $subjects = $this->subjectModel->getAll();
        $shifts = $this->shiftModel->getAll();
        $minDate = $this->calculateMinDate();

        include BASE_PATH . '/views/attendance/register.php';
    }

    private function getTeacherCourses($teacherId) {
        $sql = "SELECT DISTINCT c.*, sh.name as shift_name
                FROM courses c
                INNER JOIN teacher_assignments ta ON c.id = ta.course_id
                INNER JOIN shifts sh ON c.shift_id = sh.id
                WHERE ta.teacher_id = :teacher_id
                ORDER BY c.name";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }
    
    private function calculateMinDate() {
        $date = new DateTime();
        $hoursBack = 0;
        
        while ($hoursBack < 48) {
            $date->modify('-1 day');
            $dayOfWeek = $date->format('N'); // 1=Lunes, 7=Domingo
            
            // Solo contar días laborables (lunes a viernes)
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $hoursBack += 24;
            }
        }
        
        return $date->format('Y-m-d');
    }

    public function getStudents() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = (int)$_POST['course_id'];
            $students = $this->courseModel->getStudents($courseId);
            
            header('Content-Type: application/json');
            echo json_encode($students);
            exit;
        }
    }

    public function view() {
        if (!Security::hasRole(['docente', 'inspector', 'autoridad'])) {
            die('Acceso denegado');
        }

        $courses = $this->courseModel->getAll();
        $attendances = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = (int)$_POST['course_id'];
            $date = $_POST['date'];
            $attendances = $this->attendanceModel->getByCourse($courseId, $date);
        }

        include BASE_PATH . '/views/attendance/view.php';
    }

    public function myAttendance() {
        if (!Security::hasRole('estudiante')) {
            die('Acceso denegado');
        }

        $studentId = $_SESSION['user_id'];
        $attendances = $this->attendanceModel->getByStudent($studentId);

        include BASE_PATH . '/views/attendance/my_attendance.php';
    }

    public function calendar() {
        if (!Security::hasRole(['docente', 'autoridad', 'inspector'])) {
            die('Acceso denegado');
        }

        $courses = $this->courseModel->getAll();
        
        if (isset($_GET['course_id'])) {
            $courseId = (int)$_GET['course_id'];
            $month = $_GET['month'] ?? date('Y-m');
            
            list($year, $monthNum) = explode('-', $month);
            
            $monthNames = [
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
            ];
            
            $monthName = $monthNames[$monthNum];
            
            // Calcular mes anterior y siguiente
            $prevMonth = date('Y-m', strtotime($month . '-01 -1 month'));
            $nextMonth = date('Y-m', strtotime($month . '-01 +1 month'));
            
            // Generar calendario
            $firstDay = date('w', strtotime($year . '-' . $monthNum . '-01'));
            $daysInMonth = date('t', strtotime($year . '-' . $monthNum . '-01'));
            
            $calendarDays = [];
            
            // Días vacíos al inicio
            for ($i = 0; $i < $firstDay; $i++) {
                $calendarDays[] = ['day' => '', 'classes' => ''];
            }
            
            // Días del mes
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $year . '-' . $monthNum . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                $dayOfWeek = date('w', strtotime($date));
                
                $classes = [];
                if ($date == date('Y-m-d')) $classes[] = 'today';
                if ($dayOfWeek == 0 || $dayOfWeek == 6) $classes[] = 'weekend';
                
                // Obtener estadísticas de asistencia del día
                $attendance = $this->attendanceModel->getDayStats($courseId, $date);
                
                if ($attendance && $attendance['total'] > 0) {
                    $classes[] = 'has-attendance';
                }
                
                $calendarDays[] = [
                    'day' => $day,
                    'classes' => implode(' ', $classes),
                    'attendance' => $attendance
                ];
            }
            
            include BASE_PATH . '/views/attendance/calendar.php';
        } else {
            include BASE_PATH . '/views/attendance/calendar.php';
        }
    }

    public function getCourseSubjects() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        
        $courseId = (int)($_GET['course_id'] ?? 0);
        
        if (!$courseId) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        
        $sql = "SELECT DISTINCT ta.subject_id, s.name as subject_name
                FROM teacher_assignments ta
                INNER JOIN subjects s ON ta.subject_id = s.id
                WHERE ta.course_id = :course_id
                ORDER BY s.name";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($subjects);
        exit;
    }

    public function getTeacherCourseSubjects() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autenticado']);
            exit;
        }
        
        $courseId = (int)($_GET['course_id'] ?? 0);
        
        if (!$courseId) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        
        // Si es autoridad: todas las asignaturas del curso
        // Si es docente: solo las que él dicta en ese curso
        if (Security::hasRole('autoridad')) {
            $sql = "SELECT DISTINCT ta.subject_id, s.name as subject_name
                    FROM teacher_assignments ta
                    INNER JOIN subjects s ON ta.subject_id = s.id
                    WHERE ta.course_id = :course_id
                    ORDER BY s.name";
            
            $params = [':course_id' => $courseId];
        } else {
            $sql = "SELECT DISTINCT ta.subject_id, s.name as subject_name
                    FROM teacher_assignments ta
                    INNER JOIN subjects s ON ta.subject_id = s.id
                    WHERE ta.course_id = :course_id 
                    AND ta.teacher_id = :teacher_id
                    ORDER BY s.name";
            
            $params = [
                ':course_id' => $courseId,
                ':teacher_id' => $_SESSION['user_id']
            ];
        }
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute($params);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($subjects);
        exit;
    }
}