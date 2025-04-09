<?php
session_start();
require_once('../config/db_connect.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $title = trim($_POST['title']);
                $description = trim($_POST['description']);
                $stmt = $conn->prepare("INSERT INTO courses (title, description) VALUES (?, ?)");
                $stmt->bind_param("ss", $title, $description);
                $stmt->execute();
                break;

            case 'update':
                $id = $_POST['course_id'];
                $title = trim($_POST['title']);
                $description = trim($_POST['description']);
                $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ? WHERE id = ?");
                $stmt->bind_param("ssi", $title, $description, $id);
                $stmt->execute();
                break;

            case 'delete':
                $id = $_POST['course_id'];
                $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
    }
}

// Fetch all courses
$result = $conn->query("SELECT * FROM courses ORDER BY title");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Management</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <h1>Course Management</h1>
    
    <!-- Add New Course Form -->
    <div class="form-section">
        <h2>Add New Course</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <input type="text" name="title" placeholder="Course Title" required>
            <textarea name="description" placeholder="Course Description" required></textarea>
            <button type="submit">Add Course</button>
        </form>
    </div>

    <!-- List of Courses -->
    <div class="courses-list">
        <h2>Existing Courses</h2>
        <?php while ($course = $result->fetch_assoc()): ?>
            <div class="course-item">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                    <input type="text" name="title" value="<?php echo htmlspecialchars($course['title']); ?>">
                    <textarea name="description"><?php echo htmlspecialchars($course['description']); ?></textarea>
                    <button type="submit">Update</button>
                </form>
                <form method="POST" class="delete-form">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this course?')">Delete</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>