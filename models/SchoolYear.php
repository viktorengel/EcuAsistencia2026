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


    public function findById($id) {
        $sql = "SELECT * FROM school_years
                WHERE id = :id AND institution_id = :institution_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':institution_id' => $_SESSION['institution_id']
        ]);

        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO school_years
                (institution_id, name, start_date, end_date, is_active)
                VALUES
                (:institution_id, :name, :start_date, :end_date, :is_active)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':institution_id' => $data['institution_id'],
            ':name' => $data['name'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':is_active' => $data['is_active']
        ]);
    }

    public function update($data) {
        $sql = "UPDATE school_years
                SET name=:name,
                    start_date=:start_date,
                    end_date=:end_date
                WHERE id=:id
                AND institution_id=:institution_id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id'=>$data['id'],
            ':name'=>$data['name'],
            ':start_date'=>$data['start_date'],
            ':end_date'=>$data['end_date'],
            ':institution_id'=>$_SESSION['institution_id']
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM school_years
             WHERE id=:id AND institution_id=:institution_id"
        );

        return $stmt->execute([
            ':id'=>$id,
            ':institution_id'=>$_SESSION['institution_id']
        ]);
    }

    public function activate($id) {

        // desactiva todos
        $this->db->prepare(
            "UPDATE school_years
             SET is_active=0
             WHERE institution_id=:institution_id"
        )->execute([':institution_id'=>$_SESSION['institution_id']]);

        if ($id == 0) return true;

        // activa uno
        $stmt = $this->db->prepare(
            "UPDATE school_years
             SET is_active=1
             WHERE id=:id AND institution_id=:institution_id"
        );

        return $stmt->execute([
            ':id'=>$id,
            ':institution_id'=>$_SESSION['institution_id']
        ]);
    }

    public function deactivate($id) {
        $stmt = $this->db->prepare(
            "UPDATE school_years
             SET is_active=0
             WHERE id=:id AND institution_id=:institution_id"
        );

        return $stmt->execute([
            ':id'=>$id,
            ':institution_id'=>$_SESSION['institution_id']
        ]);
    }

    public function checkOverlap($start, $end, $excludeId = null) {

        $sql = "SELECT COUNT(*)
                FROM school_years
                WHERE institution_id = :institution_id
                AND (start_date <= :end AND end_date >= :start)";

        if ($excludeId) {
            $sql .= " AND id != :excludeId";
        }

        $stmt = $this->db->prepare($sql);

        $params = [
            ':institution_id' => $_SESSION['institution_id'],
            ':start' => $start,
            ':end' => $end
        ];

        if ($excludeId) {
            $params[':excludeId'] = $excludeId;
        }

        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

}