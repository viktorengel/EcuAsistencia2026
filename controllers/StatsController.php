<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Attendance.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/User.php';

class StatsController {
    private $attendanceModel;
    private $courseModel;
    private $userModel;

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole(['autoridad', 'inspector', 'docente'])) {
            die('Acceso denegado');
        }

        $db = new Database();
        $this->attendanceModel = new Attendance($db);
        $this->courseModel = new Course($db);
        $this->userModel = new User($db);
    }

    public function index() {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $stats = [
            'overall' => $this->getOverallStats($startDate, $endDate),
            'by_course' => $this->getStatsByCourse($startDate, $endDate),
            'by_student' => $this->getTopAbsentStudents($startDate, $endDate),
            'by_day' => $this->getStatsByDay($startDate, $endDate)
        ];

        include BASE_PATH . '/views/stats/index.php';
    }

    private function getOverallStats($startDate, $endDate) {
        $sql = "SELECT 
                COUNT(*) as total_records,
                COUNT(DISTINCT student_id) as total_students,
                SUM(CASE WHEN status = 'presente' THEN 1 ELSE 0 END) as presente,
                SUM(CASE WHEN status = 'ausente' THEN 1 ELSE 0 END) as ausente,
                SUM(CASE WHEN status = 'tardanza' THEN 1 ELSE 0 END) as tardanza,
                SUM(CASE WHEN status = 'justificado' THEN 1 ELSE 0 END) as justificado
                FROM attendances 
                WHERE date BETWEEN :start_date AND :end_date";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
        return $stmt->fetch();
    }

    private function getStatsByCourse($startDate, $endDate) {
        $sql = "SELECT 
                c.name as course_name,
                COUNT(*) as total,
                SUM(CASE WHEN a.status = 'presente' THEN 1 ELSE 0 END) as presente,
                SUM(CASE WHEN a.status = 'ausente' THEN 1 ELSE 0 END) as ausente,
                ROUND(SUM(CASE WHEN a.status = 'presente' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as percentage
                FROM attendances a
                INNER JOIN courses c ON a.course_id = c.id
                WHERE a.date BETWEEN :start_date AND :end_date
                GROUP BY c.id, c.name
                ORDER BY percentage DESC";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
        return $stmt->fetchAll();
    }

    private function getTopAbsentStudents($startDate, $endDate, $limit = 10) {
        $sql = "SELECT 
                CONCAT(u.last_name, ' ', u.first_name) as student_name,
                c.name as course_name,
                COUNT(*) as total_absences
                FROM attendances a
                INNER JOIN users u ON a.student_id = u.id
                INNER JOIN courses c ON a.course_id = c.id
                WHERE a.status = 'ausente' 
                AND a.date BETWEEN :start_date AND :end_date
                GROUP BY a.student_id, u.last_name, u.first_name, c.name
                ORDER BY total_absences DESC
                LIMIT :limit";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->bindValue(':start_date', $startDate);
        $stmt->bindValue(':end_date', $endDate);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getStatsByDay($startDate, $endDate) {
        $sql = "SELECT 
                date,
                COUNT(*) as total,
                SUM(CASE WHEN status = 'presente' THEN 1 ELSE 0 END) as presente,
                ROUND(SUM(CASE WHEN status = 'presente' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as percentage
                FROM attendances 
                WHERE date BETWEEN :start_date AND :end_date
                GROUP BY date
                ORDER BY date ASC";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
        return $stmt->fetchAll();
    }
}