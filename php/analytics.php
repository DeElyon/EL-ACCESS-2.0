<?php
require_once 'config.php';
require_once 'auth.php';

// Ensure only admin users can access this page
if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

class Analytics {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getCourseEngagementStats() {
        $query = "SELECT 
                    courses.title,
                    COUNT(DISTINCT enrollments.user_id) as total_students,
                    AVG(progress.completion_percentage) as avg_completion,
                    COUNT(DISTINCT completed_courses.user_id) as completions
                  FROM courses
                  LEFT JOIN enrollments ON courses.id = enrollments.course_id
                  LEFT JOIN progress ON enrollments.id = progress.enrollment_id
                  LEFT JOIN completed_courses ON courses.id = completed_courses.course_id
                  GROUP BY courses.id";
        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getPaymentAnalytics() {
        $query = "SELECT 
                    SUM(amount) as total_revenue,
                    COUNT(*) as total_transactions,
                    AVG(amount) as avg_transaction,
                    DATE_FORMAT(payment_date, '%Y-%m') as month
                  FROM payments
                  WHERE payment_status = 'completed'
                  GROUP BY month
                  ORDER BY month DESC
                  LIMIT 12";
        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserActivityMetrics() {
        $query = "SELECT 
                    COUNT(DISTINCT user_id) as active_users,
                    DATE_FORMAT(login_time, '%Y-%m-%d') as date
                  FROM user_activity_logs
                  WHERE login_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY date
                  ORDER BY date DESC";
        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}

// Initialize analytics
$analytics = new Analytics($db);

// Fetch analytics data
$courseStats = $analytics->getCourseEngagementStats();
$paymentStats = $analytics->getPaymentAnalytics();
$userActivity = $analytics->getUserActivityMetrics();

// Convert data to JSON for JavaScript charts
$courseStatsJson = json_encode($courseStats);
$paymentStatsJson = json_encode($paymentStats);
$userActivityJson = json_encode($userActivity);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Analytics Dashboard</h1>
    
    <div class="analytics-container">
        <div class="chart-container">
            <h2>Course Engagement</h2>
            <canvas id="courseEngagementChart"></canvas>
        </div>
        
        <div class="chart-container">
            <h2>Revenue Analysis</h2>
            <canvas id="revenueChart"></canvas>
        </div>
        
        <div class="chart-container">
            <h2>User Activity</h2>
            <canvas id="userActivityChart"></canvas>
        </div>
    </div>

    <script>
        // Initialize charts using the PHP data
        const courseData = <?php echo $courseStatsJson; ?>;
        const paymentData = <?php echo $paymentStatsJson; ?>;
        const activityData = <?php echo $userActivityJson; ?>;

        // Create charts using Chart.js
        // Course Engagement Chart
        new Chart(document.getElementById('courseEngagementChart'), {
            type: 'bar',
            data: {
                labels: courseData.map(item => item.title),
                datasets: [{
                    label: 'Total Students',
                    data: courseData.map(item => item.total_students)
                }]
            }
        });

        // Revenue Chart
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: paymentData.map(item => item.month),
                datasets: [{
                    label: 'Monthly Revenue',
                    data: paymentData.map(item => item.total_revenue)
                }]
            }
        });

        // User Activity Chart
        new Chart(document.getElementById('userActivityChart'), {
            type: 'line',
            data: {
                labels: activityData.map(item => item.date),
                datasets: [{
                    label: 'Active Users',
                    data: activityData.map(item => item.active_users)
                }]
            }
        });
    </script>
</body>
</html>