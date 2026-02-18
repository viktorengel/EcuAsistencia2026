<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Notification.php';

class NotificationController {
    private $notificationModel;

    public function __construct() {
        Security::requireLogin();
        $db = new Database();
        $this->notificationModel = new Notification($db);
    }

    // Vista principal: todas las notificaciones del usuario
    public function index() {
        $userId  = $_SESSION['user_id'];
        $perPage = 15;
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $offset  = ($page - 1) * $perPage;

        $notifications = $this->notificationModel->getByUser($userId, $perPage, $offset);
        $total         = $this->notificationModel->countByUser($userId);
        $totalPages    = (int)ceil($total / $perPage);
        $unreadCount   = $this->notificationModel->getUnreadCount($userId);

        include BASE_PATH . '/views/notifications/index.php';
    }

    // AJAX: marcar una como leída y devolver nuevo conteo
    public function markRead() {
        $userId = $_SESSION['user_id'];
        $id     = (int)($_GET['id'] ?? 0);

        if ($id) {
            $this->notificationModel->markAsRead($id, $userId);
        }

        // Responder JSON para el dropdown del navbar
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'unread'  => $this->notificationModel->getUnreadCount($userId)
        ]);
        exit;
    }

    // Marcar todas como leídas (POST desde vista)
    public function markAllRead() {
        $userId = $_SESSION['user_id'];
        $this->notificationModel->markAllAsRead($userId);
        header('Location: ?action=notifications&success=read');
        exit;
    }

    // Eliminar una notificación (POST desde vista)
    public function delete() {
        $userId = $_SESSION['user_id'];
        $id     = (int)($_POST['notification_id'] ?? 0);

        if ($id) {
            $this->notificationModel->delete($id, $userId);
        }

        header('Location: ?action=notifications&success=deleted');
        exit;
    }

    // Eliminar todas las leídas
    public function deleteRead() {
        $userId = $_SESSION['user_id'];
        $this->notificationModel->deleteRead($userId);
        header('Location: ?action=notifications&success=cleaned');
        exit;
    }

    // AJAX: obtener no leídas para el dropdown del navbar
    public function getUnread() {
        $userId        = $_SESSION['user_id'];
        $notifications = $this->notificationModel->getUnread($userId, 8);
        $count         = $this->notificationModel->getUnreadCount($userId);

        header('Content-Type: application/json');
        echo json_encode([
            'notifications' => $notifications,
            'count'         => $count
        ]);
        exit;
    }
}