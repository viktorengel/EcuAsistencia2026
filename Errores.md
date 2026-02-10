Fatal error: Uncaught PDOException: SQLSTATE[HY093]: Invalid parameter number in C:\xampp\htdocs\EcuAsistencia2026\models\Representative.php:15 Stack trace: #0 C:\xampp\htdocs\EcuAsistencia2026\models\Representative.php(15): PDOStatement->execute(Array) #1 C:\xampp\htdocs\EcuAsistencia2026\controllers\RepresentativeController.php(35): Representative->assignStudent(1, 3, 'T\xC3\xADo/a', 1) #2 C:\xampp\htdocs\EcuAsistencia2026\public\index.php(128): RepresentativeController->manageRepresentatives() #3 {main} thrown in C:\xampp\htdocs\EcuAsistencia2026\models\Representative.php on line 15

❌ El problema

En PDO no puedes usar el mismo parámetro dos veces (:relationship, :is_primary) en la misma consulta cuando usas
ON DUPLICATE KEY UPDATE.

Por eso sale el error:

Invalid parameter number

✅ La solución

Usa nombres de parámetros distintos para el INSERT y el UPDATE.

✔️ Código corregido
public function assignStudent($representativeId, $studentId, $relationship, $isPrimary = 0) {
    $sql = "INSERT INTO representatives 
            (representative_id, student_id, relationship, is_primary) 
            VALUES 
            (:rep_id, :stu_id, :rel_ins, :prim_ins)
            ON DUPLICATE KEY UPDATE 
            relationship = :rel_upd,
            is_primary = :prim_upd";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        ':rep_id'   => $representativeId,
        ':stu_id'   => $studentId,
        ':rel_ins'  => $relationship,
        ':prim_ins' => $isPrimary,
        ':rel_upd'  => $relationship,
        ':prim_upd' => $isPrimary
    ]);
}

🧠 Importante

Asegúrate de que la tabla tenga una clave única:

UNIQUE (representative_id, student_id)

🟢 Resumen final

👉 Cambia los nombres de los parámetros duplicados
👉 No repitas :relationship ni :is_primary
👉 Todo lo demás está bien