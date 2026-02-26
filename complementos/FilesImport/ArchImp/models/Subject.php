<?php
class Subject {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function getAll() {
        $sql = "SELECT * FROM subjects 
                WHERE institution_id = :institution_id 
                ORDER BY name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':institution_id' => $_SESSION['institution_id']]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO subjects (institution_id, name, code) 
                VALUES (:institution_id, :name, :code)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function findById($id) {
        $sql = "SELECT * FROM subjects WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function update($data) {
        $sql = "UPDATE subjects SET 
                name = :name,
                code = :code,
                updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $sql = "DELETE FROM subjects WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}