<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Attendance.php';

class SearchController {
    private $userModel;
    private $courseModel;
    private $attendanceModel;

    public function __construct() {
        Security::requireLogin();
        
        $db = new Database();
        $this->userModel = new User($db);
        $this->courseModel = new Course($db);
        $this->attendanceModel = new Attendance($db);
    }

    public function searchStudents() {
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            echo json_encode([]);
            exit;
        }

        $sql = "SELECT u.id, u.first_name, u.last_name, u.dni, u.email,
                c.name as course_name
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                LEFT JOIN course_students cs ON u.id = cs.student_id
                LEFT JOIN courses c ON cs.course_id = c.id
                WHERE r.name = 'estudiante'
                AND u.institution_id = :institution_id
                AND (u.first_name LIKE :query 
                    OR u.last_name LIKE :query 
                    OR u.dni LIKE :query 
                    OR u.email LIKE :query)
                LIMIT 10";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([
            ':institution_id' => $_SESSION['institution_id'],
            ':query' => '%' . $query . '%'
        ]);
        
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll());
        exit;
    }

    public function advancedAttendanceReport() {
        if (!Security::hasRole(['autoridad', 'inspector'])) {
            die('Acceso denegado');
        }

        $filters = [
            'course_id' => $_POST['course_id'] ?? null,
            'student_id' => $_POST['student_id'] ?? null,
            'subject_id' => $_POST['subject_id'] ?? null,
            'status' => $_POST['status'] ?? null,
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null
        ];

        $results = $this->attendanceModel->getFiltered($filters);

        include BASE_PATH . '/views/reports/advanced.php';
    }
}