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
        // 1. Desmatricular estudiantes
        $this->db->prepare("DELETE FROM course_students WHERE course_id = :id")->execute([':id' => $id]);
        // 2. Eliminar asignaciones docentes (incluye tutor)
        $this->db->prepare("DELETE FROM teacher_assignments WHERE course_id = :id")->execute([':id' => $id]);
        // 3. Eliminar horarios
        $this->db->prepare("DELETE FROM class_schedule WHERE course_id = :id")->execute([':id' => $id]);
        // 4. Eliminar asignaturas del curso
        $this->db->prepare("DELETE FROM course_subjects WHERE course_id = :id")->execute([':id' => $id]);
        // 5. Eliminar curso
        return $this->db->prepare("DELETE FROM courses WHERE id = :id")->execute([':id' => $id]);
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

    public function getAllWithTutor() {
        $sql = "SELECT c.*, s.name as shift_name, sy.name as year_name,
                       u.first_name as tutor_first, u.last_name as tutor_last
                FROM courses c
                INNER JOIN shifts s ON c.shift_id = s.id
                INNER JOIN school_years sy ON c.school_year_id = sy.id
                LEFT JOIN teacher_assignments ta ON (ta.course_id = c.id AND ta.is_tutor = 1 AND ta.school_year_id = c.school_year_id)
                LEFT JOIN users u ON u.id = ta.teacher_id
                WHERE c.institution_id = :institution_id
                ORDER BY c.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':institution_id' => $_SESSION['institution_id']]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene el máximo de horas por día según el tipo de curso
     */
    public function getMaxHoursPerDay($courseId) {
        $sql = "SELECT grade_level FROM courses WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $courseId]);
        $course = $stmt->fetch();
        
        if (!$course) return 7; // Valor por defecto
        
        $gradeLevel = $course['grade_level'];
        
        // Determinar si es técnico (BT) para 8 horas, si no 7 horas
        if (strpos($gradeLevel, 'BT') !== false || strpos($gradeLevel, 'Técnico') !== false) {
            return 8; // Bachillerato Técnico: 8 horas
        }
        
        return 7; // Educación regular: 7 horas
    }

    /**
     * Calcula las horas totales disponibles en la semana
     */
    public function getTotalWeeklyHoursAvailable($courseId) {
        $hoursPerDay = $this->getMaxHoursPerDay($courseId);
        $workingDays = $this->getWorkingDaysCount(); // Necesitamos este método
        return $hoursPerDay * $workingDays;
    }

    /**
     * Obtiene la cantidad de días laborables configurados
     */
    public function getWorkingDaysCount() {
        // Por defecto 5 días (lunes a viernes)
        $defaultDays = 5;
        
        if (empty($_SESSION['institution_id'])) return $defaultDays;
        
        $sql = "SELECT working_days_list FROM institutions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $_SESSION['institution_id']]);
        $institution = $stmt->fetch();
        
        if (empty($institution['working_days_list'])) return $defaultDays;
        
        $days = json_decode($institution['working_days_list'], true);
        return is_array($days) ? count($days) : $defaultDays;
    }

    /**
     * Calcula la suma total de horas asignadas a todas las materias del curso
     */
    public function getTotalAssignedHours($courseId) {
        $sql = "SELECT SUM(cs.hours_per_week) as total
                FROM course_subjects cs
                WHERE cs.course_id = :course_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['course_id' => $courseId]);
        $result = $stmt->fetch();
        
        return (int)($result['total'] ?? 0);
    }

    /**
     * Verifica si se puede asignar una cantidad de horas a una materia
     */
    public function canAssignHours($courseId, $subjectId, $newHours) {
        $totalAvailable = $this->getTotalWeeklyHoursAvailable($courseId);
        $currentTotal = $this->getTotalAssignedHours($courseId);
        
        // Obtener horas actuales de esta materia específica
        $sql = "SELECT hours_per_week FROM course_subjects 
                WHERE course_id = :course_id AND subject_id = :subject_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['course_id' => $courseId, 'subject_id' => $subjectId]);
        $current = $stmt->fetch();
        
        $currentSubjectHours = $current ? (int)$current['hours_per_week'] : 0;
        
        // Calcular nuevo total restando lo que ya tenía esta materia
        $newTotal = ($currentTotal - $currentSubjectHours) + $newHours;
        
        return [
            'allowed' => $newTotal <= $totalAvailable,
            'currentTotal' => $currentTotal,
            'newTotal' => $newTotal,
            'available' => $totalAvailable,
            'remaining' => $totalAvailable - ($currentTotal - $currentSubjectHours)
        ];
    }
}