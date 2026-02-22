<?php
class Institution {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function getById($id) {
        $sql = "SELECT * FROM institutions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getAll() {
        $sql = "SELECT * FROM institutions ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $sql = "UPDATE institutions SET 
                name = :name,
                address = :address,
                province = :province,
                city = :city,
                phone = :phone,
                email = :email,
                director_name = :director_name,
                amie_code = :amie_code,
                website = :website,
                logo_path = :logo_path,
                working_days_list = :working_days_list,
                updated_at = NOW()
                WHERE id = :id";
        
        // Obtener logo actual si no se proporciona uno nuevo
        $currentInstitution = $this->getById($id);
        $logoPath = $data['logo_path'] ?? $currentInstitution['logo_path'];
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':address' => $data['address'],
            ':province' => $data['province'],
            ':city' => $data['city'],
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':director_name' => $data['director_name'],
            ':amie_code' => $data['amie_code'],
            ':website' => $data['website'],
            ':logo_path' => $logoPath,
            ':working_days_list' => $data['working_days_list'] ?? '["lunes","martes","miercoles","jueves","viernes"]'
        ]);
    }

    public function getShifts($institutionId) {
        $sql = "SELECT s.id, s.name, 
                GROUP_CONCAT(s.name ORDER BY s.name) as shift_names
                FROM shifts s
                INNER JOIN institution_shifts ins ON s.id = ins.shift_id
                WHERE ins.institution_id = :institution_id
                GROUP BY ins.institution_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':institution_id' => $institutionId]);
        return $stmt->fetch();
    }
}