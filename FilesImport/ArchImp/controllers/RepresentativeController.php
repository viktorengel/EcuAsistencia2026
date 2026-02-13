<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Representative.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Attendance.php';

class RepresentativeController {
    private $representativeModel;
    private $userModel;
    private $attendanceModel;

    public function __construct() {
        Security::requireLogin();
        
        $db = new Database();
        $this->representativeModel = new Representative($db);
        $this->userModel = new User($db);
        $this->attendanceModel = new Attendance($db);
    }

    public function manageRepresentatives() {
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $representatives = $this->userModel->getByRole('representante');
        $students = $this->userModel->getByRole('estudiante');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $repId = (int)$_POST['representative_id'];
            $studentId = (int)$_POST['student_id'];
            $relationship = Security::sanitize($_POST['relationship']);
            $isPrimary = isset($_POST['is_primary']) ? 1 : 0;

            $this->representativeModel->assignStudent($repId, $studentId, $relationship, $isPrimary);
            header('Location: ?action=manage_representatives&success=1');
            exit;
        }

        include BASE_PATH . '/views/representatives/manage.php';
    }

    public function myChildren() {
        if (!Security::hasRole('representante')) {
            die('Acceso denegado');
        }

        $children = $this->representativeModel->getStudentsByRepresentative($_SESSION['user_id']);
        
        include BASE_PATH . '/views/representatives/my_children.php';
    }

    public function childAttendance() {
        if (!Security::hasRole('representante')) {
            die('Acceso denegado');
        }

        $studentId = (int)($_GET['student_id'] ?? 0);
        
        // Verificar que el estudiante sea hijo del representante
        $children = $this->representativeModel->getStudentsByRepresentative($_SESSION['user_id']);
        $authorized = false;
        $student = null;

        foreach ($children as $child) {
            if ($child['id'] == $studentId) {
                $authorized = true;
                $student = $child;
                break;
            }
        }

        if (!$authorized) {
            die('No autorizado para ver este estudiante');
        }

        $attendances = $this->attendanceModel->getByStudent($studentId);
        
        include BASE_PATH . '/views/representatives/child_attendance.php';
    }
}