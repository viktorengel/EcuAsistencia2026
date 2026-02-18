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
}