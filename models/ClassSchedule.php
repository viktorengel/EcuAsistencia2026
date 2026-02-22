<?php
class ClassSchedule {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function create($data) {
        // Verificar si ya existe una clase en ese horario
        $checkSql = "SELECT s.name as subject_name, 
                     COALESCE(CONCAT(u.last_name, ' ', u.first_name), 'Sin docente') as teacher_name
                     FROM class_schedule cs
                     INNER JOIN subjects s ON cs.subject_id = s.id
                     LEFT JOIN users u ON cs.teacher_id = u.id
                     WHERE cs.course_id = :course_id 
                     AND cs.day_of_week = :day_of_week 
                     AND cs.period_number = :period_number
                     AND cs.school_year_id = :school_year_id";
        
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([
            ':course_id' => $data[':course_id'],
            ':day_of_week' => $data[':day_of_week'],
            ':period_number' => $data[':period_number'],
            ':school_year_id' => $data[':school_year_id']
        ]);
        
        $existing = $checkStmt->fetch();
        
        if ($existing) {
            return [
                'success' => false,
                'message' => 'Ya existe ' . $existing['subject_name'] . ' con ' . $existing['teacher_name'] . ' en este horario'
            ];
        }
        
        $sql = "INSERT INTO class_schedule 
                (course_id, subject_id, teacher_id, school_year_id, day_of_week, period_number) 
                VALUES (:course_id, :subject_id, :teacher_id, :school_year_id, :day_of_week, :period_number)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($data);
        
        return [
            'success' => $result,
            'message' => 'Clase agregada correctamente'
        ];
    }

    public function getByCourse($courseId, $schoolYearId) {
        $sql = "SELECT cs.*, 
                s.name as subject_name,
                COALESCE(CONCAT(u.last_name, ' ', u.first_name), 'Sin docente') as teacher_name
                FROM class_schedule cs
                INNER JOIN subjects s ON cs.subject_id = s.id
                LEFT JOIN users u ON cs.teacher_id = u.id
                WHERE cs.course_id = :course_id 
                AND cs.school_year_id = :school_year_id
                ORDER BY cs.day_of_week, cs.period_number";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':course_id' => $courseId,
            ':school_year_id' => $schoolYearId
        ]);
        return $stmt->fetchAll();
    }

    public function getTeacherScheduleToday($teacherId, $schoolYearId) {
        $dayName = $this->getCurrentDayName();
        
        $sql = "SELECT cs.*, 
                c.name as course_name,
                c.grade_level,
                s.name as subject_name
                FROM class_schedule cs
                INNER JOIN courses c ON cs.course_id = c.id
                INNER JOIN subjects s ON cs.subject_id = s.id
                WHERE cs.teacher_id = :teacher_id 
                AND cs.school_year_id = :school_year_id
                AND cs.day_of_week = :day_of_week
                ORDER BY cs.period_number";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':teacher_id' => $teacherId,
            ':school_year_id' => $schoolYearId,
            ':day_of_week' => $dayName
        ]);
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $sql = "DELETE FROM class_schedule WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    private function getCurrentDayName() {
        // Forzar zona horaria Ecuador
        $tz = new DateTimeZone('America/Guayaquil');
        $now = new DateTime('now', $tz);
        $dayNumber = (int)$now->format('N'); // 1=Lunes, 7=Domingo
        
        $days = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado'
        ];
        
        return $days[$dayNumber] ?? 'lunes';
    }
}