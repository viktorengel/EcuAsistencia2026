<?php
class Justification {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function create($data) {
        $sql = "INSERT INTO justifications 
                (attendance_id, student_id, submitted_by, reason, document_path) 
                VALUES (:attendance_id, :student_id, :submitted_by, :reason, :document_path)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getPending() {
        $sql = "SELECT j.*, 
                CONCAT(u.last_name, ' ', u.first_name) as student_name,
                CONCAT(s.last_name, ' ', s.first_name) as submitted_by_name,
                a.date as attendance_date,
                sub.name as subject_name
                FROM justifications j
                INNER JOIN users u ON j.student_id = u.id
                INNER JOIN users s ON j.submitted_by = s.id
                INNER JOIN attendances a ON j.attendance_id = a.id
                INNER JOIN subjects sub ON a.subject_id = sub.id
                WHERE j.status = 'pendiente'
                ORDER BY j.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getByStudent($studentId) {
        $sql = "SELECT j.*, 
                a.date as attendance_date,
                sub.name as subject_name,
                CONCAT(r.last_name, ' ', r.first_name) as reviewed_by_name
                FROM justifications j
                INNER JOIN attendances a ON j.attendance_id = a.id
                INNER JOIN subjects sub ON a.subject_id = sub.id
                LEFT JOIN users r ON j.reviewed_by = r.id
                WHERE j.student_id = :student_id
                ORDER BY j.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function approve($justificationId, $reviewerId, $notes = '') {
        $sql = "UPDATE justifications 
                SET status = 'aprobado', 
                    reviewed_by = :reviewed_by, 
                    review_notes = :notes,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':reviewed_by' => $reviewerId,
            ':notes' => $notes,
            ':id' => $justificationId
        ]);

        if ($result) {
            // Actualizar asistencia a justificado
            $sql2 = "UPDATE attendances a
                     INNER JOIN justifications j ON a.id = j.attendance_id
                     SET a.status = 'justificado'
                     WHERE j.id = :id";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([':id' => $justificationId]);
        }

        return $result;
    }

    public function reject($justificationId, $reviewerId, $notes) {
        $sql = "UPDATE justifications 
                SET status = 'rechazado', 
                    reviewed_by = :reviewed_by, 
                    review_notes = :notes,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':reviewed_by' => $reviewerId,
            ':notes' => $notes,
            ':id' => $justificationId
        ]);
    }
}