<?php
// ARCHIVO COMPLETO - Reemplaza TODO el archivo
// models/Attendance.php

class Attendance {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function create($data) {
        $checkSql = "SELECT id FROM attendances 
                     WHERE student_id = :student_id 
                     AND course_id = :course_id 
                     AND subject_id = :subject_id 
                     AND date = :date 
                     AND hour_period = :hour_period";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([
            ':student_id'  => $data[':student_id'],
            ':course_id'   => $data[':course_id'],
            ':subject_id'  => $data[':subject_id'],
            ':date'        => $data[':date'],
            ':hour_period' => $data[':hour_period']
        ]);
        $existing = $checkStmt->fetch();
        
        if ($existing) {
            $updateSql = "UPDATE attendances 
                         SET status = :status, observation = :observation, updated_at = NOW()
                         WHERE id = :id";
            $updateStmt = $this->db->prepare($updateSql);
            return $updateStmt->execute([
                ':status'      => $data[':status'],
                ':observation' => $data[':observation'],
                ':id'          => $existing['id']
            ]);
        }
        
        $sql = "INSERT INTO attendances 
                (student_id, course_id, subject_id, teacher_id, school_year_id, shift_id, date, hour_period, status, observation) 
                VALUES (:student_id, :course_id, :subject_id, :teacher_id, :school_year_id, :shift_id, :date, :hour_period, :status, :observation)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function canEdit($attendanceId) {
        $sql = "SELECT created_at FROM attendances WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $attendanceId]);
        $record = $stmt->fetch();
        if (!$record) return false;
        return (time() - strtotime($record['created_at'])) <= (EDIT_ATTENDANCE_HOURS * 3600);
    }

    public function update($id, $data) {
        $sql = "UPDATE attendances 
                SET status = :status, observation = :observation, updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':status' => $data['status'], ':observation' => $data['observation']]);
    }

    public function getByCourse($courseId, $date) {
        $sql = "SELECT a.*, u.first_name, u.last_name, s.name as subject_name,
                CONCAT(t.first_name, ' ', t.last_name) as teacher_name
                FROM attendances a
                INNER JOIN users u ON a.student_id = u.id
                INNER JOIN subjects s ON a.subject_id = s.id
                INNER JOIN users t ON a.teacher_id = t.id
                WHERE a.course_id = :course_id AND a.date = :date
                ORDER BY u.last_name, u.first_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId, ':date' => $date]);
        return $stmt->fetchAll();
    }

    public function getByStudent($studentId) {
        $sql = "SELECT a.*, s.name as subject_name, c.name as course_name, sh.name as shift_name
                FROM attendances a
                INNER JOIN subjects s ON a.subject_id = s.id
                INNER JOIN courses c ON a.course_id = c.id
                INNER JOIN shifts sh ON a.shift_id = sh.id
                WHERE a.student_id = :student_id
                ORDER BY a.date DESC, a.hour_period
                LIMIT 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function getReportData($courseId, $startDate, $endDate) {
        $sql = "SELECT a.*, 
                CONCAT(u.last_name, ' ', u.first_name) as student_name,
                s.name as subject_name
                FROM attendances a
                INNER JOIN users u ON a.student_id = u.id
                INNER JOIN subjects s ON a.subject_id = s.id
                WHERE a.course_id = :course_id 
                AND a.date BETWEEN :start_date AND :end_date
                ORDER BY a.date, u.last_name, u.first_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId, ':start_date' => $startDate, ':end_date' => $endDate]);
        return $stmt->fetchAll();
    }

    public function getStatsByCourse($courseId, $startDate, $endDate) {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'presente'    THEN 1 ELSE 0 END) as presente,
                SUM(CASE WHEN status = 'ausente'     THEN 1 ELSE 0 END) as ausente,
                SUM(CASE WHEN status = 'tardanza'    THEN 1 ELSE 0 END) as tardanza,
                SUM(CASE WHEN status = 'justificado' THEN 1 ELSE 0 END) as justificado
                FROM attendances
                WHERE course_id = :course_id AND date BETWEEN :start_date AND :end_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId, ':start_date' => $startDate, ':end_date' => $endDate]);
        return $stmt->fetch();
    }

    public function getFiltered($filters) {
        $sql = "SELECT a.*, 
                CONCAT(u.last_name, ' ', u.first_name) as student_name,
                c.name as course_name, s.name as subject_name,
                CONCAT(t.last_name, ' ', t.first_name) as teacher_name
                FROM attendances a
                INNER JOIN users u ON a.student_id = u.id
                INNER JOIN courses c ON a.course_id = c.id
                INNER JOIN subjects s ON a.subject_id = s.id
                INNER JOIN users t ON a.teacher_id = t.id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['course_id']))   { $sql .= " AND a.course_id = :course_id";     $params[':course_id']   = $filters['course_id']; }
        if (!empty($filters['student_id']))  { $sql .= " AND a.student_id = :student_id";   $params[':student_id']  = $filters['student_id']; }
        if (!empty($filters['subject_id']))  { $sql .= " AND a.subject_id = :subject_id";   $params[':subject_id']  = $filters['subject_id']; }
        if (!empty($filters['status']))      { $sql .= " AND a.status = :status";            $params[':status']      = $filters['status']; }
        if (!empty($filters['start_date']))  { $sql .= " AND a.date >= :start_date";         $params[':start_date']  = $filters['start_date']; }
        if (!empty($filters['end_date']))    { $sql .= " AND a.date <= :end_date";           $params[':end_date']    = $filters['end_date']; }
        $sql .= " ORDER BY a.date DESC, u.last_name, u.first_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getDayStats($courseId, $date) {
        $sql = "SELECT COUNT(*) as total,
                SUM(CASE WHEN status = 'presente' THEN 1 ELSE 0 END) as presente,
                SUM(CASE WHEN status = 'ausente'  THEN 1 ELSE 0 END) as ausente,
                SUM(CASE WHEN status = 'tardanza' THEN 1 ELSE 0 END) as tardanza
                FROM attendances WHERE course_id = :course_id AND date = :date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId, ':date' => $date]);
        return $stmt->fetch();
    }

    // ── MÉTODOS PARA DOCENTE TUTOR ────────────────────────────────

    // Obtener el course_id donde el docente es tutor en el año activo
    public function getTutorCourseId($teacherId) {
        $sql = "SELECT ta.course_id
                FROM teacher_assignments ta
                INNER JOIN school_years sy ON ta.school_year_id = sy.id
                WHERE ta.teacher_id = :teacher_id AND ta.is_tutor = 1 AND sy.is_active = 1
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':teacher_id' => $teacherId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['course_id'] : null;
    }

    // Asignaturas del curso (para filtro)
    public function getSubjectsByCourse($courseId) {
        $sql = "SELECT DISTINCT s.id, s.name
                FROM subjects s
                INNER JOIN teacher_assignments ta ON s.id = ta.subject_id
                WHERE ta.course_id = :course_id
                ORDER BY s.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        return $stmt->fetchAll();
    }

    // Estudiantes del curso (para filtro)
    public function getStudentsByCourse($courseId) {
        $sql = "SELECT u.id, CONCAT(u.last_name, ' ', u.first_name) as full_name
                FROM users u
                INNER JOIN course_students cs ON u.id = cs.student_id
                WHERE cs.course_id = :course_id
                ORDER BY u.last_name, u.first_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        return $stmt->fetchAll();
    }

    // Asistencias del curso con filtros (vista del tutor)
    public function getTutorCourseAttendance($courseId, $filters = []) {
        $sql = "SELECT 
                    a.id, a.date, a.hour_period, a.status, a.observation,
                    CONCAT(u.last_name, ' ', u.first_name) as student_name,
                    u.id as student_id,
                    s.name as subject_name,
                    s.id as subject_id,
                    CONCAT(t.last_name, ' ', t.first_name) as teacher_name
                FROM attendances a
                INNER JOIN users u ON a.student_id = u.id
                INNER JOIN subjects s ON a.subject_id = s.id
                INNER JOIN users t ON a.teacher_id = t.id
                WHERE a.course_id = :course_id";
        $params = [':course_id' => $courseId];

        if (!empty($filters['subject_id'])) { $sql .= " AND a.subject_id = :subject_id"; $params[':subject_id'] = $filters['subject_id']; }
        if (!empty($filters['student_id'])) { $sql .= " AND a.student_id = :student_id"; $params[':student_id'] = $filters['student_id']; }
        if (!empty($filters['status']))     { $sql .= " AND a.status = :status";          $params[':status']     = $filters['status']; }
        if (!empty($filters['start_date'])) { $sql .= " AND a.date >= :start_date";       $params[':start_date'] = $filters['start_date']; }
        if (!empty($filters['end_date']))   { $sql .= " AND a.date <= :end_date";         $params[':end_date']   = $filters['end_date']; }

        $sql .= " ORDER BY a.date DESC, u.last_name, u.first_name, s.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Estadísticas globales del curso (vista del tutor)
    public function getTutorCourseStats($courseId, $filters = []) {
        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'presente'    THEN 1 ELSE 0 END) as presente,
                    SUM(CASE WHEN status = 'ausente'     THEN 1 ELSE 0 END) as ausente,
                    SUM(CASE WHEN status = 'tardanza'    THEN 1 ELSE 0 END) as tardanza,
                    SUM(CASE WHEN status = 'justificado' THEN 1 ELSE 0 END) as justificado
                FROM attendances a
                WHERE a.course_id = :course_id";
        $params = [':course_id' => $courseId];

        if (!empty($filters['subject_id'])) { $sql .= " AND a.subject_id = :subject_id"; $params[':subject_id'] = $filters['subject_id']; }
        if (!empty($filters['student_id'])) { $sql .= " AND a.student_id = :student_id"; $params[':student_id'] = $filters['student_id']; }
        if (!empty($filters['start_date'])) { $sql .= " AND a.date >= :start_date";       $params[':start_date'] = $filters['start_date']; }
        if (!empty($filters['end_date']))   { $sql .= " AND a.date <= :end_date";         $params[':end_date']   = $filters['end_date']; }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    // Top estudiantes con más ausencias (vista del tutor)
    public function getTutorTopAbsences($courseId, $limit = 10) {
        $sql = "SELECT CONCAT(u.last_name, ' ', u.first_name) as student_name,
                    COUNT(*) as total_ausencias
                FROM attendances a
                INNER JOIN users u ON a.student_id = u.id
                WHERE a.course_id = :course_id AND a.status = 'ausente'
                GROUP BY a.student_id, u.last_name, u.first_name
                ORDER BY total_ausencias DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}