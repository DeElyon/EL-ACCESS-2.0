<?php
class Notification {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Create a new notification
    public function create($userId, $type, $message, $isRead = false) {
        $sql = "INSERT INTO notifications (user_id, type, message, is_read, created_at) 
                VALUES (:user_id, :type, :message, :is_read, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'is_read' => $isRead
        ]);
    }

    // Get all notifications for a user
    public function getUserNotifications($userId, $limit = 10) {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mark notification as read
    public function markAsRead($notificationId) {
        $sql = "UPDATE notifications 
                SET is_read = true 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $notificationId]);
    }

    // Get unread notifications count
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) FROM notifications 
                WHERE user_id = :user_id AND is_read = false";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchColumn();
    }
}

// Example usage:
/*
$db = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
$notification = new Notification($db);

// Create notification
$notification->create(1, 'course_update', 'New content available in PHP course');

// Get user notifications
$notifications = $notification->getUserNotifications(1);

// Display notifications
foreach ($notifications as $notif) {
    echo "<div class='notification " . ($notif['is_read'] ? 'read' : 'unread') . "'>";
    echo htmlspecialchars($notif['message']);
    echo "<span class='date'>" . $notif['created_at'] . "</span>";
    echo "</div>";
}
*/
?>