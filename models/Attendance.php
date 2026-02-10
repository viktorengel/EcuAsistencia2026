<?php
class Attendance {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function create($data) {
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
        
        $hoursElapsed = (time() - strtotime($record['created_at'])) / 3600;
        return $hoursElapsed <= EDIT_ATTENDANCE_HOURS;
    }

    public function update($id, $data) {
        if (!$this->canEdit($id)) return false;
        
        $sql = "UPDATE attendances SET status = :status, observation = :observation WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $data['status'],
            ':observation' => $data['observation'],
            ':id' => $id
        ]);
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
        $sql = "SELECT a.*, s.name as subject_name, c.name as course_name,
                sh.name as shift_name
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

    // Agregar al final de la clase Attendance

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
        $stmt->execute([
            ':course_id' => $courseId,
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
        return $stmt->fetchAll();
    }

    public function getStatsByCourse($courseId, $startDate, $endDate) {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'presente' THEN 1 ELSE 0 END) as presente,
                SUM(CASE WHEN status = 'ausente' THEN 1 ELSE 0 END) as ausente,
                SUM(CASE WHEN status = 'tardanza' THEN 1 ELSE 0 END) as tardanza,
                SUM(CASE WHEN status = 'justificado' THEN 1 ELSE 0 END) as justificado
                FROM attendances
                WHERE course_id = :course_id 
                AND date BETWEEN :start_date AND :end_date";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':course_id' => $courseId,
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
        return $stmt->fetch();
    }
}