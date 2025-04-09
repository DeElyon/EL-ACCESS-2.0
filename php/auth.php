<?php
session_start();

class Auth {
    private $db;
    
    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function signup($email, $password, $name, $subscriptionMonths = 1) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $startDate = date('Y-m-d H:i:s');
        $expiryDate = date('Y-m-d H:i:s', strtotime("+{$subscriptionMonths} months"));
        
        try {
            $this->db->beginTransaction();
            
            // Insert user
            $stmt = $this->db->prepare("INSERT INTO users (email, password, name, subscription_start, subscription_expiry) VALUES (?, ?, ?, ?, ?)");
            $success = $stmt->execute([$email, $hashedPassword, $name, $startDate, $expiryDate]);
            
            if ($success) {
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            return false;
            
        } catch(PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function renewSubscription($userId, $months = 1) {
        try {
            $currentExpiry = $this->db->prepare("SELECT subscription_expiry FROM users WHERE id = ?");
            $currentExpiry->execute([$userId]);
            $result = $currentExpiry->fetch();
            
            $newExpiry = date('Y-m-d H:i:s', strtotime($result['subscription_expiry'] . " +{$months} months"));
            
            $stmt = $this->db->prepare("UPDATE users SET subscription_expiry = ? WHERE id = ?");
            return $stmt->execute([$newExpiry, $userId]);
        } catch(PDOException $e) {
            return false;
        }
    }

    // ... rest of the existing methods ...
}
