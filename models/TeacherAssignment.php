<?php
class TeacherAssignment {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function assign($data) {
        $sql = "INSERT INTO teacher_assignments 
                (teacher_id, course_id, subject_id, school_year_id, is_tutor) 
                VALUES (:teacher_id, :course_id, :subject_id, :school_year_id, :is_tutor)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getByTeacher($teacherId) {
        $sql = "SELECT ta.*, c.name as course_name, s.name as subject_name, 
                sh.name as shift_name, sy.name as year_name
                FROM teacher_assignments ta
                INNER JOIN courses c ON ta.course_id = c.id
                INNER JOIN subjects s ON ta.subject_id = s.id
                INNER JOIN shifts sh ON c.shift_id = sh.id
                INNER JOIN school_years sy ON ta.school_year_id = sy.id
                WHERE ta.teacher_id = :teacher_id
                ORDER BY c.name, s.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }

    public function getByCourse($courseId) {
        $sql = "SELECT ta.*, 
                CONCAT(u.last_name, ' ', u.first_name) as teacher_name,
                s.name as subject_name
                FROM teacher_assignments ta
                INNER JOIN users u ON ta.teacher_id = u.id
                INNER JOIN subjects s ON ta.subject_id = s.id
                WHERE ta.course_id = :course_id
                ORDER BY s.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        return $stmt->fetchAll();
    }

    public function getTutorByCourse($courseId, $schoolYearId) {
        $sql = "SELECT u.*, ta.id as assignment_id
                FROM teacher_assignments ta
                INNER JOIN users u ON ta.teacher_id = u.id
                WHERE ta.course_id = :course_id 
                AND ta.school_year_id = :school_year_id
                AND ta.is_tutor = 1
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':course_id' => $courseId,
            ':school_year_id' => $schoolYearId
        ]);
        return $stmt->fetch();
    }

    public function setTutor($courseId, $teacherId, $schoolYearId) {
        // Primero quitar tutor actual del curso
        $sql1 = "UPDATE teacher_assignments 
                 SET is_tutor = 0 
                 WHERE course_id = :course_id AND school_year_id = :school_year_id";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute([
            ':course_id' => $courseId,
            ':school_year_id' => $schoolYearId
        ]);

        // Luego asignar nuevo tutor
        $sql2 = "UPDATE teacher_assignments 
                 SET is_tutor = 1 
                 WHERE course_id = :course_id 
                 AND teacher_id = :teacher_id 
                 AND school_year_id = :school_year_id";
        $stmt2 = $this->db->prepare($sql2);
        return $stmt2->execute([
            ':course_id' => $courseId,
            ':teacher_id' => $teacherId,
            ':school_year_id' => $schoolYearId
        ]);
    }

    public function remove($assignmentId) {
        $sql = "DELETE FROM teacher_assignments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $assignmentId]);
    }

    public function getAll() {
        $sql = "SELECT ta.*, 
                CONCAT(u.last_name, ' ', u.first_name) as teacher_name,
                c.name as course_name,
                s.name as subject_name,
                sy.name as year_name
                FROM teacher_assignments ta
                INNER JOIN users u ON ta.teacher_id = u.id
                INNER JOIN courses c ON ta.course_id = c.id
                INNER JOIN subjects s ON ta.subject_id = s.id
                INNER JOIN school_years sy ON ta.school_year_id = sy.id
                ORDER BY c.name, s.name";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}