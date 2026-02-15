<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Subject.php';
require_once BASE_PATH . '/models/SchoolYear.php';
require_once BASE_PATH . '/models/Shift.php';
require_once BASE_PATH . '/models/User.php';

class AcademicController {
    private $courseModel;
    private $subjectModel;
    private $schoolYearModel;
    private $shiftModel;
    private $userModel;

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $db = new Database();
        $this->courseModel = new Course($db);
        $this->subjectModel = new Subject($db);
        $this->schoolYearModel = new SchoolYear($db);
        $this->shiftModel = new Shift($db);
        $this->userModel = new User($db);
    }

    public function index() {
        $courses = $this->courseModel->getAll();
        $subjects = $this->subjectModel->getAll();
        $schoolYears = $this->schoolYearModel->getAll();
        $shifts = $this->shiftModel->getAll();

        include BASE_PATH . '/views/academic/index.php';
    }

    public function createCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activeYear = $this->schoolYearModel->getActive();
            
            if (!$activeYear) {
                header('Location: ?action=academic&error=no_active_year');
                exit;
            }
            
            $data = [
                ':institution_id' => $_SESSION['institution_id'],
                ':school_year_id' => $activeYear['id'],
                ':name' => Security::sanitize($_POST['name']),
                ':grade_level' => Security::sanitize($_POST['grade_level']),
                ':parallel' => Security::sanitize($_POST['parallel']),
                ':shift_id' => (int)$_POST['shift_id']
            ];

            $this->courseModel->create($data);
            header('Location: ?action=academic&course_success=1');
            exit;
        }
    }

    public function createSubject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                ':institution_id' => $_SESSION['institution_id'],
                ':name' => Security::sanitize($_POST['name']),
                ':code' => Security::sanitize($_POST['code'])
            ];

            $this->subjectModel->create($data);
            header('Location: ?action=academic&subject_success=1');
            exit;
        }
    }

    public function enrollStudents() {
        $activeYear = $this->schoolYearModel->getActive();
        $courses = $this->courseModel->getAll();
        $availableStudents = $this->userModel->getStudentsNotEnrolled($activeYear['id']);
        $allStudents = $this->userModel->getByRole('estudiante');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = (int)$_POST['course_id'];
            $studentIds = $_POST['student_ids'] ?? [];
            
            $enrolled = 0;
            $errors = 0;

            foreach ($studentIds as $studentId) {
                if ($this->courseModel->enrollStudent($courseId, (int)$studentId, $activeYear['id'])) {
                    $enrolled++;
                } else {
                    $errors++;
                }
            }

            header('Location: ?action=enroll_students&enrolled=' . $enrolled . '&errors=' . $errors);
            exit;
        }

        include BASE_PATH . '/views/academic/enroll.php';
    }

    public function viewCourseStudents() {
        $courseId = (int)($_GET['course_id'] ?? 0);
        
        if (!$courseId) {
            header('Location: ?action=academic');
            exit;
        }

        $course = $this->courseModel->getAll();
        $course = array_filter($course, fn($c) => $c['id'] == $courseId);
        $course = reset($course);

        $students = $this->courseModel->getEnrolledStudents($courseId);

        include BASE_PATH . '/views/academic/course_students.php';
    }
}