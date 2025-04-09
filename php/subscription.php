user_id INT NOT NULL,

<?php

class SubscriptionManager {
    private $db; // Database connection would be injected

    public function __construct($database) {
        $this->db = $database;
    }

    // Activate a new subscription
    public function activateSubscription($userId) {
        $startDate = date('Y-m-d H:i:s');
        $expiryDate = date('Y-m-d H:i:s', strtotime('+14 days'));
        
        $sql = "INSERT INTO subscriptions (user_id, start_date, expiry_date, status) 
                VALUES (?, ?, ?, 'active')";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $startDate, $expiryDate]);
            return true;
        } catch (Exception $e) {
            error_log("Subscription activation failed: " . $e->getMessage());
            return false;
        }
    }

    // Check subscription status
    public function checkSubscriptionStatus($userId) {
        $sql = "SELECT * FROM subscriptions 
                WHERE user_id = ? 
                AND status = 'active' 
                ORDER BY expiry_date DESC 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    // Check for expiring subscriptions and send notifications
    public function checkExpiringSubscriptions() {
        $warningDate = date('Y-m-d H:i:s', strtotime('+3 days'));
        
        $sql = "SELECT s.*, u.email 
                FROM subscriptions s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.status = 'active' 
                AND s.expiry_date <= ?
                AND s.notification_sent = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$warningDate]);
        $expiringSubscriptions = $stmt->fetchAll();

        foreach ($expiringSubscriptions as $subscription) {
            $this->sendExpirationNotification($subscription['email'], $subscription['expiry_date']);
            $this->markNotificationSent($subscription['id']);
        }
    }

    private function sendExpirationNotification($email, $expiryDate) {
        // Email notification logic here
        $subject = "Your subscription is expiring soon";
        $message = "Your subscription will expire on " . date('Y-m-d', strtotime($expiryDate));
        mail($email, $subject, $message);
    }

    private function markNotificationSent($subscriptionId) {
        $sql = "UPDATE subscriptions SET notification_sent = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$subscriptionId]);
    }
}

// Required database table structure:
/*
CREATE TABLE subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    start_date DATETIME NOT NULL,
    expiry_date DATETIME NOT NULL,
    status ENUM('active', 'expired') NOT NULL,
    notification_sent TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/