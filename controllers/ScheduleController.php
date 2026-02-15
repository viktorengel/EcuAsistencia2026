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
                ':teacher_id' => (int)$_POST['teacher_id'],
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
}