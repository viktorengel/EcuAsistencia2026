<?php
class InstitutionShift {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function assign($data) {
        $sql = "INSERT INTO institution_shifts (institution_id, shift_id) 
                VALUES (:institution_id, :shift_id)
                ON DUPLICATE KEY UPDATE institution_id = institution_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function remove($institutionId, $shiftId) {
        $sql = "DELETE FROM institution_shifts 
                WHERE institution_id = :institution_id AND shift_id = :shift_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':institution_id' => $institutionId,
            ':shift_id' => $shiftId
        ]);
    }

    public function getByInstitution($institutionId) {
        $sql = "SELECT s.id, s.name 
                FROM shifts s
                INNER JOIN institution_shifts ins ON s.id = ins.shift_id
                WHERE ins.institution_id = :institution_id
                ORDER BY s.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':institution_id' => $institutionId]);
        return $stmt->fetchAll();
    }

    public function getInstitutionShiftIds($institutionId) {
        $sql = "SELECT shift_id FROM institution_shifts WHERE institution_id = :institution_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':institution_id' => $institutionId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}