<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/TeacherAssignment.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Subject.php';
require_once BASE_PATH . '/models/SchoolYear.php';
require_once BASE_PATH . '/models/User.php';

class AssignmentController {
    private $assignmentModel;
    private $courseModel;
    private $subjectModel;
    private $schoolYearModel;
    private $userModel;

    public function __construct() {
        $db = new Database();
        $this->assignmentModel = new TeacherAssignment($db);
        $this->courseModel = new Course($db);
        $this->subjectModel = new Subject($db);
        $this->schoolYearModel = new SchoolYear($db);
        $this->userModel = new User($db);
    }

    public function index() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $courses = $this->courseModel->getAll();
        $subjects = $this->subjectModel->getAll();
        $teachers = $this->userModel->getByRole('docente');
        $activeYear = $this->schoolYearModel->getActive();
        $assignments = $this->assignmentModel->getAll();

        include BASE_PATH . '/views/assignments/index.php';
    }

    public function assign() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activeYear = $this->schoolYearModel->getActive();

            $data = [
                ':teacher_id' => (int)$_POST['teacher_id'],
                ':course_id' => (int)$_POST['course_id'],
                ':subject_id' => (int)$_POST['subject_id'],
                ':school_year_id' => $activeYear['id'],
                ':is_tutor' => 0
            ];

            $result = $this->assignmentModel->assign($data);
            
            if ($result['success']) {
                header('Location: ?action=assignments&success=1');
            } else {
                header('Location: ?action=assignments&error=' . urlencode($result['message']));
            }
            exit;
        }
    }

    public function setTutor() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activeYear = $this->schoolYearModel->getActive();
            $courseId = (int)$_POST['course_id'];
            $teacherId = (int)$_POST['teacher_id'];

            $result = $this->assignmentModel->setTutor($courseId, $teacherId, $activeYear['id']);
            
            if ($result['success']) {
                header('Location: ?action=assignments&tutor_success=1');
            } else {
                header('Location: ?action=assignments&tutor_error=' . urlencode($result['message']));
            }
            exit;
        }
    }

    public function removeTutor() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $activeYear = $this->schoolYearModel->getActive();
            $courseId = (int)$_POST['course_id'];
            
            $db = new Database();
            $sql = "UPDATE teacher_assignments 
                    SET is_tutor = 0 
                    WHERE course_id = :course_id 
                    AND school_year_id = :school_year_id";
            
            $stmt = $db->connect()->prepare($sql);
            $stmt->execute([
                ':course_id' => $courseId,
                ':school_year_id' => $activeYear['id']
            ]);
            
            header('Location: ?action=assignments&tutor_removed=1');
            exit;
        }
    }

    public function remove() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $assignmentId = (int)$_POST['assignment_id'];
            $result = $this->assignmentModel->remove($assignmentId);
            
            if ($result['success']) {
                header('Location: ?action=assignments&removed=1');
            } else {
                header('Location: ?action=assignments&error=' . urlencode($result['message']));
            }
            exit;
        }
    }

    public function viewByCourse() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $courseId = (int)($_GET['course_id'] ?? 0);
        
        if (!$courseId) {
            header('Location: ?action=assignments');
            exit;
        }

        $courses = $this->courseModel->getAll();
        $course = array_filter($courses, fn($c) => $c['id'] == $courseId);
        $course = reset($course);

        $activeYear = $this->schoolYearModel->getActive();
        $assignments = $this->assignmentModel->getByCourse($courseId);
        $tutor = $this->assignmentModel->getTutorByCourse($courseId, $activeYear['id']);

        include BASE_PATH . '/views/assignments/view_course.php';
    }

    public function getCourseTeachers() {
        // No verificar roles para AJAX
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
        
        $sql = "SELECT DISTINCT ta.teacher_id, 
                CONCAT(u.last_name, ' ', u.first_name) as teacher_name
                FROM teacher_assignments ta
                INNER JOIN users u ON ta.teacher_id = u.id
                WHERE ta.course_id = :course_id
                ORDER BY u.last_name, u.first_name";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($teachers);
        exit;
    }
}