<?php
session_start();
require_once 'config/database.php';

class UserProfile {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getUserProfile($userId) {
        $query = "SELECT name, email, avatar FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function updateProfile($userId, $name, $email, $avatar = null) {
        $query = "UPDATE users SET name = ?, email = ?";
        $params = [$name, $email];
        
        if ($avatar) {
            $query .= ", avatar = ?";
            $params[] = $avatar;
        }
        
        $query .= " WHERE id = ?";
        $params[] = $userId;
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(str_repeat("s", count($params) - 1) . "i", ...$params);
        return $stmt->execute();
    }
}

// Usage example
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $profile = new UserProfile($db);
    
    if (isset($_POST['update_profile'])) {
        $userId = $_SESSION['user_id'] ?? 0;
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        // Handle avatar upload
        $avatar = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
            $avatar = file_get_contents($_FILES['avatar']['tmp_name']);
        }
        
        if ($profile->updateProfile($userId, $name, $email, $avatar)) {
            echo "Profile updated successfully";
        } else {
            echo "Error updating profile";
        }
    }
    
    $db->close();
}