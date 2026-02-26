<?php
// ARCHIVO COMPLETO - Reemplaza TODO el archivo
// controllers/TutorController.php

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Attendance.php';
require_once BASE_PATH . '/models/Course.php';

class TutorController {
    private $attendanceModel;
    private $courseModel;

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole('docente')) {
            die('Acceso denegado');
        }
        $db = new Database();
        $this->attendanceModel = new Attendance($db);
        $this->courseModel     = new Course($db);
    }

    public function courseAttendance() {
        $teacherId = $_SESSION['user_id'];
        $courseId  = $this->attendanceModel->getTutorCourseId($teacherId);

        if (!$courseId) {
            include BASE_PATH . '/views/tutor/no_tutor.php';
            return;
        }

        $course      = $this->courseModel->findById($courseId);
        $subjects    = $this->attendanceModel->getSubjectsByCourse($courseId);
        $students    = $this->attendanceModel->getStudentsByCourse($courseId);
        $topAbsences = $this->attendanceModel->getTutorTopAbsences($courseId, 5);

        // Leer filtros del GET
        $filters = [];
        if (!empty($_GET['subject_id']))  $filters['subject_id']  = (int)$_GET['subject_id'];
        if (!empty($_GET['student_id']))  $filters['student_id']  = (int)$_GET['student_id'];
        if (!empty($_GET['status']))      $filters['status']      = $_GET['status'];
        if (!empty($_GET['start_date']))  $filters['start_date']  = $_GET['start_date'];
        if (!empty($_GET['end_date']))    $filters['end_date']    = $_GET['end_date'];

        $attendances = $this->attendanceModel->getTutorCourseAttendance($courseId, $filters);
        $stats       = $this->attendanceModel->getTutorCourseStats($courseId, $filters);

        include BASE_PATH . '/views/tutor/course_attendance.php';
    }

    // Endpoint AJAX
    public function ajax() {
        header('Content-Type: application/json');

        $teacherId = $_SESSION['user_id'];
        $courseId  = $this->attendanceModel->getTutorCourseId($teacherId);

        if (!$courseId) {
            echo json_encode(['error' => 'No es tutor']);
            exit;
        }

        $filters = [];
        if (!empty($_GET['subject_id']))  $filters['subject_id']  = (int)$_GET['subject_id'];
        if (!empty($_GET['student_id']))  $filters['student_id']  = (int)$_GET['student_id'];
        if (!empty($_GET['status']))      $filters['status']      = $_GET['status'];
        if (!empty($_GET['start_date']))  $filters['start_date']  = $_GET['start_date'];
        if (!empty($_GET['end_date']))    $filters['end_date']    = $_GET['end_date'];

        $attendances = $this->attendanceModel->getTutorCourseAttendance($courseId, $filters);
        $stats       = $this->attendanceModel->getTutorCourseStats($courseId, $filters);

        echo json_encode([
            'stats'       => $stats,
            'attendances' => $attendances,
            'total'       => count($attendances),
        ]);
        exit;
    }

    // ── Dashboard del tutor ─────────────────────────────────────────────
    public function dashboard() {
        $teacherId = $_SESSION['user_id'];
        $courseId  = $this->attendanceModel->getTutorCourseId($teacherId);

        if (!$courseId) {
            include BASE_PATH . '/views/tutor/no_tutor.php';
            return;
        }

        $course      = $this->courseModel->findById($courseId);
        $students    = $this->attendanceModel->getStudentsByCourse($courseId);
        $topAbsences = $this->attendanceModel->getTutorTopAbsences($courseId, 10);
        $stats       = $this->attendanceModel->getTutorCourseStats($courseId);

        // Asistencia de hoy
        $db   = new Database();
        $pdo  = $db->connect();
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN status='presente'    THEN 1 ELSE 0 END) as presente,
                    SUM(CASE WHEN status='ausente'     THEN 1 ELSE 0 END) as ausente,
                    SUM(CASE WHEN status='tardanza'    THEN 1 ELSE 0 END) as tardanza,
                    SUM(CASE WHEN status='justificado' THEN 1 ELSE 0 END) as justificado
             FROM attendances
             WHERE course_id = :course_id AND date = CURDATE()"
        );
        $stmt->execute([':course_id' => $courseId]);
        $todayStats = $stmt->fetch();

        // Tendencia últimos 7 días
        $stmt2 = $pdo->prepare(
            "SELECT date,
                    COUNT(*) as total,
                    SUM(CASE WHEN status='presente' THEN 1 ELSE 0 END) as presente
             FROM attendances
             WHERE course_id = :course_id
               AND date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY date
             ORDER BY date ASC"
        );
        $stmt2->execute([':course_id' => $courseId]);
        $trend = $stmt2->fetchAll();

        include BASE_PATH . '/views/tutor/dashboard.php';
    }

    // ── Buscar estudiantes del curso ─────────────────────────────────────
    public function searchStudents() {
        $teacherId = $_SESSION['user_id'];
        $courseId  = $this->attendanceModel->getTutorCourseId($teacherId);

        if (!$courseId) {
            include BASE_PATH . '/views/tutor/no_tutor.php';
            return;
        }

        $course   = $this->courseModel->findById($courseId);
        $query    = trim($_GET['q'] ?? '');
        $students = [];

        $db   = new Database();
        $pdo  = $db->connect();

        $sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.dni, u.phone,
                       COUNT(a.id) as total_clases,
                       SUM(CASE WHEN a.status='presente'    THEN 1 ELSE 0 END) as presentes,
                       SUM(CASE WHEN a.status='ausente'     THEN 1 ELSE 0 END) as ausentes,
                       SUM(CASE WHEN a.status='tardanza'    THEN 1 ELSE 0 END) as tardanzas,
                       SUM(CASE WHEN a.status='justificado' THEN 1 ELSE 0 END) as justificados
                FROM users u
                INNER JOIN course_students cs ON u.id = cs.student_id
                LEFT JOIN attendances a ON a.student_id = u.id AND a.course_id = :course_id
                WHERE cs.course_id = :course_id2";

        $params = [':course_id' => $courseId, ':course_id2' => $courseId];

        if ($query !== '') {
            $sql .= " AND (u.first_name LIKE :q OR u.last_name LIKE :q2 OR u.dni LIKE :q3
                           OR CONCAT(u.last_name,' ',u.first_name) LIKE :q4)";
            $like = '%' . $query . '%';
            $params[':q']  = $like;
            $params[':q2'] = $like;
            $params[':q3'] = $like;
            $params[':q4'] = $like;
        }

        $sql .= " GROUP BY u.id, u.first_name, u.last_name, u.email, u.dni, u.phone
                  ORDER BY u.last_name, u.first_name";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();

        include BASE_PATH . '/views/tutor/search_students.php';
    }
}