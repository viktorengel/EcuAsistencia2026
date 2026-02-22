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

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $db = new Database();
        $this->scheduleModel = new ClassSchedule($db);
        $this->courseModel = new Course($db);
        $this->subjectModel = new Subject($db);
        $this->userModel = new User($db);
        $this->schoolYearModel = new SchoolYear($db);
    }

    public function index() {
        $courses = $this->courseModel->getAll();
        include BASE_PATH . '/views/schedule/index.php';
    }

    public function manageCourse() {
        $courseId = (int)($_GET['course_id'] ?? 0);
        
        if (!$courseId) {
            header('Location: ?action=schedules');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activeYear = $this->schoolYearModel->getActive();

            $data = [
                ':course_id' => $courseId,
                ':subject_id' => (int)$_POST['subject_id'],
                ':teacher_id' => !empty($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : null,
                ':school_year_id' => $activeYear['id'],
                ':day_of_week' => $_POST['day_of_week'],
                ':period_number' => (int)$_POST['period_number']
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
        $course = array_filter($courses, fn($c) => $c['id'] == $courseId);
        $course = reset($course);

        $schedule = $this->scheduleModel->getByCourse($courseId, $activeYear['id']);
        $subjects = $this->subjectModel->getAll();
        $teachers = $this->userModel->getByRole('docente');

        include BASE_PATH . '/views/schedule/manage.php';
    }

    public function deleteClass() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        
        // Obtener asignaturas del curso (con o sin docente asignado)
        $sql = "SELECT DISTINCT
                    s.id as subject_id,
                    s.name as subject_name,
                    ta.teacher_id,
                    COALESCE(CONCAT(u.last_name, ' ', u.first_name), 'Sin docente') as teacher_name,
                    COALESCE(cs.hours_per_week, 1) as hours_per_week
                FROM course_subjects cs
                INNER JOIN subjects s ON cs.subject_id = s.id
                LEFT JOIN teacher_assignments ta ON ta.subject_id = s.id
                    AND ta.course_id = :course_id AND ta.is_tutor = 0
                LEFT JOIN users u ON u.id = ta.teacher_id
                WHERE cs.course_id = :course_id2
                ORDER BY s.name";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':course_id' => $courseId, ':course_id2' => $courseId]);
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
        
        $db = new Database();
        $activeYear = $this->schoolYearModel->getActive();
        
        $sql = "SELECT s.name as subject_name, CONCAT(u.last_name, ' ', u.first_name) as teacher_name
                FROM class_schedule cs
                INNER JOIN subjects s ON cs.subject_id = s.id
                INNER JOIN users u ON cs.teacher_id = u.id
                WHERE cs.course_id = :course_id 
                AND cs.day_of_week = :day 
                AND cs.period_number = :period
                AND cs.school_year_id = :school_year_id
                LIMIT 1";
        
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([
            ':course_id' => $courseId,
            ':day' => $day,
            ':period' => $period,
            ':school_year_id' => $activeYear['id']
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
}