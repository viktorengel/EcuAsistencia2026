<?php
// ARCHIVO COMPLETO - Reemplaza TODO el archivo
// controllers/JustificationController.php

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Justification.php';
require_once BASE_PATH . '/models/Attendance.php';
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

        $studentId = Security::hasRole('estudiante')
            ? $_SESSION['user_id']
            : (isset($_GET['student_id']) ? (int)$_GET['student_id'] : null);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $studentId = Security::hasRole('estudiante')
                ? $_SESSION['user_id']
                : (int)$_POST['student_id'];

            // Ausencias seleccionadas
            $attendanceIds = $_POST['attendance_ids'] ?? [];
            if (empty($attendanceIds)) {
                header('Location: ?action=submit_justification&error=no_absences');
                exit;
            }

            // Obtener fechas de las ausencias seleccionadas para calcular días laborables
            $db  = new Database();
            $pdo = $db->connect();
            $in  = implode(',', array_map('intval', $attendanceIds));
            $rows = $pdo->query(
                "SELECT date FROM attendances WHERE id IN ($in) AND student_id = $studentId ORDER BY date"
            )->fetchAll();

            if (empty($rows)) {
                header('Location: ?action=submit_justification&error=no_absences');
                exit;
            }

            $dates       = array_column($rows, 'date');
            $dateFrom    = min($dates);
            $dateTo      = max($dates);
            $workingDays = count($attendanceIds); // cada ausencia = 1 día (pueden ser no consecutivos)
            $canApprove  = Justification::resolveApprover($workingDays);

            // Subir documento
            $documentPath = null;
            if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
                $uploadDir = BASE_PATH . '/uploads/justifications/';
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext     = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf','jpg','jpeg','png'];
                if (in_array($ext, $allowed) && $_FILES['document']['size'] <= 5 * 1024 * 1024) {
                    $filename = uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadDir . $filename)) {
                        $documentPath = 'uploads/justifications/' . $filename;
                    }
                }
            }

            // Motivo
            $reasonType  = Security::sanitize($_POST['reason_type'] ?? '');
            $reasonText  = Security::sanitize($_POST['reason']      ?? '');
            $reasonFinal = ($reasonType === 'Otro')
                ? $reasonText
                : ($reasonType . ($reasonText ? ': ' . $reasonText : ''));

            $data = [
                ':student_id'    => $studentId,
                ':submitted_by'  => $_SESSION['user_id'],
                ':date_from'     => $dateFrom,
                ':date_to'       => $dateTo,
                ':working_days'  => $workingDays,
                ':reason_type'   => $reasonType,
                ':reason'        => $reasonFinal,
                ':document_path' => $documentPath,
                ':can_approve'   => $canApprove,
            ];

            $this->justificationModel->createForAttendances($attendanceIds, $data);

            // Notificar
            if ($canApprove === 'tutor') {
                $stmt = $pdo->prepare(
                    "SELECT ta.teacher_id FROM teacher_assignments ta
                     INNER JOIN course_students cs ON ta.course_id = cs.course_id
                     WHERE cs.student_id = :sid AND ta.is_tutor = 1 LIMIT 1"
                );
                $stmt->execute([':sid' => $studentId]);
                $tutorId = $stmt->fetchColumn();
                if ($tutorId) {
                    $this->notificationModel->create(
                        $tutorId,
                        '📝 Justificación pendiente (tutor)',
                        "Un estudiante de tu curso necesita justificación por $workingDays día(s).",
                        'info',
                        '?action=tutor_pending_justifications'
                    );
                }
            } else {
                $this->_notifyReviewers(
                    '📝 Nueva justificación pendiente',
                    "Justificación de $workingDays día(s) requiere revisión.",
                    'info',
                    '?action=pending_justifications'
                );
            }

            header('Location: ?action=my_justifications&success=1');
            exit;
        }

        // GET: cargar ausencias no justificadas
        $absences = $this->attendanceModel->getUnjustifiedAbsences($studentId ?? $_SESSION['user_id']);

        // Si representante, cargar sus hijos
        $myChildren = [];
        if (Security::hasRole('representante')) {
            $db   = new Database();
            $stmt = $db->connect()->prepare(
                "SELECT u.id, CONCAT(u.last_name,' ',u.first_name) as full_name
                 FROM users u INNER JOIN representatives r ON u.id = r.student_id
                 WHERE r.representative_id = :rid ORDER BY u.last_name"
            );
            $stmt->execute([':rid' => $_SESSION['user_id']]);
            $myChildren = $stmt->fetchAll();
        }

        include BASE_PATH . '/views/justifications/submit.php';
    }


    public function pendingForTutor() {
        if (!Security::hasRole('docente')) die('Acceso denegado');

        $db           = new Database();
        $attModel     = new Attendance($db);
        $courseId     = $attModel->getTutorCourseId($_SESSION['user_id']);

        if (!$courseId) {
            header('Location: ?action=dashboard&error=not_tutor'); exit;
        }

        $justifications = $this->justificationModel->getPendingForTutor($courseId);
        include BASE_PATH . '/views/justifications/pending.php';
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
                $this->justificationModel->approveRange($justificationId, $_SESSION['user_id'], $notes);

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