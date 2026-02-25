<?php
class User {
    public $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function create($data) {
        $sql = "INSERT INTO users (institution_id, username, email, password, first_name, last_name, dni, phone) 
                VALUES (:institution_id, :username, :email, :password, :first_name, :last_name, :dni, :phone)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':institution_id' => $data['institution_id'],
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => Security::hashPassword($data['password']),
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':dni' => $data['dni'] ?? null,
            ':phone' => $data['phone'] ?? null
        ]);
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getUserRoles($userId) {
        $sql = "SELECT r.name FROM roles r 
                INNER JOIN user_roles ur ON r.id = ur.role_id 
                WHERE ur.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function assignRole($userId, $roleId) {
        $sql = "INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId, ':role_id' => $roleId]);
    }

    public function setResetToken($email, $token) {
        $sql = "UPDATE users SET reset_token = :token, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) 
                WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':token' => $token, ':email' => $email]);
    }

    public function validateResetToken($token) {
        $sql = "SELECT * FROM users WHERE reset_token = :token AND reset_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }

    public function resetPassword($userId, $newPassword) {
        $sql = "UPDATE users SET password = :password, reset_token = NULL, reset_expires = NULL 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password' => Security::hashPassword($newPassword),
            ':id' => $userId
        ]);
    }

    public function getAll() {
        $sql = "SELECT u.*, GROUP_CONCAT(r.name) as roles 
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.institution_id = :institution_id
                GROUP BY u.id
                ORDER BY u.last_name, u.first_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':institution_id' => $_SESSION['institution_id']]);
        return $stmt->fetchAll();
    }

    public function removeRole($userId, $roleId) {
        $sql = "DELETE FROM user_roles WHERE user_id = :user_id AND role_id = :role_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId, ':role_id' => $roleId]);
    }

    public function getUserWithRoles($userId) {
        $user = $this->findById($userId);
        if ($user) {
            $user['roles'] = $this->getUserRoles($userId);
        }
        return $user;
    }

/*     public function getByRole($roleName) {
        $sql = "SELECT u.* FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.name = :role_name AND u.institution_id = :institution_id
                ORDER BY u.last_name, u.first_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':role_name' => $roleName,
            ':institution_id' => $_SESSION['institution_id']
        ]);
        return $stmt->fetchAll();
    } */

    // Agregar al final de la clase User

    public function getByRole($roleName) {
        $sql = "SELECT u.* FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.name = :role_name AND u.institution_id = :institution_id
                ORDER BY u.last_name, u.first_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':role_name' => $roleName,
            ':institution_id' => $_SESSION['institution_id']
        ]);
        return $stmt->fetchAll();
    }

    public function getStudentsNotEnrolled($schoolYearId) {
        $sql = "SELECT u.* FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.name = 'estudiante' 
                AND u.institution_id = :institution_id
                AND u.id NOT IN (
                    SELECT student_id FROM course_students 
                    WHERE school_year_id = :school_year_id
                )
                ORDER BY u.last_name, u.first_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':institution_id' => $_SESSION['institution_id'],
            ':school_year_id' => $schoolYearId
        ]);
        return $stmt->fetchAll();
    }

    public function getStudentCourse($studentId, $schoolYearId) {
        $sql = "SELECT c.*, s.name as shift_name 
                FROM courses c
                INNER JOIN course_students cs ON c.id = cs.course_id
                INNER JOIN shifts s ON c.shift_id = s.id
                WHERE cs.student_id = :student_id 
                AND cs.school_year_id = :school_year_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':student_id' => $studentId,
            ':school_year_id' => $schoolYearId
        ]);
        return $stmt->fetch();
    }

    public function updateProfile($userId, $data) {
        $sql = "UPDATE users 
                SET first_name = :first_name, 
                    last_name = :last_name, 
                    phone = :phone, 
                    email = :email,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':id' => $userId
        ]);
    }

    public function updatePassword($userId, $newPassword) {
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password' => Security::hashPassword($newPassword),
            ':id' => $userId
        ]);
    }

    public function findByEmailOrUsername($emailOrUsername) {
        $sql = "SELECT * FROM users 
                WHERE (email = :email OR username = :username) 
                AND is_active = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => $emailOrUsername,
            ':username' => $emailOrUsername
        ]);
        return $stmt->fetch();
    }

    public function getUserRoleIds($userId) {
        $sql = "SELECT r.id FROM roles r 
                INNER JOIN user_roles ur ON r.id = ur.role_id 
                WHERE ur.user_id = :user_id
                ORDER BY r.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    }

    public function update($data) {
        // Si se incluye password, actualizarla
        if (isset($data['password'])) {
            $sql = "UPDATE users SET 
                    username = :username, 
                    email = :email, 
                    password = :password,
                    first_name = :first_name, 
                    last_name = :last_name, 
                    dni = :dni, 
                    phone = :phone,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $params = [
                ':id' => $data['id'],
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => Security::hashPassword($data['password']),
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':dni' => $data['dni'],
                ':phone' => $data['phone']
            ];
        } else {
            // No actualizar password
            $sql = "UPDATE users SET 
                    username = :username, 
                    email = :email, 
                    first_name = :first_name, 
                    last_name = :last_name, 
                    dni = :dni, 
                    phone = :phone,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $params = [
                ':id' => $data['id'],
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':dni' => $data['dni'],
                ':phone' => $data['phone']
            ];
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($userId) {
        // 1. Eliminar roles
        $this->db->prepare("DELETE FROM user_roles WHERE user_id = :uid")
            ->execute([':uid' => $userId]);

        // 2. Eliminar representaciones
        $this->db->prepare("DELETE FROM representatives WHERE representative_id = :rid OR student_id = :sid")
            ->execute([':rid' => $userId, ':sid' => $userId]);

        // 3. Eliminar matrículas
        $this->db->prepare("DELETE FROM course_students WHERE student_id = :uid")
            ->execute([':uid' => $userId]);

        // 4. Eliminar entradas del horario donde era el docente asignado
        $this->db->prepare("DELETE FROM class_schedule WHERE teacher_id = :uid")
            ->execute([':uid' => $userId]);

        // 5. Eliminar asignaciones docentes
        $this->db->prepare("DELETE FROM teacher_assignments WHERE teacher_id = :uid")
            ->execute([':uid' => $userId]);

        // 6. Eliminar asistencias registradas por o para este usuario
        $this->db->prepare("DELETE FROM attendances WHERE student_id = :uid OR teacher_id = :uid2")
            ->execute([':uid' => $userId, ':uid2' => $userId]);

        // 7. Eliminar usuario
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $userId]);
    }

    public function deactivate($userId) {
        $sql = "UPDATE users SET is_active = 0, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $userId]);
    }

    public function findByDni(string $dni, int $excludeId = 0) {
        $sql = "SELECT id FROM users WHERE dni = :dni AND id != :exclude AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':dni' => $dni, ':exclude' => $excludeId]);
        return $stmt->fetch();
    }
}