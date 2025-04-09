<?php
header('Content-Type: application/json');
require_once 'config/database.php';

function getCourses() {
    try {
        $db = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASSWORD
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT id, title, description, duration, level 
                 FROM courses 
                 WHERE active = 1 
                 ORDER BY title ASC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $courses
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error occurred'
        ]);
    }
}

getCourses();
?>