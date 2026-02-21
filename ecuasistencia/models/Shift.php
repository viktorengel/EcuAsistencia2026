<?php
class Shift {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function getAll() {
        $sql = "SELECT * FROM shifts ORDER BY id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}