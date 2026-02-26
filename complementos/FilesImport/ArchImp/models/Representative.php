<?php
class Representative {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    /**
     * Verifica si ya existe un representante con el mismo parentesco exclusivo (Padre/Madre)
     * para el estudiante dado. Excluye al propio representante si es una actualización.
     */
    public function hasExclusiveRelationship($studentId, $relationship, $excludeRepId = null) {
        $exclusivos = ['Padre', 'Madre'];
        if (!in_array($relationship, $exclusivos)) return false;

        $sql = "SELECT COUNT(*) FROM representatives
                WHERE student_id = :student_id
                  AND relationship = :relationship";

        if ($excludeRepId) {
            $sql .= " AND representative_id != :exclude_rep";
        }

        $stmt = $this->db->prepare($sql);
        $params = [':student_id' => $studentId, ':relationship' => $relationship];
        if ($excludeRepId) $params[':exclude_rep'] = $excludeRepId;

        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function assignStudent($representativeId, $studentId, $relationship, $isPrimary = 0) {
        // Verificar duplicado de parentesco exclusivo
        // Se excluye al propio representante para permitir actualizar su propio parentesco
        if ($this->hasExclusiveRelationship($studentId, $relationship, $representativeId)) {
            return ['error' => "Este estudiante ya tiene un representante con parentesco \"{$relationship}\". Solo puede haber uno."];
        }

        // Verificar si ya existe la relación (para decidir INSERT o UPDATE)
        $stmtCheck = $this->db->prepare(
            "SELECT COUNT(*) FROM representatives
             WHERE representative_id = :rep_id AND student_id = :stu_id"
        );
        $stmtCheck->execute([':rep_id' => $representativeId, ':stu_id' => $studentId]);
        $exists = $stmtCheck->fetchColumn() > 0;

        if ($exists) {
            $sql = "UPDATE representatives SET relationship = :relationship, is_primary = :is_primary
                    WHERE representative_id = :rep_id AND student_id = :stu_id";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                ':relationship' => $relationship,
                ':is_primary'   => $isPrimary,
                ':rep_id'       => $representativeId,
                ':stu_id'       => $studentId
            ]);
        } else {
            $sql = "INSERT INTO representatives (representative_id, student_id, relationship, is_primary)
                    VALUES (:rep_id, :stu_id, :relationship, :is_primary)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                ':rep_id'       => $representativeId,
                ':stu_id'       => $studentId,
                ':relationship' => $relationship,
                ':is_primary'   => $isPrimary
            ]);
        }

        return $ok ? true : ['error' => 'Error al guardar la relación.'];
    }

    /**
     * Cambia el tipo (Principal <-> Secundario) de una relación específica.
     * Si se marca como Principal, el otro representante del mismo estudiante pasa a Secundario.
     */
    public function togglePrimary($representativeId, $studentId) {
        // Obtener estado actual
        $stmt = $this->db->prepare(
            "SELECT is_primary FROM representatives
             WHERE representative_id = :rep_id AND student_id = :stu_id"
        );
        $stmt->execute([':rep_id' => $representativeId, ':stu_id' => $studentId]);
        $current = $stmt->fetchColumn();

        if ($current === false) return false;

        $newValue = $current ? 0 : 1;

        // Si se va a marcar como Principal, quitar Principal a los demás del mismo estudiante
        if ($newValue == 1) {
            $stmtReset = $this->db->prepare(
                "UPDATE representatives SET is_primary = 0
                 WHERE student_id = :stu_id AND representative_id != :rep_id"
            );
            $stmtReset->execute([':stu_id' => $studentId, ':rep_id' => $representativeId]);
        }

        $stmtUpdate = $this->db->prepare(
            "UPDATE representatives SET is_primary = :val
             WHERE representative_id = :rep_id AND student_id = :stu_id"
        );
        return $stmtUpdate->execute([
            ':val'    => $newValue,
            ':rep_id' => $representativeId,
            ':stu_id' => $studentId
        ]);
    }

    public function getStudentsByRepresentative($representativeId) {
        $sql = "SELECT u.*, r.relationship, r.is_primary, c.name as course_name, sh.name as shift_name
                FROM users u
                INNER JOIN representatives r ON u.id = r.student_id
                LEFT JOIN course_students cs ON u.id = cs.student_id
                LEFT JOIN courses c ON cs.course_id = c.id
                LEFT JOIN shifts sh ON c.shift_id = sh.id
                WHERE r.representative_id = :representative_id
                ORDER BY r.is_primary DESC, u.last_name, u.first_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':representative_id' => $representativeId]);
        return $stmt->fetchAll();
    }

    public function getRepresentativesByStudent($studentId) {
        $sql = "SELECT u.*, r.relationship, r.is_primary
                FROM users u
                INNER JOIN representatives r ON u.id = r.representative_id
                WHERE r.student_id = :student_id
                ORDER BY r.is_primary DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function removeStudent($representativeId, $studentId) {
        $sql = "DELETE FROM representatives 
                WHERE representative_id = :representative_id AND student_id = :student_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':representative_id' => $representativeId,
            ':student_id' => $studentId
        ]);
    }
}