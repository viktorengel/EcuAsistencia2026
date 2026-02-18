<?php
// ARCHIVO COMPLETO - Reemplaza TODO el archivo
// controllers/JustificationController.php

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Justification.php';
require_once BASE_PATH . '/models/Attendance.php';
require_once BASE_PATH . '/models/Notification.php';

class JustificationController {
    private $justificationModel;
    private $attendanceModel;
    private $notificationModel;

    public function __construct() {
        Security::requireLogin();

        $db = new Database();
        $this->justificationModel  = new Justification($db);
        $this->attendanceModel     = new Attendance($db);
        $this->notificationModel   = new Notification($db);
    }

    public function submit() {
        if (!Security::hasRole(['estudiante', 'representante'])) {
            die('Acceso denegado');
        }

        $attendanceId = (int)$_GET['attendance_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->justificationModel->existsByAttendance($attendanceId)) {
                header('Location: ?action=my_attendance&error=already_justified');
                exit;
            }

            $documentPath = null;

            if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
                $uploadDir = BASE_PATH . '/uploads/justifications/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $extension = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
                $filename  = uniqid() . '.' . $extension;
                if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadDir . $filename)) {
                    $documentPath = 'uploads/justifications/' . $filename;
                }
            }

            $studentId = Security::hasRole('estudiante')
                ? $_SESSION['user_id']
                : (int)$_POST['student_id'];

            $data = [
                ':attendance_id' => $attendanceId,
                ':student_id'    => $studentId,
                ':submitted_by'  => $_SESSION['user_id'],
                ':reason'        => Security::sanitize($_POST['reason']),
                ':document_path' => $documentPath
            ];

            $this->justificationModel->create($data);

            // Notificar a autoridades/inspectores — enlace directo a pendientes
            $this->_notifyReviewers(
                '📝 Nueva justificación pendiente',
                'Un estudiante envió una justificación que requiere revisión.',
                'justificacion',
                '?action=pending_justifications'
            );

            header('Location: ?action=my_justifications&success=1');
            exit;
        }

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
        $studentId      = $_SESSION['user_id'];
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
            $action          = $_POST['review_action'];
            $notes           = Security::sanitize($_POST['notes'] ?? '');

            // Obtener datos ANTES de actualizar para poder notificar
            $justification = $this->justificationModel->getById($justificationId);

            if ($action === 'approve') {
                $this->justificationModel->approve($justificationId, $_SESSION['user_id'], $notes);

                if ($justification) {
                    // Notificar al estudiante — enlace a sus justificaciones
                    $this->notificationModel->create(
                        $justification['student_id'],
                        '✅ Justificación aprobada',
                        'Tu justificación fue aprobada.' . ($notes ? " Nota: $notes" : ''),
                        'success',
                        '?action=my_justifications'
                    );
                    // Notificar al representante si fue distinto del estudiante
                    if ($justification['submitted_by'] != $justification['student_id']) {
                        $this->notificationModel->create(
                            $justification['submitted_by'],
                            '✅ Justificación aprobada',
                            'La justificación que enviaste fue aprobada.' . ($notes ? " Nota: $notes" : ''),
                            'success',
                            '?action=my_justifications'
                        );
                    }
                }
            } else {
                $this->justificationModel->reject($justificationId, $_SESSION['user_id'], $notes);

                if ($justification) {
                    // Notificar al estudiante — puede enviar una nueva justificación
                    $this->notificationModel->create(
                        $justification['student_id'],
                        '❌ Justificación rechazada',
                        'Tu justificación fue rechazada.' . ($notes ? " Motivo: $notes" : ''),
                        'danger',
                        '?action=my_justifications'
                    );
                    if ($justification['submitted_by'] != $justification['student_id']) {
                        $this->notificationModel->create(
                            $justification['submitted_by'],
                            '❌ Justificación rechazada',
                            'La justificación que enviaste fue rechazada.' . ($notes ? " Motivo: $notes" : ''),
                            'danger',
                            '?action=my_justifications'
                        );
                    }
                }
            }

            header('Location: ?action=pending_justifications&success=1');
            exit;
        }
    }

    public function reviewed() {
        if (!Security::hasRole(['autoridad', 'inspector'])) {
            die('Acceso denegado');
        }
        $filter         = $_GET['filter'] ?? 'all';
        $justifications = $this->justificationModel->getReviewed();

        if ($filter !== 'all') {
            $justifications = array_values(array_filter($justifications, function($j) use ($filter) {
                return $j['status'] === $filter;
            }));
        }

        include BASE_PATH . '/views/justifications/reviewed.php';
    }

    // Privado: notificar a todos los revisores
    private function _notifyReviewers($title, $message, $type = 'info', $link = null) {
        $db  = new Database();
        $pdo = $db->connect();

        $sql  = "SELECT DISTINCT u.id FROM users u
                 INNER JOIN user_roles ur ON u.id = ur.user_id
                 INNER JOIN roles r ON ur.role_id = r.id
                 WHERE r.name IN ('autoridad','inspector') AND u.id != :sender";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':sender' => $_SESSION['user_id']]);
        $reviewers = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($reviewers as $reviewerId) {
            $this->notificationModel->create($reviewerId, $title, $message, $type, $link);
        }
    }
}