<?php
class SchoolYear {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function getActive() {
        $sql = "SELECT * FROM school_years 
                WHERE institution_id = :institution_id AND is_active = 1 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':institution_id' => $_SESSION['institution_id']]);
        return $stmt->fetch();
    }

    public function getAll() {
        $sql = "SELECT * FROM school_years 
                WHERE institution_id = :institution_id 
                ORDER BY start_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':institution_id' => $_SESSION['institution_id']]);
        return $stmt->fetchAll();
    }
}