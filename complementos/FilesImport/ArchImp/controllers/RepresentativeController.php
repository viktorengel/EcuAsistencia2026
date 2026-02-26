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
        $errorMsg = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $repId        = (int)$_POST['representative_id'];
            $studentId    = (int)$_POST['student_id'];
            $relationship = Security::sanitize($_POST['relationship']);
            $isPrimary    = isset($_POST['is_primary']) ? 1 : 0;

            $result = $this->representativeModel->assignStudent($repId, $studentId, $relationship, $isPrimary);

            if ($result === true) {
                header('Location: ?action=manage_representatives&success=1');
                exit;
            } else {
                // Devolver error sin redirigir para mostrar mensaje
                $errorMsg = $result['error'];
            }
        }

        include BASE_PATH . '/views/representatives/manage.php';
    }

    public function togglePrimary() {
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $repId     = (int)($_GET['rep_id'] ?? 0);
        $studentId = (int)($_GET['student_id'] ?? 0);

        if ($repId && $studentId) {
            $this->representativeModel->togglePrimary($repId, $studentId);
        }

        header('Location: ?action=manage_representatives&toggled=1');
        exit;
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

    public function removeRelation() {
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $repId     = (int)($_GET['rep_id'] ?? 0);
        $studentId = (int)($_GET['student_id'] ?? 0);

        if ($repId && $studentId) {
            if ($this->representativeModel->removeStudent($repId, $studentId)) {
                header('Location: ?action=manage_representatives&removed=1');
                exit;
            }
        }

        header('Location: ?action=manage_representatives&error=1');
        exit;
    }

    // ── Asignar representante desde vista académica ───────────────────────
    public function assignFromAcademic() {
        if (!Security::hasRole('autoridad')) { die('Acceso denegado'); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?action=academic'); exit; }

        $repId        = (int)$_POST['representative_id'];
        $studentId    = (int)$_POST['student_id'];
        $courseId     = (int)$_POST['course_id'];
        $relationship = Security::sanitize($_POST['relationship']);
        $isPrimary    = isset($_POST['is_primary']) ? 1 : 0;

        $result = $this->representativeModel->assignStudent($repId, $studentId, $relationship, $isPrimary);

        if ($result === true) {
            header('Location: ?action=academic&open_students=' . $courseId . '&rep_assigned=1');
        } else {
            header('Location: ?action=academic&open_students=' . $courseId . '&rep_error=' . urlencode($result['error']));
        }
        exit;
    }

    // ── Quitar representante desde vista académica ────────────────────────
    public function removeFromAcademic() {
        if (!Security::hasRole('autoridad')) { die('Acceso denegado'); }

        $repId     = (int)($_GET['rep_id']     ?? 0);
        $studentId = (int)($_GET['student_id'] ?? 0);
        $courseId  = (int)($_GET['course_id']  ?? 0);

        if ($repId && $studentId) {
            $this->representativeModel->removeStudent($repId, $studentId);
        }

        header('Location: ?action=academic&open_students=' . $courseId . '&rep_removed=1');
        exit;
    }

}