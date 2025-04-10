<?php
require_once('../config/database.php');
require_once('../includes/auth.php');

// Ensure only admin access
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

class ReportGenerator {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getPaymentReport($startDate = null, $endDate = null) {
        $query = "SELECT 
                    p.payment_date,
                    u.username,
                    p.amount,
                    p.status
                  FROM payments p
                  JOIN users u ON p.user_id = u.id
                  WHERE 1=1";
        
        if ($startDate) {
            $query .= " AND p.payment_date >= :startDate";
        }
        if ($endDate) {
            $query .= " AND p.payment_date <= :endDate";
        }
        
        return $this->db->query($query);
    }

    public function getUserEngagementReport() {
        $query = "SELECT 
                    u.username,
                    COUNT(l.id) as login_count,
                    MAX(l.login_time) as last_login
                  FROM users u
                  LEFT JOIN login_logs l ON u.id = l.user_id
                  GROUP BY u.id
                  ORDER BY login_count DESC";
        
        return $this->db->query($query);
    }

    public function getSubscriptionStatusReport() {
        $query = "SELECT 
                    s.status,
                    COUNT(*) as total,
                    s.expiry_date
                  FROM subscriptions s
                  GROUP BY s.status";
        
        return $this->db->query($query);
    }
}

// Initialize report generator
$reports = new ReportGenerator($db);

// Handle form submission
$reportType = $_GET['type'] ?? 'payments';
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reports</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <h1>Administrative Reports</h1>
        
        <div class="report-filters">
            <form method="GET">
                <select name="type">
                    <option value="payments" <?php echo $reportType == 'payments' ? 'selected' : ''; ?>>Payment Activity</option>
                    <option value="engagement" <?php echo $reportType == 'engagement' ? 'selected' : ''; ?>>User Engagement</option>
                    <option value="subscriptions" <?php echo $reportType == 'subscriptions' ? 'selected' : ''; ?>>Subscription Status</option>
                </select>
                <input type="date" name="start_date" value="<?php echo $startDate; ?>">
                <input type="date" name="end_date" value="<?php echo $endDate; ?>">
                <button type="submit">Generate Report</button>
            </form>
        </div>

        <div class="report-results">
            <?php
            switch($reportType) {
                case 'payments':
                    $data = $reports->getPaymentReport($startDate, $endDate);
                    include('report-templates/payments.php');
                    break;
                case 'engagement':
                    $data = $reports->getUserEngagementReport();
                    include('report-templates/engagement.php');
                    break;
                case 'subscriptions':
                    $data = $reports->getSubscriptionStatusReport();
                    include('report-templates/subscriptions.php');
                    break;
            }
            ?>
        </div>
    </div>
</body>
</html>