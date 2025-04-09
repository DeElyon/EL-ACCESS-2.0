<?php
// Database connection configuration
require_once '../config/database.php';

class UserManager {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new user
    public function createUser($username, $password, $email, $role) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (username, password, email, role) 
                  VALUES (:username, :password, :email, :role)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // Read user(s)
    public function getUsers() {
        $query = "SELECT id, username, email, role FROM users";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get single user by ID
    public function getUserById($id) {
        $query = "SELECT id, username, email, role FROM users WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Update user
    public function updateUser($id, $username, $email, $role) {
        $query = "UPDATE users 
                  SET username = :username, 
                      email = :email, 
                      role = :role 
                  WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // Update password
    public function updatePassword($id, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':password', $hashedPassword);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // Delete user
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $userManager = new UserManager($db);
    
    $data = json_decode(file_get_contents("php://input"), true);
    $response = array();

    switch($data['action']) {
        case 'create':
            if($userManager->createUser($data['username'], $data['password'], $data['email'], $data['role'])) {
                $response = ['status' => 'success', 'message' => 'User created successfully'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to create user'];
            }
            break;

        case 'update':
            if($userManager->updateUser($data['id'], $data['username'], $data['email'], $data['role'])) {
                $response = ['status' => 'success', 'message' => 'User updated successfully'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to update user'];
            }
            break;

        case 'delete':
            if($userManager->deleteUser($data['id'])) {
                $response = ['status' => 'success', 'message' => 'User deleted successfully'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to delete user'];
            }
            break;

        default:
            $response = ['status' => 'error', 'message' => 'Invalid action'];
    }

    echo json_encode($response);
}

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $database = new Database();
    $db = $database->getConnection();
    $userManager = new UserManager($db);

    if (isset($_GET['id'])) {
        $user = $userManager->getUserById($_GET['id']);
        echo json_encode($user);
    } else {
        $users = $userManager->getUsers();
        echo json_encode($users);
    }
}
?>