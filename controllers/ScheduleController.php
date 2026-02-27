<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/ClassSchedule.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Subject.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/SchoolYear.php';

class ScheduleController {
    private $scheduleModel;
    private $courseModel;
    private $subjectModel;
    private $userModel;
    private $schoolYearModel;
    private $db; // <-- AÑADE ESTA LÍNEA (propiedad para la conexión)

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $this->db = new Database(); // <-- GUARDAR LA CONEXIÓN EN LA PROPIEDAD
        $this->scheduleModel = new ClassSchedule($this->db);
        $this->courseModel = new Course($this->db);
        $this->subjectModel = new Subject($this->db);
        $this->userModel = new User($this->db);
        $this->schoolYearModel = new SchoolYear($this->db);
    }

    public function index() {
        $courses = $this->courseModel->getAll();
        include BASE_PATH . '/views/schedule/index.php';
    }

    public function manageCourse() {
        $courseId = (int)($_GET['course_id'] ?? 0);

        if (!$courseId) {
            header('Location: ?action=schedule');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $activeYear = $this->schoolYearModel->getActive();

            if (!$activeYear) {
                header('Location: ?action=manage_schedule&course_id=' . $courseId . '&error=' . urlencode('No hay un año escolar activo. Configure el año escolar primero.'));
                exit;
            }

            // Verificar si la materia tiene un docente asignado en teacher_assignments
            $teacherCheck = $this->db->connect()->prepare(
                'SELECT teacher_id FROM teacher_assignments 
                 WHERE course_id = :course_id 
                   AND subject_id = :subject_id 
                   AND school_year_id = :school_year_id 
                   AND is_tutor = 0 
                 LIMIT 1'
            );
            $teacherCheck->execute([
                'course_id' => $courseId,
                'subject_id' => (int)$_POST['subject_id'],
                'school_year_id' => $activeYear['id']
            ]);
            $teacherData = $teacherCheck->fetch();

            // Priorizar: 1. POST teacher_id, 2. teacher_assignments, 3. null
            $teacherId = !empty($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : 
                        ($teacherData ? $teacherData['teacher_id'] : null);

            $data = [
                'course_id' => $courseId,
                'subject_id' => (int)$_POST['subject_id'],
                'teacher_id' => $teacherId,
                'school_year_id' => $activeYear['id'],
                'day_of_week' => $_POST['day_of_week'],
                'period_number' => (int)$_POST['period_number']
            ];

            $result = $this->scheduleModel->create($data);

            if ($result['success']) {
                header('Location: ?action=manage_schedule&course_id=' . $courseId . '&success=1');
            } else {
                header('Location: ?action=manage_schedule&course_id=' . $courseId . '&error=' . urlencode($result['message']));
            }
            exit;
        }

        $activeYear = $this->schoolYearModel->getActive();
        $courses = $this->courseModel->getAll();
        $course = array_filter($courses, function($c) use ($courseId) { return $c['id'] == $courseId; });
        $course = reset($course);

        if (!$activeYear) {
            header('Location: ?action=schedule&error=' . urlencode('No hay un año escolar activo. Configure el año escolar primero.'));
            exit;
        }

        $schedule = $this->scheduleModel->getByCourse($courseId, $activeYear['id']);
        $subjects = $this->subjectModel->getAll();
        $teachers = $this->userModel->getByRole('docente');

        include BASE_PATH . '/views/schedule/manage.php';
    }

    public function deleteClass() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $scheduleId = (int)$_POST['schedule_id'];
            $courseId = (int)$_POST['course_id'];

            $this->scheduleModel->delete($scheduleId);
            header('Location: ?action=manage_schedule&course_id=' . $courseId . '&deleted=1');
            exit;
        }
    }

    public function getCourseSubjectsSchedule() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        $courseId = (int)($_GET['course_id'] ?? 0);

        if (!$courseId) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        // Obtener asignaturas del curso (ahora incluye TODAS, incluso sin docente)
        $sql = 'SELECT DISTINCT 
                        s.id as subject_id, 
                        s.name as subject_name, 
                        ta.teacher_id, 
                        COALESCE(CONCAT(u.last_name, " ", u.first_name), "Sin docente") as teacher_name,
                        COALESCE(cs.hours_per_week, 1) as hours_per_week
                FROM course_subjects cs
                INNER JOIN subjects s ON cs.subject_id = s.id
                LEFT JOIN teacher_assignments ta ON ta.subject_id = s.id 
                    AND ta.course_id = :course_id 
                    AND ta.school_year_id = (SELECT id FROM school_years WHERE is_active = 1 LIMIT 1)
                    AND ta.is_tutor = 0
                LEFT JOIN users u ON u.id = ta.teacher_id
                WHERE cs.course_id = :course_id2
                ORDER BY s.name';

        $stmt = $this->db->connect()->prepare($sql); // <-- AHORA SÍ FUNCIONA PORQUE $this->db EXISTE
        $stmt->execute(['course_id' => $courseId, 'course_id2' => $courseId]);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($subjects);
        exit;
    }

    public function checkScheduleConflict() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['exists' => false]);
            exit;
        }

        $courseId = (int)($_GET['course_id'] ?? 0);
        $day = $_GET['day'] ?? '';
        $period = (int)($_GET['period'] ?? 0);

        if (!$courseId || !$day || !$period) {
            header('Content-Type: application/json');
            echo json_encode(['exists' => false]);
            exit;
        }

        $activeYear = $this->schoolYearModel->getActive();

        $sql = 'SELECT s.name as subject_name, CONCAT(u.last_name, " ", u.first_name) as teacher_name
                FROM class_schedule cs
                INNER JOIN subjects s ON cs.subject_id = s.id
                LEFT JOIN users u ON cs.teacher_id = u.id
                WHERE cs.course_id = :course_id
                  AND cs.day_of_week = :day
                  AND cs.period_number = :period
                  AND cs.school_year_id = :school_year_id
                LIMIT 1';

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([
            'course_id' => $courseId,
            'day' => $day,
            'period' => $period,
            'school_year_id' => $activeYear['id']
        ]);

        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        $result = [
            'exists' => $existing ? true : false,
            'subject_name' => $existing['subject_name'] ?? null,
            'teacher_name' => $existing['teacher_name'] ?? null
        ];

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function swapClass() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success'=>false]); exit; }

        $sidA     = (int)($_POST['sid_a']     ?? 0);
        $sidB     = (int)($_POST['sid_b']     ?? 0);
        $courseId = (int)($_POST['course_id'] ?? 0);

        if (!$sidA || !$sidB || !$courseId) { echo json_encode(['success'=>false,'message'=>'Datos incompletos']); exit; }

        try {
            $pdo = (new Database())->connect();
            $stmt = $pdo->prepare("SELECT id, day_of_week, period_number FROM class_schedule WHERE id IN (:a,:b) AND course_id = :cid");
            $stmt->execute([':a'=>$sidA,':b'=>$sidB,':cid'=>$courseId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) !== 2) { echo json_encode(['success'=>false,'message'=>'Registros no encontrados']); exit; }

            $a = $rows[0]['id'] == $sidA ? $rows[0] : $rows[1];
            $b = $rows[0]['id'] == $sidB ? $rows[0] : $rows[1];

            $upd = $pdo->prepare("UPDATE class_schedule SET day_of_week=:day, period_number=:per WHERE id=:id");
            $upd->execute([':day'=>$b['day_of_week'],':per'=>$b['period_number'],':id'=>$sidA]);
            $upd->execute([':day'=>$a['day_of_week'],':per'=>$a['period_number'],':id'=>$sidB]);

            echo json_encode(['success'=>true]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
        }
        exit;
    }

    // Mover una clase a una celda vacía
    public function moveClass() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success'=>false]); exit; }

        $sid      = (int)($_POST['schedule_id']  ?? 0);
        $day      = $_POST['day_of_week']         ?? '';
        $period   = (int)($_POST['period_number'] ?? 0);
        $courseId = (int)($_POST['course_id']     ?? 0);

        if (!$sid || !$day || !$period || !$courseId) { echo json_encode(['success'=>false,'message'=>'Datos incompletos']); exit; }

        try {
            $pdo = (new Database())->connect();

            // Verificar que la celda destino esté vacía
            $check = $pdo->prepare("SELECT id FROM class_schedule WHERE course_id=:cid AND day_of_week=:day AND period_number=:per");
            $check->execute([':cid'=>$courseId,':day'=>$day,':per'=>$period]);
            if ($check->fetch()) { echo json_encode(['success'=>false,'message'=>'La celda destino ya está ocupada']); exit; }

            // Verificar que la escuela activa coincida
            $upd = $pdo->prepare("UPDATE class_schedule SET day_of_week=:day, period_number=:per WHERE id=:id AND course_id=:cid");
            $upd->execute([':day'=>$day,':per'=>$period,':id'=>$sid,':cid'=>$courseId]);

            echo json_encode(['success'=>true]);
        } catch (Exception $e) {
            echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
        }
        exit;
    }
}