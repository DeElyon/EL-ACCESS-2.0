<?php
session_start();
require_once 'config.php';
require_once 'db_connect.php';

// Define constants
define('UPLOAD_DIR', '../uploads/proofs/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'application/pdf']);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate file upload
        if (!isset($_FILES['proof_file']) || $_FILES['proof_file']['error'] !== UPLOAD_ERROR_OK) {
            throw new Exception('File upload failed');
        }

        $file = $_FILES['proof_file'];
        
        // Validate file size
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('File size exceeds limit of 5MB');
        }

        // Validate file type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $fileType = $finfo->file($file['tmp_name']);
        if (!in_array($fileType, ALLOWED_TYPES)) {
            throw new Exception('Invalid file type. Only JPG, PNG and PDF allowed');
        }

        // Generate unique filename
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid('proof_') . '_' . time() . '.' . $fileExtension;
        $uploadPath = UPLOAD_DIR . $newFilename;

        // Create upload directory if it doesn't exist
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        // Move file to upload directory
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to save file');
        }

        // Save to database
        $userId = $_SESSION['user_id'];
        $stmt = $pdo->prepare("INSERT INTO payment_proofs (user_id, filename, upload_date) VALUES (?, ?, NOW())");
        $stmt->execute([$userId, $newFilename]);

        echo json_encode([
            'success' => true,
            'message' => 'Proof of payment uploaded successfully'
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}