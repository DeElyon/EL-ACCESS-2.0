<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');

// Ensure only admin access
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'platform_name' => filter_input(INPUT_POST, 'platform_name', FILTER_SANITIZE_STRING),
        'logo_url' => filter_input(INPUT_POST, 'logo_url', FILTER_SANITIZE_URL),
        'primary_color' => filter_input(INPUT_POST, 'primary_color', FILTER_SANITIZE_STRING),
        'api_key' => filter_input(INPUT_POST, 'api_key', FILTER_SANITIZE_STRING),
        'subscription_period' => filter_input(INPUT_POST, 'subscription_period', FILTER_VALIDATE_INT),
        'subscription_price' => filter_input(INPUT_POST, 'subscription_price', FILTER_VALIDATE_FLOAT),
        'contact_email' => filter_input(INPUT_POST, 'contact_email', FILTER_VALIDATE_EMAIL)
    ];

    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) 
                              VALUES (?, ?) 
                              ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    
    $message = "Settings updated successfully!";
}

// Fetch current settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$current_settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Platform Settings</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="container">
        <h1>Platform Settings</h1>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <h2>Branding</h2>
            <div class="form-group">
                <label>Platform Name:</label>
                <input type="text" name="platform_name" value="<?php echo htmlspecialchars($current_settings['platform_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Logo URL:</label>
                <input type="url" name="logo_url" value="<?php echo htmlspecialchars($current_settings['logo_url'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Primary Color:</label>
                <input type="color" name="primary_color" value="<?php echo htmlspecialchars($current_settings['primary_color'] ?? '#000000'); ?>">
            </div>

            <h2>API Configuration</h2>
            <div class="form-group">
                <label>API Key:</label>
                <input type="password" name="api_key" value="<?php echo htmlspecialchars($current_settings['api_key'] ?? ''); ?>">
            </div>

            <h2>Subscription Settings</h2>
            <div class="form-group">
                <label>Subscription Period (days):</label>
                <input type="number" name="subscription_period" value="<?php echo htmlspecialchars($current_settings['subscription_period'] ?? '30'); ?>" required>
            </div>
            <div class="form-group">
                <label>Subscription Price:</label>
                <input type="number" step="0.01" name="subscription_price" value="<?php echo htmlspecialchars($current_settings['subscription_price'] ?? '0.00'); ?>" required>
            </div>

            <h2>Contact Information</h2>
            <div class="form-group">
                <label>Contact Email:</label>
                <input type="email" name="contact_email" value="<?php echo htmlspecialchars($current_settings['contact_email'] ?? ''); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</body>
</html>