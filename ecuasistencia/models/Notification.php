<?php
class Notification {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    // Crear notificación
    // $link: URL relativa destino, ej: '?action=pending_justifications'
    public function create($userId, $title, $message, $type = 'info', $link = null) {
        $sql = "INSERT INTO notifications (user_id, title, message, type, link, is_read, created_at) 
                VALUES (:user_id, :title, :message, :type, :link, 0, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id'  => $userId,
            ':title'    => $title,
            ':message'  => $message,
            ':type'     => $type,
            ':link'     => $link
        ]);
    }

    // Obtener notificaciones de un usuario (con paginación)
    public function getByUser($userId, $limit = 20, $offset = 0) {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',   $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset',  $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Solo no leídas (para el dropdown del navbar)
    public function getUnread($userId, $limit = 10) {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = :user_id AND is_read = 0
                ORDER BY created_at DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',   $limit,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Contar no leídas
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM notifications 
                WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return (int)$stmt->fetch()['count'];
    }

    // Marcar una como leída
    public function markAsRead($notificationId, $userId) {
        $sql = "UPDATE notifications SET is_read = 1 
                WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $notificationId, ':user_id' => $userId]);
    }

    // Marcar TODAS como leídas
    public function markAllAsRead($userId) {
        $sql = "UPDATE notifications SET is_read = 1 
                WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }

    // Eliminar una notificación
    public function delete($notificationId, $userId) {
        $sql = "DELETE FROM notifications WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $notificationId, ':user_id' => $userId]);
    }

    // Eliminar todas las leídas
    public function deleteRead($userId) {
        $sql = "DELETE FROM notifications WHERE user_id = :user_id AND is_read = 1";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }

    // Total de notificaciones del usuario (para paginación)
    public function countByUser($userId) {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return (int)$stmt->fetch()['count'];
    }
}