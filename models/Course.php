<?php
class Course {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function getAll() {
        $sql = "SELECT c.*, s.name as shift_name, sy.name as year_name 
                FROM courses c
                INNER JOIN shifts s ON c.shift_id = s.id
                INNER JOIN school_years sy ON c.school_year_id = sy.id
                WHERE c.institution_id = :institution_id
                ORDER BY c.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':institution_id' => $_SESSION['institution_id']]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO courses (institution_id, school_year_id, name, grade_level, parallel, shift_id) 
                VALUES (:institution_id, :school_year_id, :name, :grade_level, :parallel, :shift_id)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getStudents($courseId) {
        $sql = "SELECT u.id, u.first_name, u.last_name, u.dni 
                FROM users u
                INNER JOIN course_students cs ON u.id = cs.student_id
                WHERE cs.course_id = :course_id
                ORDER BY u.last_name, u.first_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        return $stmt->fetchAll();
    }

    public function enrollStudent($courseId, $studentId, $schoolYearId) {
        // Primero verificar si ya está matriculado en otro curso del mismo año
        $checkSql = "SELECT COUNT(*) as count FROM course_students 
                     WHERE student_id = :student_id AND school_year_id = :school_year_id";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([
            ':student_id' => $studentId,
            ':school_year_id' => $schoolYearId
        ]);
        $result = $checkStmt->fetch();
        
        if ($result['count'] > 0) {
            return false; // Ya está matriculado
        }
        
        $sql = "INSERT INTO course_students (student_id, course_id, school_year_id, enrollment_date) 
                VALUES (:student_id, :course_id, :school_year_id, CURDATE())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':student_id' => $studentId,
            ':course_id' => $courseId,
            ':school_year_id' => $schoolYearId
        ]);
    }

    public function getEnrolledStudents($courseId) {
        $sql = "SELECT u.id, u.first_name, u.last_name, u.dni, cs.enrollment_date 
                FROM users u
                INNER JOIN course_students cs ON u.id = cs.student_id
                WHERE cs.course_id = :course_id
                ORDER BY u.last_name, u.first_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT c.*, s.name as shift_name 
                FROM courses c
                INNER JOIN shifts s ON c.shift_id = s.id
                WHERE c.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function update($data) {
        $sql = "UPDATE courses SET 
                name = :name,
                grade_level = :grade_level,
                parallel = :parallel,
                shift_id = :shift_id,
                updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id) {
        // Eliminar relaciones primero
        $sql = "DELETE FROM course_students WHERE course_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Eliminar horarios
        $sql = "DELETE FROM class_schedule WHERE course_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Eliminar curso
        $sql = "DELETE FROM courses WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function unenrollStudent($studentId, $schoolYearId) {
        $sql = "DELETE FROM course_students 
                WHERE student_id = :student_id AND school_year_id = :school_year_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':student_id' => $studentId,
            ':school_year_id' => $schoolYearId
        ]);
    }
}