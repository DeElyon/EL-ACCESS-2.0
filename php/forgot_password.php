<?php
require_once 'config.php';
require_once 'db_connection.php';
session_start();

function generateResetToken() {
    return bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        // Step 1: Generate and send reset link
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($user = $stmt->fetch()) {
            $token = generateResetToken();
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expiry) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expiry]);
            
            $resetLink = "https://yourdomain.com/reset_password.php?token=" . $token;
            
            // Send email (implement your email sending logic here)
            $to = $email;
            $subject = "Password Reset Request";
            $message = "Click the following link to reset your password: $resetLink";
            mail($to, $subject, $message);
            
            echo "Password reset instructions have been sent to your email.";
        } else {
            echo "If the email exists in our system, you will receive reset instructions.";
        }
    } elseif (isset($_POST['token']) && isset($_POST['new_password'])) {
        // Step 2: Process password reset
        $token = filter_var($_POST['token'], FILTER_SANITIZE_STRING);
        $newPassword = $_POST['new_password'];
        
        $stmt = $pdo->prepare("SELECT user_id FROM password_resets 
                              WHERE token = ? AND expiry > NOW() AND used = 0");
        $stmt->execute([$token]);
        
        if ($reset = $stmt->fetch()) {
            if (strlen($newPassword) >= 8) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $reset['user_id']]);
                
                $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
                $stmt->execute([$token]);
                
                echo "Password has been successfully reset.";
            } else {
                echo "Password must be at least 8 characters long.";
            }
        } else {
            echo "Invalid or expired reset token.";
        }
    }
} else {
    // Display the password reset form
    ?>
    <form method="post">
        <h2>Reset Password</h2>
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
    </form>
    <?php
}
?>