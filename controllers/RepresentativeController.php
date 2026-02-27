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

        // Solicitudes pendientes y rechazadas del representante
        $db   = new Database();
        $stmt = $db->connect()->prepare(
            "SELECT lr.*, CONCAT(u.last_name,' ',u.first_name) as student_name
              FROM link_requests lr
              INNER JOIN users u ON lr.student_id = u.id
              WHERE lr.representative_id = :rid AND lr.status IN ('pendiente','rechazado')
              ORDER BY lr.created_at DESC"
        );
        $stmt->execute([':rid' => $_SESSION['user_id']]);
        $myRequests = $stmt->fetchAll();

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


    // ── Buscar estudiantes (JSON para autocomplete) ───────────────────────
    public function searchStudentsJson() {
        if (!Security::hasRole(['representante','autoridad','inspector'])) { http_response_code(403); echo '[]'; exit; }
        $q = Security::sanitize($_GET['q'] ?? '');
        if (strlen($q) < 2) { echo '[]'; exit; }

        $db   = new Database();
        $stmt = $db->connect()->prepare(
            "SELECT u.id, u.first_name, u.last_name, u.dni,
                     c.name as course_name
              FROM users u
              INNER JOIN user_roles ur ON u.id = ur.user_id
              INNER JOIN roles r ON ur.role_id = r.id AND r.name = 'estudiante'
              LEFT JOIN course_students cs ON u.id = cs.student_id
              LEFT JOIN courses c ON cs.course_id = c.id
              WHERE u.is_active = 1
                AND (u.last_name LIKE :q1 OR u.first_name LIKE :q2 OR u.dni LIKE :q3)
              ORDER BY u.last_name, u.first_name
              LIMIT 10"
        );
        $stmt->execute([':q1' => '%'.$q.'%', ':q2' => '%'.$q.'%', ':q3' => '%'.$q.'%']);
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // ── Solicitar vinculación ─────────────────────────────────────────────
    public function requestLink() {
        if (!Security::hasRole('representante')) die('Acceso denegado');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?action=my_children'); exit; }
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) die('Token inválido');

        $studentId    = (int)$_POST['student_id'];
        $relationship = Security::sanitize($_POST['relationship']);
        $message      = Security::sanitize($_POST['message'] ?? '');
        $repId        = $_SESSION['user_id'];

        if (!$studentId || !$relationship) {
            header('Location: ?action=my_children');
            exit;
        }

        $db   = new Database();
        $pdo  = $db->connect();

        // Verificar que no ya sea representante de ese estudiante
        $check = $pdo->prepare("SELECT COUNT(*) FROM representatives WHERE representative_id = :rid AND student_id = :sid");
        $check->execute([':rid' => $repId, ':sid' => $studentId]);
        if ($check->fetchColumn() > 0) {
            header('Location: ?action=my_children&request_exists=1');
            exit;
        }

        // Verificar que no haya solicitud pendiente ya
        $check2 = $pdo->prepare("SELECT COUNT(*) FROM link_requests WHERE representative_id = :rid AND student_id = :sid AND status = 'pendiente'");
        $check2->execute([':rid' => $repId, ':sid' => $studentId]);
        if ($check2->fetchColumn() > 0) {
            header('Location: ?action=my_children&request_exists=1');
            exit;
        }

        // Insertar solicitud (reemplaza rechazada previa si existe)
        $stmt = $pdo->prepare(
            "INSERT INTO link_requests (representative_id, student_id, relationship, message, status)
              VALUES (:rid, :sid, :rel, :msg, 'pendiente')
              ON DUPLICATE KEY UPDATE relationship = :rel2, message = :msg2, status = 'pendiente', reviewed_by = NULL, reviewed_at = NULL, review_notes = NULL"
        );
        $stmt->execute([':rid' => $repId, ':sid' => $studentId, ':rel' => $relationship, ':msg' => $message, ':rel2' => $relationship, ':msg2' => $message]);

        // Nombre del representante y del estudiante para la notificación
        $repRow = $pdo->query("SELECT CONCAT(last_name,' ',first_name) as name FROM users WHERE id = $repId")->fetch();
        $stuRow = $pdo->query("SELECT CONCAT(last_name,' ',first_name) as name FROM users WHERE id = $studentId")->fetch();
        $repName = $repRow ? $repRow['name'] : 'Un representante';
        $stuName = $stuRow ? $stuRow['name'] : 'un estudiante';

        // Notificar a autoridad e inspector
        require_once BASE_PATH . '/models/Notification.php';
        $notifDb    = new Database();
        $notifModel = new Notification($notifDb);

        $reviewers = $pdo->query(
            "SELECT DISTINCT u.id FROM users u
             INNER JOIN user_roles ur ON u.id = ur.user_id
             INNER JOIN roles r ON ur.role_id = r.id
             WHERE r.name IN ('autoridad','inspector') AND u.is_active = 1"
        )->fetchAll(PDO::FETCH_COLUMN);

        foreach ($reviewers as $reviewerId) {
            $notifModel->create(
                $reviewerId,
                '🔗 Nueva solicitud de vinculación',
                "$repName solicita ser representante de $stuName.",
                'info',
                '?action=link_requests'
            );
        }

        header('Location: ?action=my_children&request_sent=1');
        exit;
    }

    // ── Panel de solicitudes (autoridad/inspector) ────────────────────────
    public function linkRequests() {
        if (!Security::hasRole(['autoridad', 'inspector'])) die('Acceso denegado');

        $filter = $_GET['filter'] ?? 'pendiente';
        $db     = new Database();
        $pdo    = $db->connect();

        $stmt = $pdo->prepare(
            "SELECT lr.*,
                     CONCAT(rep.last_name,' ',rep.first_name) as rep_name, rep.dni as rep_dni,
                     CONCAT(stu.last_name,' ',stu.first_name) as student_name, stu.dni as student_dni,
                     CONCAT(rev.last_name,' ',rev.first_name) as reviewer_name
              FROM link_requests lr
              INNER JOIN users rep ON lr.representative_id = rep.id
              INNER JOIN users stu ON lr.student_id        = stu.id
              LEFT  JOIN users rev ON lr.reviewed_by       = rev.id
              WHERE lr.status = :status
              ORDER BY lr.created_at DESC"
        );
        $stmt->execute([':status' => $filter]);
        $requests = $stmt->fetchAll();

        $countStmt = $pdo->query("SELECT COUNT(*) FROM link_requests WHERE status = 'pendiente'");
        $pendingCount = $countStmt->fetchColumn();

        include BASE_PATH . '/views/representatives/link_requests.php';
    }

    // ── Revisar solicitud (aprobar / rechazar) ────────────────────────────
    public function reviewLinkRequest() {
        if (!Security::hasRole(['autoridad', 'inspector'])) die('Acceso denegado');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?action=link_requests'); exit; }
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) die('Token inválido');

        $requestId   = (int)$_POST['request_id'];
        $decision    = $_POST['decision'] === 'aprobado' ? 'aprobado' : 'rechazado';
        $reviewNotes = Security::sanitize($_POST['review_notes'] ?? '');
        $reviewerId  = $_SESSION['user_id'];

        $db  = new Database();
        $pdo = $db->connect();

        // Obtener datos de la solicitud
        $stmt = $pdo->prepare("SELECT * FROM link_requests WHERE id = :id");
        $stmt->execute([':id' => $requestId]);
        $req = $stmt->fetch();

        if (!$req) { header('Location: ?action=link_requests'); exit; }

        // Actualizar estado
        $upd = $pdo->prepare(
            "UPDATE link_requests SET status = :status, reviewed_by = :rev, reviewed_at = NOW(), review_notes = :notes WHERE id = :id"
        );
        $upd->execute([':status' => $decision, ':rev' => $reviewerId, ':notes' => $reviewNotes, ':id' => $requestId]);

        // Si aprobado, crear la vinculación
        if ($decision === 'aprobado') {
            $ins = $pdo->prepare(
                "INSERT IGNORE INTO representatives (representative_id, student_id, relationship, is_primary)
                  VALUES (:rid, :sid, :rel, 0)"
            );
            $ins->execute([
                ':rid' => $req['representative_id'],
                ':sid' => $req['student_id'],
                ':rel' => $req['relationship']
            ]);
            header('Location: ?action=link_requests&filter=pendiente&approved=1');
        } else {
            header('Location: ?action=link_requests&filter=pendiente&rejected=1');
        }
        exit;
    }


    public function unlinkStudent() {
        if (!Security::hasRole('representante')) die('Acceso denegado');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?action=my_children'); exit; }
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) die('Token inválido');

        $studentId = (int)($_POST['student_id'] ?? 0);
        $repId     = $_SESSION['user_id'];

        if ($studentId) {
            $db  = new Database();
            $pdo = $db->connect();
            $stmt = $pdo->prepare("DELETE FROM representatives WHERE representative_id = :rid AND student_id = :sid");
            $stmt->execute([':rid' => $repId, ':sid' => $studentId]);
        }

        header('Location: ?action=my_children&unlinked=1');
        exit;
    }

}