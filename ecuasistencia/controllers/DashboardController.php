<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Attendance.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Course.php';

class DashboardController {
    private $attendanceModel;
    private $userModel;
    private $courseModel;

    public function __construct() {
        Security::requireLogin();
        
        $db = new Database();
        $this->attendanceModel = new Attendance($db);
        $this->userModel = new User($db);
        $this->courseModel = new Course($db);
    }

    public function index() {
        $stats = $this->getStats();
        include BASE_PATH . '/views/dashboard/index.php';
    }

    private function getStats() {
        $stats = [];
        
        if (Security::hasRole('autoridad')) {
            $stats['total_students'] = $this->getTotalByRole('estudiante');
            $stats['total_teachers'] = $this->getTotalByRole('docente');
            $stats['total_courses'] = $this->getTotalCourses();
            $stats['today_attendance'] = $this->getTodayAttendanceStats();
        }

        if (Security::hasRole('docente')) {
            $stats['my_courses']  = $this->getTeacherCourses($_SESSION['user_id']);
            $stats['my_students'] = $this->getTeacherStudentsCount($_SESSION['user_id']);
            $stats['tutor_course'] = $this->getTutorCourse($_SESSION['user_id']);
        }

        if (Security::hasRole('estudiante')) {
            $stats['my_attendance'] = $this->getStudentAttendanceStats($_SESSION['user_id']);
        }

        if (Security::hasRole('representante')) {
            $stats['my_children'] = $this->getRepresentativeChildrenCount($_SESSION['user_id']);
        }

        return $stats;
    }

    private function getTotalByRole($role) {
        $sql = "SELECT COUNT(DISTINCT u.id) as total
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.name = :role AND u.institution_id = :institution_id";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([
            ':role' => $role,
            ':institution_id' => $_SESSION['institution_id']
        ]);
        
        return $stmt->fetch()['total'];
    }

    private function getTotalCourses() {
        $sql = "SELECT COUNT(*) as total FROM courses WHERE institution_id = :institution_id";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':institution_id' => $_SESSION['institution_id']]);
        
        return $stmt->fetch()['total'];
    }

    private function getTodayAttendanceStats() {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'presente' THEN 1 ELSE 0 END) as presente,
                SUM(CASE WHEN status = 'ausente' THEN 1 ELSE 0 END) as ausente
                FROM attendances 
                WHERE date = CURDATE()";
        
        $db = new Database();
        $stmt = $db->connect()->query($sql);
        return $stmt->fetch();
    }

    private function getTeacherCourses($teacherId) {
        $sql = "SELECT COUNT(DISTINCT course_id) as total 
                FROM teacher_assignments 
                WHERE teacher_id = :teacher_id";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':teacher_id' => $teacherId]);
        
        return $stmt->fetch()['total'];
    }

    private function getTeacherStudentsCount($teacherId) {
        $sql = "SELECT COUNT(DISTINCT cs.student_id) as total
                FROM teacher_assignments ta
                INNER JOIN course_students cs ON ta.course_id = cs.course_id
                WHERE ta.teacher_id = :teacher_id";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':teacher_id' => $teacherId]);
        
        return $stmt->fetch()['total'];
    }

    private function getStudentAttendanceStats($studentId) {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'presente' THEN 1 ELSE 0 END) as presente,
                SUM(CASE WHEN status = 'ausente' THEN 1 ELSE 0 END) as ausente,
                SUM(CASE WHEN status = 'tardanza' THEN 1 ELSE 0 END) as tardanza
                FROM attendances 
                WHERE student_id = :student_id
                AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        
        return $stmt->fetch();
    }

    private function getRepresentativeChildrenCount($representativeId) {
        $sql = "SELECT COUNT(*) as total 
                FROM representatives 
                WHERE representative_id = :representative_id";
        
        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':representative_id' => $representativeId]);
        
        return $stmt->fetch()['total'];
    }

    private function getTutorCourse($teacherId) {
        $sql = "SELECT c.name as course_name
                FROM teacher_assignments ta
                INNER JOIN courses c ON ta.course_id = c.id
                INNER JOIN school_years sy ON ta.school_year_id = sy.id
                WHERE ta.teacher_id = :teacher_id
                AND ta.is_tutor = 1
                AND sy.is_active = 1
                LIMIT 1";

        $db = new Database();
        $stmt = $db->connect()->prepare($sql);
        $stmt->execute([':teacher_id' => $teacherId]);
        $row = $stmt->fetch();
        return $row ? $row['course_name'] : null;
    }
}