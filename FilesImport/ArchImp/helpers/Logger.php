<?php
class Logger {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function log($action, $entityType = null, $entityId = null, $description = null) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $sql = "INSERT INTO activity_logs 
                (user_id, action, entity_type, entity_id, description, ip_address, user_agent) 
                VALUES (:user_id, :action, :entity_type, :entity_id, :description, :ip_address, :user_agent)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':action' => $action,
            ':entity_type' => $entityType,
            ':entity_id' => $entityId,
            ':description' => $description,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    public function getLogs($limit = 100, $filters = []) {
        $sql = "SELECT l.*, CONCAT(u.last_name, ' ', u.first_name) as user_name
                FROM activity_logs l
                INNER JOIN users u ON l.user_id = u.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND l.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $sql .= " AND l.action = :action";
            $params[':action'] = $filters['action'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND l.created_at >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND l.created_at <= :end_date";
            $params[':end_date'] = $filters['end_date'] . ' 23:59:59';
        }

        $sql .= " ORDER BY l.created_at DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}