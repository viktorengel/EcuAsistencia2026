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

    // ============================================
    // CRUD AÑOS LECTIVOS
    // ============================================

    public function createSchoolYear() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            include BASE_PATH . '/views/academic/create.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            // Validaciones
            if (empty($_POST['name'])) {
                $errors[] = "El nombre es obligatorio";
            }
            if (empty($_POST['start_date'])) {
                $errors[] = "La fecha de inicio es obligatoria";
            }
            if (empty($_POST['end_date'])) {
                $errors[] = "La fecha de fin es obligatoria";
            }

            if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
                if (strtotime($_POST['end_date']) <= strtotime($_POST['start_date'])) {
                    $errors[] = "La fecha de fin debe ser posterior a la fecha de inicio";
                }

                // Verificar solapamiento de fechas
                if ($this->schoolYearModel->checkOverlap($_POST['start_date'], $_POST['end_date'])) {
                    $errors[] = "Ya existe un año lectivo con fechas que se solapan";
                }
            }

            if (!empty($errors)) {
                include BASE_PATH . '/views/academic/school_year_create.php';
                return;
            }

            // Crear año lectivo
            $data = [
                'institution_id' => $_SESSION['institution_id'],
                'name' => Security::sanitize($_POST['name']),
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Si se marca como activo, desactivar los demás
            if ($data['is_active']) {
                $this->schoolYearModel->activate(0); // Desactiva todos
            }

            if ($this->schoolYearModel->create($data)) {
                // Si se creó como activo, activar el nuevo
                if ($data['is_active']) {
                    $db = new Database();
                    $newId = $db->connect()->lastInsertId();
                    $this->schoolYearModel->activate($newId);
                }

                header('Location: ?action=academic&sy_created=1');
                exit;
            } else {
                $errors[] = "Error al crear el año lectivo";
                include BASE_PATH . '/views/academic/school_year_create.php';
            }
        }
    }

    public function editSchoolYear() {
        $yearId = (int)$_GET['id'];
        $year = $this->schoolYearModel->findById($yearId);

        if (!$year) {
            header('Location: ?action=academic&error=year_not_found');
            exit;
        }

        // Verificar institución
        if ($year['institution_id'] != $_SESSION['institution_id']) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            include BASE_PATH . '/views/academic/school_year_edit.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            // Validaciones
            if (empty($_POST['name'])) {
                $errors[] = "El nombre es obligatorio";
            }
            if (empty($_POST['start_date'])) {
                $errors[] = "La fecha de inicio es obligatoria";
            }
            if (empty($_POST['end_date'])) {
                $errors[] = "La fecha de fin es obligatoria";
            }

            if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
                if (strtotime($_POST['end_date']) <= strtotime($_POST['start_date'])) {
                    $errors[] = "La fecha de fin debe ser posterior a la fecha de inicio";
                }

                // Verificar solapamiento (excluyendo el actual)
                if ($this->schoolYearModel->checkOverlap($_POST['start_date'], $_POST['end_date'], $yearId)) {
                    $errors[] = "Ya existe otro año lectivo con fechas que se solapan";
                }
            }

            if (!empty($errors)) {
                include BASE_PATH . '/views/academic/school_year_edit.php';
                return;
            }

            // Actualizar año lectivo
            $data = [
                'id' => $yearId,
                'name' => Security::sanitize($_POST['name']),
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date']
            ];

            if ($this->schoolYearModel->update($data)) {
                header('Location: ?action=academic&sy_updated=1');
                exit;
            } else {
                $errors[] = "Error al actualizar el año lectivo";
                include BASE_PATH . '/views/academic/school_year_edit.php';
            }
        }
    }

    public function deleteSchoolYear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $yearId = (int)$_POST['year_id'];
            $year = $this->schoolYearModel->findById($yearId);

            if (!$year) {
                header('Location: ?action=academic&error=year_not_found');
                exit;
            }

            // Verificar institución
            if ($year['institution_id'] != $_SESSION['institution_id']) {
                die('Acceso denegado');
            }

            // No permitir eliminar año activo
            if ($year['is_active'] == 1) {
                header('Location: ?action=academic&error=cannot_delete_active');
                exit;
            }

            if ($this->schoolYearModel->delete($yearId)) {
                header('Location: ?action=academic&sy_deleted=1');
                exit;
            } else {
                header('Location: ?action=academic&error=has_courses');
                exit;
            }
        }
    }

    public function activateSchoolYear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $yearId = (int)$_POST['year_id'];
            $year = $this->schoolYearModel->findById($yearId);

            if (!$year) {
                header('Location: ?action=academic&error=year_not_found');
                exit;
            }

            // Verificar institución
            if ($year['institution_id'] != $_SESSION['institution_id']) {
                die('Acceso denegado');
            }

            if ($this->schoolYearModel->activate($yearId)) {
                header('Location: ?action=academic&sy_activated=1');
                exit;
            } else {
                header('Location: ?action=academic&error=activate_failed');
                exit;
            }
        }
    }

    public function deactivateSchoolYear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $yearId = (int)$_POST['year_id'];
            $year = $this->schoolYearModel->findById($yearId);

            if (!$year) {
                header('Location: ?action=academic&error=year_not_found');
                exit;
            }

            // Verificar institución
            if ($year['institution_id'] != $_SESSION['institution_id']) {
                die('Acceso denegado');
            }

            if ($this->schoolYearModel->deactivate($yearId)) {
                header('Location: ?action=academic&sy_deactivated=1');
                exit;
            } else {
                header('Location: ?action=academic&error=deactivate_failed');
                exit;
            }
        }
    }
}