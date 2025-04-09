<?php
class ProgressTracking {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Log lesson completion
    public function logLessonProgress($userId, $lessonId, $status, $completedAt = null) {
        $completedAt = $completedAt ?? date('Y-m-d H:i:s');
        $sql = "INSERT INTO lesson_progress (user_id, lesson_id, status, completed_at) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE status = ?, completed_at = ?";
        
        return $this->db->execute($sql, [$userId, $lessonId, $status, $completedAt, $status, $completedAt]);
    }

    // Log quiz attempt and score
    public function logQuizProgress($userId, $quizId, $score, $attemptedAt = null) {
        $attemptedAt = $attemptedAt ?? date('Y-m-d H:i:s');
        $sql = "INSERT INTO quiz_progress (user_id, quiz_id, score, attempted_at) 
                VALUES (?, ?, ?, ?)";
        
        return $this->db->execute($sql, [$userId, $quizId, $score, $attemptedAt]);
    }

    // Get user progress summary
    public function getUserProgress($userId) {
        $sql = "SELECT 
                    COUNT(DISTINCT l.lesson_id) as completed_lessons,
                    (SELECT COUNT(*) FROM lessons) as total_lessons,
                    AVG(q.score) as average_quiz_score
                FROM lesson_progress l
                LEFT JOIN quiz_progress q ON q.user_id = l.user_id
                WHERE l.user_id = ? AND l.status = 'completed'";
        
        return $this->db->query($sql, [$userId])->fetch();
    }

    // Get detailed lesson progress
    public function getLessonProgress($userId) {
        $sql = "SELECT lesson_id, status, completed_at 
                FROM lesson_progress 
                WHERE user_id = ? 
                ORDER BY completed_at DESC";
        
        return $this->db->query($sql, [$userId])->fetchAll();
    }

    // Get quiz history
    public function getQuizHistory($userId) {
        $sql = "SELECT quiz_id, score, attempted_at 
                FROM quiz_progress 
                WHERE user_id = ? 
                ORDER BY attempted_at DESC";
        
        return $this->db->query($sql, [$userId])->fetchAll();
    }
}