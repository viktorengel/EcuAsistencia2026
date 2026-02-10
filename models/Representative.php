<?php
class Representative {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function assignStudent($representativeId, $studentId, $relationship, $isPrimary = 0) {
        $sql = "INSERT INTO representatives (representative_id, student_id, relationship, is_primary) 
                VALUES (:representative_id, :student_id, :relationship, :is_primary)
                ON DUPLICATE KEY UPDATE relationship = :relationship, is_primary = :is_primary";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':representative_id' => $representativeId,
            ':student_id' => $studentId,
            ':relationship' => $relationship,
            ':is_primary' => $isPrimary
        ]);
    }

    public function getStudentsByRepresentative($representativeId) {
        $sql = "SELECT u.*, r.relationship, r.is_primary, c.name as course_name, sh.name as shift_name
                FROM users u
                INNER JOIN representatives r ON u.id = r.student_id
                LEFT JOIN course_students cs ON u.id = cs.student_id
                LEFT JOIN courses c ON cs.course_id = c.id
                LEFT JOIN shifts sh ON c.shift_id = sh.id
                WHERE r.representative_id = :representative_id
                ORDER BY r.is_primary DESC, u.last_name, u.first_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':representative_id' => $representativeId]);
        return $stmt->fetchAll();
    }

    public function getRepresentativesByStudent($studentId) {
        $sql = "SELECT u.*, r.relationship, r.is_primary
                FROM users u
                INNER JOIN representatives r ON u.id = r.representative_id
                WHERE r.student_id = :student_id
                ORDER BY r.is_primary DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function removeStudent($representativeId, $studentId) {
        $sql = "DELETE FROM representatives 
                WHERE representative_id = :representative_id AND student_id = :student_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':representative_id' => $representativeId,
            ':student_id' => $studentId
        ]);
    }
}