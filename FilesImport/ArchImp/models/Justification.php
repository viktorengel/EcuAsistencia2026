<?php
class Justification {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    public function create($data) {
        $sql = "INSERT INTO justifications 
                (attendance_id, student_id, submitted_by, reason, document_path) 
                VALUES (:attendance_id, :student_id, :submitted_by, :reason, :document_path)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getPending() {
        $sql = "SELECT j.*, 
                CONCAT(u.last_name, ' ', u.first_name) as student_name,
                CONCAT(s.last_name, ' ', s.first_name) as submitted_by_name,
                a.date as attendance_date,
                sub.name as subject_name,
                c.name as course_name
                FROM justifications j
                INNER JOIN users u ON j.student_id = u.id
                INNER JOIN users s ON j.submitted_by = s.id
                INNER JOIN attendances a ON j.attendance_id = a.id
                INNER JOIN subjects sub ON a.subject_id = sub.id
                LEFT JOIN course_students cs ON u.id = cs.student_id
                LEFT JOIN courses c ON cs.course_id = c.id
                WHERE j.status = 'pendiente'
                ORDER BY j.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getByStudent($studentId) {
        $sql = "SELECT j.*, 
                a.date as attendance_date,
                sub.name as subject_name,
                j.created_at as submitted_at,
                j.updated_at as reviewed_at,
                CONCAT(r.last_name, ' ', r.first_name) as reviewer_name
                FROM justifications j
                INNER JOIN attendances a ON j.attendance_id = a.id
                INNER JOIN subjects sub ON a.subject_id = sub.id
                LEFT JOIN users r ON j.reviewed_by = r.id
                WHERE j.student_id = :student_id
                ORDER BY j.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function approve($justificationId, $reviewerId, $notes = '') {
        $sql = "UPDATE justifications 
                SET status = 'aprobado', 
                    reviewed_by = :reviewed_by, 
                    review_notes = :notes,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':reviewed_by' => $reviewerId,
            ':notes' => $notes,
            ':id' => $justificationId
        ]);

        if ($result) {
            // Actualizar asistencia a justificado
            $sql2 = "UPDATE attendances a
                     INNER JOIN justifications j ON a.id = j.attendance_id
                     SET a.status = 'justificado'
                     WHERE j.id = :id";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([':id' => $justificationId]);
        }

        return $result;
    }

    public function reject($justificationId, $reviewerId, $notes) {
        $sql = "UPDATE justifications 
                SET status = 'rechazado', 
                    reviewed_by = :reviewed_by, 
                    review_notes = :notes,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':reviewed_by' => $reviewerId,
            ':notes' => $notes,
            ':id' => $justificationId
        ]);
    }

    public function existsByAttendance($attendanceId) {
        $sql = "SELECT COUNT(*) as count FROM justifications WHERE attendance_id = :attendance_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':attendance_id' => $attendanceId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function getById($id) {
        $sql = "SELECT j.*, j.student_id, j.submitted_by,
                    CONCAT(u.last_name, ' ', u.first_name) as student_name
                FROM justifications j
                INNER JOIN users u ON j.student_id = u.id
                WHERE j.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }    

    public function getReviewed($institutionId = null) {
        $sql = "SELECT j.*, 
                CONCAT(u.last_name, ' ', u.first_name) as student_name,
                CONCAT(s.last_name, ' ', s.first_name) as submitted_by_name,
                CONCAT(r.last_name, ' ', r.first_name) as reviewer_name,
                a.date as attendance_date,
                a.hour_period,
                sub.name as subject_name,
                c.name as course_name
                FROM justifications j
                INNER JOIN users u ON j.student_id = u.id
                INNER JOIN users s ON j.submitted_by = s.id
                INNER JOIN attendances a ON j.attendance_id = a.id
                INNER JOIN subjects sub ON a.subject_id = sub.id
                LEFT JOIN users r ON j.reviewed_by = r.id
                LEFT JOIN course_students cs ON u.id = cs.student_id
                LEFT JOIN courses c ON cs.course_id = c.id
                WHERE j.status IN ('aprobado', 'rechazado')
                ORDER BY j.updated_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // ── Crear justificación por rango de fechas ──────────────────────────
    public function createByRange($data) {
        $sql = "INSERT INTO justifications
                    (student_id, submitted_by, date_from, date_to,
                     working_days, reason_type, reason, document_path, can_approve, status)
                VALUES
                    (:student_id, :submitted_by, :date_from, :date_to,
                     :working_days, :reason_type, :reason, :document_path, :can_approve, 'pendiente')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // ── Contar días laborables entre dos fechas (lun-vie) ────────────────
    public static function countWorkingDays($from, $to) {
        $count = 0;
        $cur   = strtotime($from);
        $end   = strtotime($to);
        while ($cur <= $end) {
            $dow = (int)date('N', $cur); // 1=lun, 7=dom
            if ($dow < 6) $count++;     // lun-vie
            $cur = strtotime('+1 day', $cur);
        }
        return $count;
    }

    // ── Determinar quién puede aprobar según días laborables ─────────────
    // Tutor: hasta 3 días | Inspector/Autoridad: más de 3
    public static function resolveApprover($workingDays) {
        return $workingDays <= 3 ? 'tutor' : 'inspector';
    }

    // ── Obtener justificaciones pendientes con datos de rango ────────────
    public function getPendingByApprover($approverRole) {
        // tutor ve las suyas (can_approve='tutor')
        // inspector/autoridad ven las de inspector y autoridad
        if ($approverRole === 'tutor') {
            $where = "j.can_approve = 'tutor'";
        } else {
            $where = "j.can_approve IN ('inspector','autoridad')";
        }

        $sql = "SELECT j.*,
                    CONCAT(u.last_name, ' ', u.first_name) as student_name,
                    CONCAT(s.last_name, ' ', s.first_name) as submitted_by_name,
                    a.date  as attendance_date,
                    sub.name as subject_name,
                    c.name   as course_name
                FROM justifications j
                INNER JOIN users u  ON j.student_id   = u.id
                INNER JOIN users s  ON j.submitted_by  = s.id
                LEFT  JOIN attendances a   ON j.attendance_id = a.id
                LEFT  JOIN subjects    sub ON a.subject_id    = sub.id
                LEFT  JOIN course_students cs ON u.id = cs.student_id
                LEFT  JOIN courses c ON cs.course_id = c.id
                WHERE j.status = 'pendiente' AND $where
                ORDER BY j.created_at DESC";

        return $this->db->query($sql)->fetchAll();
    }

    // ── El tutor solo aprueba las de SU curso ────────────────────────────
    public function getPendingForTutor($tutorCourseId) {
        $sql = "SELECT j.*,
                    CONCAT(u.last_name, ' ', u.first_name) as student_name,
                    CONCAT(s.last_name, ' ', s.first_name) as submitted_by_name,
                    c.name as course_name
                FROM justifications j
                INNER JOIN users u ON j.student_id  = u.id
                INNER JOIN users s ON j.submitted_by = s.id
                INNER JOIN course_students cs ON u.id = cs.student_id
                INNER JOIN courses c ON cs.course_id = c.id
                WHERE j.status = 'pendiente'
                  AND j.can_approve = 'tutor'
                  AND cs.course_id = :course_id
                ORDER BY j.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $tutorCourseId]);
        return $stmt->fetchAll();
    }

    // ── Aprobar justificación por rango (sin attendance_id) ──────────────
    public function approveRange($justificationId, $reviewerId, $notes = '') {
        $sql = "UPDATE justifications
                SET status='aprobado', reviewed_by=:reviewed_by,
                    review_notes=:notes, updated_at=NOW()
                WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':reviewed_by' => $reviewerId,
            ':notes'       => $notes,
            ':id'          => $justificationId,
        ]);

        if ($result) {
            // Si tiene attendance_id único, actualizar también esa asistencia
            $j = $this->getById($justificationId);
            if (!empty($j['attendance_id'])) {
                $this->db->prepare(
                    "UPDATE attendances SET status='justificado' WHERE id=:id"
                )->execute([':id' => $j['attendance_id']]);
            }
            // Si tiene rango de fechas, actualizar todas las ausencias del estudiante en ese rango
            if (!empty($j['date_from']) && !empty($j['date_to'])) {
                $this->db->prepare(
                    "UPDATE attendances SET status='justificado'
                     WHERE student_id=:sid AND date BETWEEN :df AND :dt AND status='ausente'"
                )->execute([
                    ':sid' => $j['student_id'],
                    ':df'  => $j['date_from'],
                    ':dt'  => $j['date_to'],
                ]);
            }
        }
        return $result;
    }

    // Crear una justificación por cada asistencia seleccionada
    public function createForAttendances($attendanceIds, $data) {
        $sql = "INSERT INTO justifications
                    (attendance_id, student_id, submitted_by,
                     date_from, date_to, working_days,
                     reason_type, reason, document_path, can_approve, status)
                VALUES
                    (:attendance_id, :student_id, :submitted_by,
                     :date_from, :date_to, :working_days,
                     :reason_type, :reason, :document_path, :can_approve, 'pendiente')";
        $stmt = $this->db->prepare($sql);
        $ok = true;
        foreach ($attendanceIds as $attId) {
            $row = $data;
            $row[':attendance_id'] = (int)$attId;
            if (!$stmt->execute($row)) $ok = false;
        }
        return $ok;
    }

    // Contar ausencias ya justificadas (pendiente o aprobada) para un estudiante en un rango
    public function countPendingJustifications($studentId) {
        $sql = "SELECT COUNT(*) FROM justifications
                WHERE student_id = :sid AND status = 'pendiente'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sid' => $studentId]);
        return (int)$stmt->fetchColumn();
    }
}