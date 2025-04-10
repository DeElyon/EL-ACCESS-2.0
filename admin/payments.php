<?php
session_start();
require_once('../includes/db_connect.php');

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Function to verify transaction
function verifyTransaction($transaction_id) {
    // In a real application, you would integrate with Access Bank's API
    // This is a placeholder implementation
    return true;
}

// Handle status updates
if (isset($_POST['update_status'])) {
    $payment_id = filter_var($_POST['payment_id'], FILTER_SANITIZE_NUMBER_INT);
    $new_status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
    
    $stmt = $conn->prepare("UPDATE payments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $payment_id);
    $stmt->execute();
}

// Fetch payments
$query = "SELECT p.*, u.email FROM payments p 
          LEFT JOIN users u ON p.user_id = u.id 
          ORDER BY p.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Management - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="container">
        <h2>Payment Records</h2>
        <div class="account-info">
            <p>Account Details: Access Bank</p>
            <p>Account Number: 1907856695</p>
            <p>Account Name: EBUBECHUKWU IFEANYI ELIJAH</p>
        </div>
        
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['transaction_id']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>₦<?= number_format($row['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="payment_id" value="<?= $row['id'] ?>">
                            <select name="status">
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="failed">Failed</option>
                            </select>
                            <button type="submit" name="update_status">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Add confirmation for status updates
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!confirm('Are you sure you want to update this payment status?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>