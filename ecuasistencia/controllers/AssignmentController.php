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
                header('Location: ?action=tutor_management&tutor_success=1');
            } else {
                header('Location: ?action=tutor_management&tutor_error=' . urlencode($result['message']));
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
            
            header('Location: ?action=tutor_management&tutor_removed=1');
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
        
        // Obtener año lectivo activo
        $db = new Database();
        $sqlYear = "SELECT id FROM school_years WHERE is_active = 1 LIMIT 1";
        $stmtYear = $db->connect()->query($sqlYear);
        $activeYear = $stmtYear->fetch();
        
        if (!$activeYear) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        
        // Solo docentes del curso que:
        // 1. NO son tutores de otro curso
        // 2. NO son tutores del curso actual
        $sql = "SELECT DISTINCT ta.teacher_id, 
                CONCAT(u.last_name, ' ', u.first_name) as teacher_name
                FROM teacher_assignments ta
                INNER JOIN users u ON ta.teacher_id = u.id
                WHERE ta.course_id = :course_id
                AND ta.teacher_id NOT IN (
                    SELECT teacher_id 
                    FROM teacher_assignments 
                    WHERE is_tutor = 1 
                    AND school_year_id = :school_year_id
                )
                ORDER BY u.last_name, u.first_name";
        
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([
            ':course_id' => $courseId,
            ':school_year_id' => $activeYear['id']
        ]);
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($teachers);
        exit;
    }

    public function tutorManagement() {
        $courses = $this->courseModel->getAll();
        $assignments = $this->assignmentModel->getAll();

        include BASE_PATH . '/views/assignments/tutor.php';
    }

    public function checkCourseTutor() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(null);
            exit;
        }
        
        $courseId = (int)($_GET['course_id'] ?? 0);
        
        if (!$courseId) {
            header('Content-Type: application/json');
            echo json_encode(null);
            exit;
        }
        
        $db = new Database();
        require_once BASE_PATH . '/models/SchoolYear.php';
        $yearModel = new SchoolYear($db);
        $activeYear = $yearModel->getActive();
        
        $sql = "SELECT CONCAT(u.last_name, ' ', u.first_name) as tutor_name
                FROM teacher_assignments ta
                INNER JOIN users u ON ta.teacher_id = u.id
                WHERE ta.course_id = :course_id 
                AND ta.school_year_id = :school_year_id 
                AND ta.is_tutor = 1
                LIMIT 1";
        
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([
            ':course_id' => $courseId,
            ':school_year_id' => $activeYear['id']
        ]);
        
        $tutor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($tutor);
        exit;
    }
}