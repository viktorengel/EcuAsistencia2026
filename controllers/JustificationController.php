<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Justification.php';
require_once BASE_PATH . '/models/Attendance.php';

class JustificationController {
    private $justificationModel;
    private $attendanceModel;

    public function __construct() {
        Security::requireLogin();
        
        $db = new Database();
        $this->justificationModel = new Justification($db);
        $this->attendanceModel = new Attendance($db);
    }

    public function submit() {
        if (!Security::hasRole(['estudiante', 'representante'])) {
            die('Acceso denegado');
        }

        $attendanceId = (int)$_GET['attendance_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar si ya existe una justificación para esta asistencia
            if ($this->justificationModel->existsByAttendance($attendanceId)) {
                header('Location: ?action=my_attendance&error=already_justified');
                exit;
            }

            $documentPath = null;

            // Subir documento si existe
            if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
                $uploadDir = BASE_PATH . '/uploads/justifications/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $uploadPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadPath)) {
                    $documentPath = 'uploads/justifications/' . $filename;
                }
            }

            $data = [
                ':attendance_id' => $attendanceId,
                ':student_id' => Security::hasRole('estudiante') ? $_SESSION['user_id'] : (int)$_POST['student_id'],
                ':submitted_by' => $_SESSION['user_id'],
                ':reason' => Security::sanitize($_POST['reason']),
                ':document_path' => $documentPath
            ];

            $this->justificationModel->create($data);
            header('Location: ?action=my_justifications&success=1');
            exit;
        }

        // Verificar si ya existe justificación antes de mostrar el formulario
        if ($this->justificationModel->existsByAttendance($attendanceId)) {
            header('Location: ?action=my_attendance&error=already_justified');
            exit;
        }

        include BASE_PATH . '/views/justifications/submit.php';
    }

    public function myJustifications() {
        if (!Security::hasRole(['estudiante', 'representante'])) {
            die('Acceso denegado');
        }

        $studentId = $_SESSION['user_id'];
        $justifications = $this->justificationModel->getByStudent($studentId);

        include BASE_PATH . '/views/justifications/my_list.php';
    }

    public function pending() {
        if (!Security::hasRole(['autoridad', 'inspector'])) {
            die('Acceso denegado');
        }

        $justifications = $this->justificationModel->getPending();

        include BASE_PATH . '/views/justifications/pending.php';
    }

    public function review() {
        if (!Security::hasRole(['autoridad', 'inspector'])) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $justificationId = (int)$_POST['justification_id'];
            $action = $_POST['review_action'];
            $notes = Security::sanitize($_POST['notes'] ?? '');

            if ($action === 'approve') {
                $this->justificationModel->approve($justificationId, $_SESSION['user_id'], $notes);
            } else {
                $this->justificationModel->reject($justificationId, $_SESSION['user_id'], $notes);
            }

            header('Location: ?action=pending_justifications&success=1');
            exit;
        }
    }
}